<template>
    <div>
        <!-- Ссылка Назад -->
        <div class="mb-2">
            <Link :href="route('clients.posts.index')" class="text-blue-500 hover:underline">&laquo; Back</Link>
        </div>

        <!-- Заголовок Поста -->
        <div class="w-1/2 mb-4 text-2xl font-semibold text-gray-800 break-words">
            {{ post.title }}
        </div>

        <!-- Изображение Поста -->
        <div v-if="post.image_url" class="w-1/2 md:w-1/2 mb-4">
            <img :src="post.image_url" :alt="post.title" class="rounded-lg shadow-md w-full" />
        </div>

        <!-- Содержимое Поста -->
        <div class="w-1/2 text-gray-700 mb-6 break-words">
            {{ post.content }}
        </div>

        <!-- Действия с постом: Лайк и счетчик комментариев -->
        <div class="w-1/2 mb-6">
            <div class="flex items-center justify-between">
                <!-- Лайк поста -->
                <button @click="toggleLike" type="button" class="flex items-center text-gray-700 focus:outline-none">
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

                <!-- Просмотры -->
                <div class="flex items-center text-gray-700">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :fill="post.views ? '#73FBFD' : 'none'"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 mr-1"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                        />
                    </svg>
                    <span>{{ post.views }}</span>
                </div>

                <!-- Комментарии -->
                <div class="flex items-center text-gray-700">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        :fill="post.comments_count ? '#D1D1D1' : 'none'"
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
                    <span>{{ post.comments_count }}</span>
                </div>

                <!-- Кнопка Редактировать (видна только для пользователя чей пост) -->
                <Link
                    v-if="auth.user && auth.user.profile.id === post.profile_name.id"
                    :href="route('admin.posts.edit', post.id)">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="text-blue-500 size-5 cursor-pointer"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m16.862
                            4.487 1.687-1.688a1.875 1.875 0 1
                            1 2.652 2.652L10.582 16.07a4.5 4.5
                            0 0 1-1.897 1.13L6
                            18l.8-2.685a4.5 4.5 0 0
                            1 1.13-1.897l8.932-8.931Zm0
                            0L19.5 7.125M18 14v4.75A2.25
                            2.25 0 0 1 15.75 21H5.25A2.25
                            2.25 0 0 1 3 18.75V8.25A2.25
                            2.25 0 0 1 5.25 6H10"
                        />
                    </svg>
                </Link>

                <!-- Кнопка Удалить (видна только для пользователя чей пост) -->
                <div v-if="auth.user && auth.user.profile.id === post.profile_name.id">
                    <svg
                        @click="promptDeletePost(post)"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="text-red-500 size-5 cursor-pointer"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m14.74
                            9-.346 9m-4.788 0L9.26
                            9m9.968-3.21c.342.052.682.107
                            1.022.166m-1.022-.165L18.16
                            19.673a2.25 2.25 0 0
                            1-2.244 2.077H8.084a2.25
                            2.25 0 0 1-2.244-2.077L4.772
                            5.79m14.456 0a48.108 48.108
                            0 0 0-3.478-.397m-12
                            .562c.34-.059.68-.114
                            1.022-.165m0 0a48.11
                            48.11 0 0 1 3.478-.397m7.5
                            0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964
                            51.964 0 0 0-3.32
                            0c-1.18.037-2.09
                            1.022-2.09 2.201v.916m7.5 0a48.667
                            48.667 0 0 0-7.5 0"
                        />
                    </svg>
                </div>

            </div>
        </div>

        <!-- Секция комментариев -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold mb-4">Comments</h3>

            <!-- Форма добавления комментария -->
            <div v-if="this.auth && this.auth.user" class="mb-6">
                <h4 class="text-lg font-medium mb-2">Add Comment</h4>
                <div class="flex flex-col items-start space-y-3">
          <textarea
              v-model="comment.content"
              :class="[
              'w-full md:w-1/2 border rounded-lg p-3 text-gray-700 focus:outline-none focus:ring-2',
              errors['comment.content'] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
            ]"
              placeholder="Your Comment"
          ></textarea>
                    <p v-if="errors['comment.content']" class="text-red-500 text-sm">
            <span v-for="(error, index) in errors['comment.content']" :key="index">
              {{ error }}<br />
            </span>
                    </p>
                    <button
                        @click.prevent="storeComment"
                        type="button"
                        class="w-full md:w-1/2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Save Comment
                    </button>
                </div>
            </div>

            <!-- Кнопка для загрузки комментариев -->
            <div v-if="post.comments_count" class="flex items-center mb-4">
                <button
                    @click.prevent="getComments"
                    type="button"
                    class="w-full md:w-1/6 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-blue-500 flex justify-center items-center"
                >
                    Comments
                    <span class="ml-2 inline-block bg-white text-green-500 font-semibold rounded-full px-2">
            {{ post.comments_count }}
          </span>
                </button>
            </div>
            <div v-else class="text-gray-500">No Comments</div>

            <!-- Вывод списка корневых комментариев -->
            <div v-if="comments.length" class="w-full md:w-1/2 space-y-4">
                <CommentItem
                    v-for="(commentItem, index) in comments"
                    :key="commentItem.id"
                    :auth="auth"
                    :comment="commentItem"
                    @toggle-comment-like="handleToggleCommentLike"
                    @store-reply="handleStoreReply"
                    @load-replies="handleLoadReplies"
                    @edit-comment="handleEditComment"
                    @delete-comment="handleDeleteComment"
                />
            </div>
        </div>

        <!-- Модальное окно подтверждения удаления поста -->
        <transition name="modal">
            <div
                v-if="isModalOpen"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
            >
                <div
                    class="bg-white rounded-lg shadow-lg w-96 transform transition-all duration-300"
                >
                    <div class="px-6 py-4">
                        <h2 class="text-xl font-semibold mb-2">Confirm Delete</h2>
                        <p class="mb-2">Are you really want delete this:</p>
                        <p><strong>{{ postToDelete.title }}</strong>?</p>
                    </div>
                    <div class="px-6 py-4 flex justify-end space-x-3">
                        <button
                            @click="closeModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                        <button
                            @click="confirmDelete"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import ClientLayout from "@/Layouts/Client/ClientLayout.vue";
