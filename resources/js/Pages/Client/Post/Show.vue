<template>
    <!-- Ссылка Назад -->
    <div class="mb-2">
        <Link :href="route('clients.posts.index')" class="text-blue-500 hover:underline">&laquo; Back</Link>
    </div>

    <!-- Заголовок Поста -->
    <div class="mb-4 text-2xl font-semibold text-gray-800 break-words">
        {{ post.title }}
    </div>

    <!-- Изображение Поста -->
    <div v-if="post.image_url" class="w-full md:w-1/2 mb-4">
        <img :src="post.image_url" :alt="post.title" class="rounded-lg shadow-md w-full"/>
    </div>

    <!-- Содержимое Поста -->
    <div class="text-gray-700 mb-6 break-words">
        {{ post.content }}
    </div>

    <div class="w-1/2">
        <!-- Лайки и действия -->
        <div class="flex items-center justify-between">
            <button @click="toggleLike" class="flex items-center text-gray-700 focus:outline-none">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    :fill="post.is_liked ? 'red' : 'none'"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6 mr-1"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
                    />
                </svg>
                <span>{{ post.likes }}</span>
            </button>

            <!-- Комментарии -->
            <div class="flex items-center text-gray-700">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    :fill="localCommentsCount ? '#D1D1D1' : 'none'"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6 mr-1"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"
                    />
                </svg>
                <span>{{ localCommentsCount }}</span>
            </div>
        </div>
    </div>

    <!-- Секция комментариев -->
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4">Comments</h3>

        <!-- Форма добавления комментария -->
        <div v-if="auth.user" class="mb-6">
            <h4 class="text-lg font-medium mb-2">Add Comment</h4>
            <div class="flex flex-col items-start space-y-3">
                <textarea
                    v-model="comment.content"
                    :class="[
                        'w-full md:w-1/2 border rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2',
                        errors[`comment.content`]
                            ? 'border-red-500 focus:ring-red-500'
                            : 'border-gray-300 focus:ring-blue-500'
                    ]"
                    placeholder="Your Comment"></textarea>
                <p v-if="errors[`comment.content`]" class="text-red-500 text-sm">
                    <span v-for="(error, index) in errors['comment.content']" :key="index">
                        {{ error }}<br/>
                    </span>
                </p>
                <button
                    @click.prevent="storeComment"
                    class="w-full md:w-1/2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Save Comment
                </button>
            </div>
        </div>

        <!-- Список комментариев -->
        <div v-if="nestedComments && nestedComments.length" class="w-full md:w-1/2 space-y-4">
            <!-- Обратите внимание: здесь добавлено событие @reply-added -->
            <CommentItem
                v-for="comment in nestedComments"
                :key="comment.id"
                :comment="comment"
                :auth="auth"
                @comment-updated="updateComment"
                @reply-added="handleReplyAdded"
            />
        </div>
        <div v-else class="text-gray-500">No Comments</div>
    </div>
</template>

<script>
import ClientLayout from "@/Layouts/Client/ClientLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';
import CommentItem from '@/Components/CommentItem.vue'; // Импортируем компонент CommentItem

export default {
    name: "Show",
    layout: ClientLayout,

    components: {
        Link,
        CommentItem,
    },

    props: {
        post: Object,
        auth: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            comment: {
                content: '',
            },
            errors: {}, // Для хранения ошибок
            localCommentsCount: this.post.comments_count, // Локальная копия количества комментариев
            localComments: [], // Локальная копия комментариев
            nestedComments: [], // Иерархическая структура комментариев
        }
    },

    mounted() {
        // Инициализируем локальную копию комментариев при монтировании
        this.localComments = this.post.comments && this.post.comments.data
            ? [...this.post.comments.data]
            : [];

        // Преобразуем плоский массив в дерево
        this.nestedComments = this.buildCommentsTree(this.localComments);
    },

    methods: {
        /**
         * Преобразует плоский массив комментариев в дерево.
         * @param {Array} comments - Плоский массив комментариев.
         * @returns {Array} - Иерархическая структура комментариев.
         */
        buildCommentsTree(comments) {
            const map = {};
            const roots = [];

            // Инициализируем карту
            comments.forEach(comment => {
                comment.replies = [];
                comment.likes = Number(comment.likes) || 0;
                comment.is_liked = comment.is_liked === true;
                map[comment.id] = comment;
            });

            // Строим дерево
            comments.forEach(comment => {
                if (comment.parent_id) {
                    if (map[comment.parent_id]) {
                        map[comment.parent_id].replies.push(comment);
                    }
                } else {
                    roots.push(comment);
                }
            });

            return roots;
        },

        storeComment() {
            // Проверяем, что комментарий не пустой
            if (!this.comment.content.trim()) {
                this.errors = {
                    'comment.content': ['Комментарий не может быть пустым.']
                };
                return;
            }

            axios.post(route('posts.comments.store', this.post.id), this.comment)
                .then(response => {
                    const newComment = response.data;
                    newComment.replies = [];
                    newComment.likes = Number(newComment.likes) || 0;
                    newComment.is_liked = newComment.is_liked === true;

                    // Добавляем новый комментарий в начало списка
                    this.nestedComments.unshift(newComment);

                    // Инкрементируем локальный счётчик комментариев
                    // (можно заменить на this.localCommentsCount = newComment.comments_count,
                    // если хотите синхронизироваться с сервером точно)
                    this.localCommentsCount += 1;

                    // Очищаем поле ввода и ошибки
                    this.comment.content = '';
                    this.errors = {};
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.errors = {
                            'general': ['Произошла ошибка при добавлении комментария.']
                        };
                    }
                });
        },

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

            // Сохраняем старые значения для отката в случае ошибки
            const originalIsLiked = this.post.is_liked;
            const originalLikes = this.post.likes;

            // Оптимистичное обновление
            this.post.is_liked = !this.post.is_liked;
            this.post.likes += this.post.is_liked ? 1 : -1;

            // Запрос к серверу
            axios.post(route("posts.likes.toggle", this.post.id))
                .then((res) => {
                    this.post.is_liked = res.data.is_liked;
                    this.post.likes = res.data.likes;
                })
                .catch((error) => {
                    console.error('Ошибка при переключении лайка:', error);
                    // Откат
                    this.post.is_liked = originalIsLiked;
                    this.post.likes = originalLikes;
                });
        },

        updateComment(updatedComment) {
            // Ищем комментарий в иерархии и обновляем
            const updateRecursive = (comments) => {
                for (let i = 0; i < comments.length; i++) {
                    if (comments[i].id === updatedComment.id) {
                        comments[i] = updatedComment;
                        return true;
                    }
                    if (comments[i].replies && comments[i].replies.length) {
                        if (updateRecursive(comments[i].replies)) {
                            return true;
                        }
                    }
                }
                return false;
            };

            updateRecursive(this.nestedComments);
        },

        /**
         * Вызывается, когда в дочернем компоненте (CommentItem) добавляется Reply.
         * @param {Object} payload
         * @param {Object} payload.reply - новый ответ
         * @param {Number} payload.comments_count - актуальное общее число комментариев (с сервера)
         */
        handleReplyAdded({ reply, comments_count }) {
            // Можете либо просто инкрементировать локально:
            // this.localCommentsCount += 1;
            // или же синхронизироваться с сервером:
            this.localCommentsCount = comments_count;
        }
    }
}
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
