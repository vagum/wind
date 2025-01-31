<template>
    <div :class="[bgClass, 'p-4 rounded-lg shadow-sm']">
        <div class="flex justify-between items-center mb-2">
            <span class="font-medium text-gray-800">
                {{ comment.profile && comment.profile.name ? comment.profile.name : 'Аноним' }}
            </span>
            <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
        </div>
        <p class="text-gray-700 mb-2">{{ comment.content }}</p>

        <!-- Действия: Лайк и Ответ -->
        <div class="flex items-center space-x-4">
            <button @click="toggleLike" class="flex items-center text-gray-700 focus:outline-none">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    :fill="comment.is_liked ? 'red' : 'none'"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5 mr-1"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
                    />
                </svg>
                <span>{{ comment.likes }}</span>
            </button>
            <button @click="toggleReply" class="text-gray-700 hover:underline focus:outline-none">
                Reply
            </button>
        </div>

        <!-- Форма ответа -->
        <div v-if="showReply" class="mt-4">
            <textarea
                v-model="reply.content"
                :class="[
                    'w-full border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                    errors[`reply.content`]
                        ? 'border-red-500 focus:ring-red-500'
                        : 'border-gray-300 focus:ring-blue-500'
                ]"
                placeholder="Your Answer"></textarea>
            <p v-if="errors[`reply.content`]" class="text-red-500 text-sm">
                <span v-for="(error, index) in errors['reply.content']" :key="index">
                    {{ error }}<br/>
                </span>
            </p>
            <button
                @click.prevent="submitReply"
                class="mt-2 px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Save
            </button>
        </div>

        <!-- Вложенные комментарии -->
        <div
            v-if="comment.replies && comment.replies.length"
            class="mt-4 ml-6 border-l-2 border-gray-200 pl-4 space-y-4"
        >
            <CommentItem
                v-for="reply in comment.replies"
                :key="reply.id"
                :comment="reply"
                :auth="auth"
                :depth="depth + 1"
                @comment-updated="handleCommentUpdated"
                @reply-added="$emit('reply-added', $event)"
            />
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: "CommentItem",
    props: {
        comment: Object,
        auth: Object,
        depth: {
            type: Number,
            default: 1
        }
    },
    data() {
        return {
            showReply: false,
            reply: {
                content: '',
            },
            errors: {},
        }
    },
    computed: {
        bgClass() {
            return this.depth % 2 === 0 ? 'bg-white' : 'bg-blue-50';
        }
    },
    methods: {
        formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleString('ru-RU', options);
        },
        toggleLike() {
            // Проверяем, аутентифицирован ли пользователь
            if (!this.auth.user) {
                window.location.href = '/login';
                return;
            }

            const originalIsLiked = this.comment.is_liked;
            const originalLikes = this.comment.likes;

            // Оптимистичное обновление
            this.comment.is_liked = !this.comment.is_liked;
            this.comment.likes = Number(this.comment.likes) + (this.comment.is_liked ? 1 : -1);

            axios.post(route("comments.likes.toggle", this.comment.id))
                .then((res) => {
                    this.comment.is_liked = res.data.is_liked;
                    this.comment.likes = Number(res.data.likes) || 0;
                    // Эмитируем событие, чтобы родитель обновил данные, если нужно
                    this.$emit('comment-updated', this.comment);
                })
                .catch((error) => {
                    console.error('Ошибка при переключении лайка:', error);
                    // Откат
                    this.comment.is_liked = originalIsLiked;
                    this.comment.likes = originalLikes;
                });
        },
        toggleReply() {
            // Проверяем, аутентифицирован ли пользователь
            if (!this.auth.user) {
                window.location.href = '/login';
                return;
            }
            this.showReply = !this.showReply;
        },
        submitReply() {
            if (!this.reply.content.trim()) {
                this.errors = {
                    'reply.content': ['Ответ не может быть пустым.']
                };
                return;
            }

            axios.post(route('comments.replies.store', this.comment.id), {
                content: this.reply.content,
            })
                .then(response => {
                    const newReply = response.data;
                    newReply.replies = [];
                    newReply.likes = Number(newReply.likes) || 0;
                    newReply.is_liked = newReply.is_liked === true;

                    // Добавляем ответ к текущему комментарию
                    if (!this.comment.replies) {
                        this.$set(this.comment, 'replies', []);
                    }
                    this.comment.replies.push(newReply);

                    // Очищаем форму
                    this.reply.content = '';
                    this.errors = {};
                    this.showReply = false;

                    // Сообщаем родителю, что добавлен новый ответ
                    // Передаём туда комментарий-ответ и общее кол-во комментариев (comments_count)
                    this.$emit('reply-added', {
                        reply: newReply,
                        comments_count: newReply.comments_count,
                    });

                    // Если нужно, чтобы родитель ещё и знал о лайках и т.д., мы можем вызвать:
                    this.$emit('comment-updated', this.comment);
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.errors = {
                            'general': ['Произошла ошибка при добавлении ответа.']
                        };
                    }
                });
        },
        handleCommentUpdated(updatedComment) {
            // Пробрасываем событие наверх, чтобы цепочка не рвалась и
            // родитель (Show.vue) знал об обновлениях (например, лайках).
            this.$emit('comment-updated', updatedComment);
        }
    }
}
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
