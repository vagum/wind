<template>
    <div :class="['p-4 rounded-lg shadow-sm', bgClass]">
        <!-- Заголовок с именем автора и датой -->
        <div class="flex justify-between items-center mb-2">
      <span class="font-medium text-gray-800">
        {{ comment.profile && comment.profile.name ? comment.profile.name : 'Аноним' }}
      </span>
            <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
        </div>

        <!-- Текст комментария -->
        <p class="text-gray-700">{{ comment.content }}</p>

        <!-- Действия: Лайк и Ответ -->
        <div class="flex items-center space-x-4 mt-2">
            <!-- Кнопка Лайка -->
            <button @click="onToggleLike" type="button" class="flex items-center text-gray-700 focus:outline-none">
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

            <!-- Кнопка для показа/скрытия формы ответа -->
            <button v-if="this.auth && this.auth.user" @click="toggleReply" type="button" class="text-gray-700 hover:underline focus:outline-none">
                Ответить
            </button>
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
                Сохранить
            </button>
        </div>

        <!-- Кнопка для загрузки ответов, если их ещё не загрузили -->
        <div v-if="comment.replies_count > 0 && !comment.showReplies" class="mt-2">
            <button
                @click="onLoadReplies"
                type="button"
                class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                Загрузить ответы
                <span class="ml-1 inline-block bg-white text-green-500 font-semibold rounded-full px-2">
          {{ comment.replies_count }}
        </span>
            </button>
        </div>

        <!-- Сообщение о загрузке ответов -->
        <div v-if="loadingReplies" class="mt-2 text-sm text-gray-500">
            Загрузка ответов...
        </div>

        <!-- Вывод загруженных ответов (рекурсивно) -->
        <div v-if="comment.showReplies && comment.replies && comment.replies.length" class="mt-2 ml-4 space-y-2">
            <CommentItem
                v-for="reply in comment.replies"
                :key="reply.id"
                :comment="reply"
                :auth="auth"
                :level="level + 1"
                @toggle-comment-like="$emit('toggle-comment-like', $event)"
                @store-reply="$emit('store-reply', $event)"
                @load-replies="$emit('load-replies', $event)"
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
        auth: {               // Обязательно объявляем проп auth
            type: Object,
            required: true,
        },
        // Уровень вложенности (0 – корневой комментарий)
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
        };
    },
    computed: {
        // Вычисляем класс фона в зависимости от уровня вложенности
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
        // При клике на лайк эмитируем событие вверх
        onToggleLike() {
            if (!this.auth.user) {
                window.location.href = "/login";
                return;
            }
            this.$emit("toggle-comment-like", this.comment.id);
        },
        // Переключение отображения формы ответа
        toggleReply() {
            this.showReply = !this.showReply;
        },
        // При сохранении ответа эмитируем событие с данными
        onStoreReply() {
            if (!this.reply.content.trim()) {
                this.errors = { "reply.content": ["Ответ не может быть пустым."] };
                return;
            }
            this.$emit("store-reply", { commentId: this.comment.id, content: this.reply.content });
            this.reply.content = "";
            this.errors = {};
            this.showReply = false;
        },
        // При загрузке ответов эмитируем событие вверх
        onLoadReplies() {
            this.loadingReplies = true;
            this.$emit("load-replies", this.comment.id);
            // Сброс индикатора загрузки можно произвести после обновления данных родителем.
            // Здесь приведён пример простого сброса через nextTick.
            this.$nextTick(() => {
                this.loadingReplies = false;
            });
        },
    },
    components: {
        // Рекурсивное подключение компонента для вложенных ответов
        CommentItem: () => import("./CommentItem.vue"),
    },
};
</script>

<style scoped>
/* Дополнительные стили по необходимости */
</style>