import CommentItem from "@/Components/Comment/CommentItem.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
    name: "Show",
    layout: ClientLayout,
    props: {
        post: {
            type: Object,
            required: true,
        },
        auth: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            comment: {
                content: "",
            },
            comments: [],  // Массив корневых комментариев
            errors: {},
            // Свойства для модального окна удаления поста
            isModalOpen: false,
            postToDelete: null,
        };
    },
    components: {
        Link,
        CommentItem,
    },
    methods: {
        // Лайк поста
        toggleLike() {
            if (!this.auth.user) {
                window.location.href = "/login";
                return;
            }
            axios
                .post(route("posts.likes.toggle", this.post.id))
                .then((response) => {
                    this.post.is_liked = response.data.is_liked;
                    this.post.likes = response.data.likes;
                })
                .catch((error) => {
                    console.error("Ошибка при обновлении лайка поста:", error);
                });
        },

        // Добавить комментарий
        storeComment() {
            if (!this.comment.content.trim()) {
                this.errors = {
                    "comment.content": ["The comment cannot be empty."],
                };
                return;
            }
            axios
                .post(route("posts.comments.store", this.post.id), this.comment)
                .then((response) => {
                    const newComment = response.data;
                    this.comments.unshift(newComment);
                    this.post.comments_count++;
                    this.comment.content = "";
                    this.errors = {};
                })
                .catch((error) => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.errors = {
                            general: ["Произошла ошибка при добавлении комментария."],
                        };
                    }
                });
        },

        // Загрузить комментарии
        getComments() {
            axios
                .get(route("posts.comments.index", this.post.id))
                .then((res) => {
                    this.comments = res.data;
                })
                .catch((error) => {
                    console.error("Ошибка при получении комментариев:", error);
                });
        },

        // Лайк комментария (рекурсивное обновление)
        handleToggleCommentLike(commentId) {
            axios
                .post(route("comments.likes.toggle", commentId))
                .then((response) => {
                    this.updateCommentInList(commentId, response.data);
                })
                .catch((error) => {
                    console.error("Ошибка при обновлении лайка комментария:", error);
                });
        },

        // Добавление ответа к комментарию
        handleStoreReply({ commentId, content }) {
            axios
                .post(route("comments.replies.store", commentId), { content })
                .then((response) => {
                    const newReply = response.data;
                    this.addReplyToComment(this.comments, commentId, newReply);
                })
                .catch((error) => {
                    console.error("Ошибка при добавлении ответа:", error);
                });
        },

        // Загрузка ответов для комментария
        handleLoadReplies(commentId) {
            axios
                .get(route("comments.replies.index", commentId))
                .then((response) => {
                    this.updateRepliesForComment(this.comments, commentId, response.data);
                })
                .catch((error) => {
                    console.error("Ошибка при загрузке ответов:", error);
                });
        },

        // Редактирование комментария
        handleEditComment({ commentId, newContent }) {
            axios
                .put(route("comments.update", commentId), { content: newContent })
                .then((response) => {
                    this.updateCommentInList(commentId, response.data);
                })
                .catch((error) => {
                    console.error("Ошибка при редактировании комментария:", error);
                });
        },

        // Удаление комментария
        handleDeleteComment(commentId) {
            axios
                .delete(route("comments.destroy", commentId))
                .then(() => {
                    // Удаляем комментарий из локального массива
                    this.removeCommentFromList(this.comments, commentId);
                    // Уменьшим общее количество комментариев поста
                    this.post.comments_count--;
                })
                .catch((error) => {
                    console.error("Ошибка при удалении комментария:", error);
                });
        },

        // Помощник: рекурсивное удаление комментария из массива
        removeCommentFromList(comments, commentId) {
            for (let i = 0; i < comments.length; i++) {
                if (comments[i].id === commentId) {
                    comments.splice(i, 1);
                    return true;
                }
                if (comments[i].replies && comments[i].replies.length) {
                    if (this.removeCommentFromList(comments[i].replies, commentId)) {
                        return true;
                    }
                }
            }
            return false;
        },

        // Помощник: рекурсивное обновление комментария (например, при лайке/редактировании)
        updateCommentInList(commentId, updatedData) {
            const updateRecursively = (comments) => {
                comments.forEach((comment) => {
                    if (comment.id === commentId) {
                        Object.assign(comment, updatedData);
                    }
                    if (comment.replies && comment.replies.length) {
                        updateRecursively(comment.replies);
                    }
                });
            };
            updateRecursively(this.comments);
        },

        // Помощник: рекурсивное добавление ответа
        addReplyToComment(comments, commentId, newReply) {
            for (let comment of comments) {
                if (comment.id === commentId) {
                    if (!comment.replies) {
                        comment.replies = [];
                    }
                    comment.replies.unshift(newReply);
                    comment.replies_count = (comment.replies_count || 0) + 1;
                    comment.showReplies = true;
                    return true;
                }
                if (comment.replies && comment.replies.length) {
                    if (this.addReplyToComment(comment.replies, commentId, newReply)) {
                        return true;
                    }
                }
            }
            return false;
        },

        // Помощник: рекурсивное обновление списка ответов
        updateRepliesForComment(comments, commentId, replies) {
            for (let comment of comments) {
                if (comment.id === commentId) {
                    comment.replies = replies;
                    comment.showReplies = true;
                    return true;
                }
                if (comment.replies && comment.replies.length) {
                    if (this.updateRepliesForComment(comment.replies, commentId, replies)) {
                        return true;
                    }
                }
            }
            return false;
        },

        /**
         * Открыть модальное окно удаления поста
         */
        promptDeletePost(post) {
            this.postToDelete = post;
            this.isModalOpen = true;
        },

        /**
         * Подтвердить удаление поста
         */
        confirmDelete() {
            if (!this.postToDelete) return;
            axios
                .delete(route("admin.posts.destroy", this.postToDelete.id))
                .then(() => {
                    // Сохраняем флеш-сообщение в localStorage (пример)
                    localStorage.setItem(
                        "flashMessage",
                        `The post "${this.postToDelete.title}" was successfully deleted.`
                    );
                    window.location.href = route("admin.posts.index");
                })
                .catch((err) => {
                    console.error("Ошибка при удалении поста:", err);
                    this.closeModal();
                });
        },

        /**
         * Закрыть модальное окно
         */
        closeModal() {
            this.isModalOpen = false;
            this.postToDelete = null;
        },
    },
};
</script>

<style scoped>
/* Анимация модального окна */
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.modal-enter-to,
.modal-leave-from {
    opacity: 1;
    transform: scale(1);
}
</style>
