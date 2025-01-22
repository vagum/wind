<template>
        <!-- Ссылка Назад -->
        <div class="mb-2">
            <Link :href="route('admin.posts.index')" class="text-blue-500 hover:underline">&laquo; Назад</Link>
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

        <!-- Секция комментариев -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold mb-4">Comments</h3>

            <!-- Форма добавления комментария -->
            <div class="mb-6">
                <h4 class="text-lg font-medium mb-2">Add Comment</h4>
                <div class="flex flex-col items-start space-y-3">
                    <textarea
                        v-model="comment.content"
                        :class="['w-full md:w-1/2 border rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2',
                        errors[`comment.content`] ? 'border-red-500 focus:ring-red-500' :
                        'border-gray-300 focus:ring-blue-500']"
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
            <div v-if="localComments && localComments.length" class="w-full md:w-1/2 space-y-4">
                <div v-for="comment in localComments" :key="comment.id" class="p-4 bg-gray-50 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-medium text-gray-800">
                            {{ comment.profile && comment.profile.name ? comment.profile.name : 'Аноним' }}
                        </span>
                        <span class="text-sm text-gray-500">{{ formatDate(comment.created_at) }}</span>
                    </div>
                    <p class="text-gray-700">{{ comment.content }}</p>
                </div>
            </div>
            <div v-else class="text-gray-500">No Comments</div>
        </div>
</template>

<script>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from 'axios';

export default {
    name: "Show",

    layout: AdminLayout,

    props: {
        post: Object,
    },

    data() {
        return {
            comment: {
                content: '',
            },
            errors: {}, // Для хранения ошибок
            localComments: [], // Локальная копия комментариев
        }
    },

    components: {
        Link
    },

    mounted() {
        // Инициализируем локальную копию комментариев при монтировании компонента
        this.localComments = this.post.comments && this.post.comments.data ? [...this.post.comments.data] : [];
    },

    methods: {
        storeComment() {
            // Проверка на пустой комментарий
            if (!this.comment.content.trim()) {
                this.errors = {
                    'comment.content': ['The comment cannot be empty.']
                };
                return;
            }

            axios.post(route('posts.comments.store', this.post.id), this.comment)
                .then(response => {
                    const newComment = response.data; // Изменено: предполагаем, что response.data содержит сам комментарий
                    // Добавляем новый комментарий в начало списка
                    this.localComments = [newComment, ...this.localComments];
                    // Очищаем поле ввода
                    this.comment.content = '';
                    // Сбрасываем ошибки
                    this.errors = {};
                })
                .catch(error => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        // Общая ошибка, если структура ответа отличается
                        this.errors = {'general': ['Произошла ошибка при добавлении комментария.']};
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
            return new Date(dateString).toLocaleString('en-EN', options);
        }
    }
}
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>
