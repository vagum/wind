<template>
    <div :class="['p-4 rounded-lg shadow-sm', bgClass]">
        <!-- Заголовок с именем автора и датой -->
        <div class="flex justify-between items-center mb-2">
            <span class="font-medium text-gray-800">
                {{ comment.profile && comment.profile.name ? comment.profile.name : 'Аноним' }}
            </span>
            <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
        </div>

        <!-- Текст комментария ИЛИ поле редактирования -->
        <div v-if="!isEditing">
            <p class="text-gray-700">{{ comment.content }}</p>
        </div>
        <div v-else>
            <textarea
                v-model="editedContent"
                class="w-full border rounded p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            ></textarea>
            <div class="mt-2 flex space-x-2">
                <button
                    @click="updateComment"
                    class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                >
                    Save Comment
                </button>
                <button
                    @click="cancelEditing"
                    class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300"
                >
                    Cancel
                </button>
            </div>
        </div>

        <!-- Действия: Лайк, Ответ, (Редактировать, Удалить - если авторизован и владелец) -->
        <div class="flex items-center space-x-4 mt-3">
            <!-- Кнопка Лайка -->
            <button
                @click="onToggleLike"
                type="button"
                class="flex items-center text-gray-700 focus:outline-none"
            >
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

            <!-- Кнопка "Ответить" (только если есть auth.user) -->
            <button
                v-if="auth.user"
                @click="toggleReply"
                type="button"
                class="text-gray-700 hover:underline focus:outline-none"
            >
                Reply
            </button>

            <!-- Кнопки "Редактировать" и "Удалить" показываем только, если автор комментария = текущий пользователь
                 (или у вас может быть другая логика прав (admin и т.д.)) -->
            <div v-if="auth.user && auth.user.profile.id === comment.profile.id" class="flex items-center space-x-2">
                <button
                    @click="startEditing"
                    class="text-blue-500 hover:underline"
                >
                    Edit
                </button>
                <button
                    @click="deleteThisComment"
                    class="text-red-500 hover:underline"
                >
                    Delete
                </button>
            </div>
        </div>

        <!-- Форма для ответа -->
        <div v-if="showReply" class="mt-4">
            <textarea
                v-model="reply.content"
                :class="[
                    'w-full border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                    errors['reply.content'] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                ]"
                placeholder="Ваш ответ"
            ></textarea>
            <p v-if="errors['reply.content']" class="text-red-500 text-sm">
                <span v-for="(error, index) in errors['reply.content']" :key="index">
                    {{ error }}<br />
                </span>
            </p>
            <button
                @click.prevent="onStoreReply"
                type="button"
                class="mt-2 px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                Save
            </button>
        </div>

        <!-- Кнопка для загрузки ответов, если они есть, но еще не загружены -->
        <div v-if="comment.replies_count > 0 && !comment.showReplies" class="mt-2">
            <button
                @click="onLoadReplies"
                type="button"
                class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                Load Replies
                <span class="ml-1 inline-block bg-white text-green-500 font-semibold rounded-full px-2">
                    {{ comment.replies_count }}
                </span>
            </button>
        </div>

        <!-- Сообщение о загрузке ответов -->
        <div v-if="loadingReplies" class="mt-2 text-sm text-gray-500">
            Loading Replies...
        </div>

        <!-- Вывод загруженных ответов (рекурсивно) -->
        <div
            v-if="comment.showReplies && comment.replies && comment.replies.length"
            class="mt-2 ml-4 space-y-2"
        >
            <CommentItem
                v-for="reply in comment.replies"
                :key="reply.id"
                :comment="reply"
                :auth="auth"
                :level="level + 1"
                @toggle-comment-like="$emit('toggle-comment-like', $event)"
                @store-reply="$emit('store-reply', $event)"
                @load-replies="$emit('load-replies', $event)"
                @edit-comment="$emit('edit-comment', $event)"
                @delete-comment="$emit('delete-comment', $event)"
            />
        </div>
    </div>
</template>

<script>
export default {
    name: "CommentItem",
    props: {
        comment: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: true,
        },
        level: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            showReply: false,
            loadingReplies: false,
            reply: {
                content: "",
            },
            errors: {},
            isEditing: false,
            editedContent: "",
        };
    },
    computed: {
        // Чередование цвета фона в зависимости от уровня вложенности
        bgClass() {
            return this.level % 2 === 0 ? "bg-green-50" : "bg-white";
        },
    },
    methods: {
        // Форматирование даты
        formatDate(dateString) {
            const options = {
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            };
            return new Date(dateString).toLocaleString("en-EN", options);
        },
        // При клике на лайк
        onToggleLike() {
            if (!this.auth.user) {
                // Перенаправляем неавторизованного пользователя на логин
                window.location.href = "/login";
                return;
            }
            this.$emit("toggle-comment-like", this.comment.id);
        },

        // Показать/скрыть форму ответа
        toggleReply() {
            this.showReply = !this.showReply;
        },

        // Сохранить ответ на комментарий
        onStoreReply() {
            if (!this.reply.content.trim()) {
                this.errors = { "reply.content": ["Ответ не может быть пустым."] };
                return;
            }
            this.$emit("store-reply", {
                commentId: this.comment.id,
                content: this.reply.content,
            });
            this.reply.content = "";
            this.errors = {};
            this.showReply = false;
        },

        // Загрузить ответы к комментарию
        onLoadReplies() {
            this.loadingReplies = true;
            this.$emit("load-replies", this.comment.id);

            // После эмита родитель может загрузить ответы; сбросим "loadingReplies" чуть позже
            this.$nextTick(() => {
                this.loadingReplies = false;
            });
        },

        // Начать редактирование комментария
        startEditing() {
            this.isEditing = true;
            this.editedContent = this.comment.content;
        },
        // Отмена редактирования
        cancelEditing() {
            this.isEditing = false;
            this.editedContent = "";
        },
        // Сохранить изменения
        updateComment() {
            this.$emit("edit-comment", {
                commentId: this.comment.id,
                newContent: this.editedContent,
            });
            this.isEditing = false;
        },
        // Удалить комментарий
        deleteThisComment() {
            this.$emit("delete-comment", this.comment.id);
        },
    },
    components: {
        // Рекурсивное подключение для отображения вложенных ответов
        CommentItem: () => import("./CommentItem.vue"),
    },
};
</script>

<style scoped>
/* Дополнительные стили по необходимости */
</style>
