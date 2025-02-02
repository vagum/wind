<template>
    <div>
        <!-- Ссылка Назад -->
        <div class="mb-2">
            <Link :href="route('clients.posts.index')" class="text-blue-500 hover:underline">&laquo; Назад</Link>
        </div>

        <!-- Заголовок Поста -->
        <div class="mb-4 text-2xl font-semibold text-gray-800 break-words">
            {{ post.title }}
        </div>

        <!-- Изображение Поста -->
        <div v-if="post.image_url" class="w-full md:w-1/2 mb-4">
            <img :src="post.image_url" :alt="post.title" class="rounded-lg shadow-md w-full" />
        </div>

        <!-- Содержимое Поста -->
        <div class="text-gray-700 mb-6 break-words">
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
                />
            </div>
        </div>
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
        post: Object,
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
            // Массив корневых комментариев
            comments: [],
            errors: {},
        };
    },
    components: {
        Link,
        CommentItem,
    },
    methods: {
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

        // Обработчик клика по лайку комментария
        handleToggleCommentLike(commentId) {
            axios
                .post(route("comments.likes.toggle", commentId))
                .then((response) => {
                    // Обновляем комментарий в массиве (рекурсивно)
                    this.updateCommentInList(commentId, response.data);
                })
                .catch((error) => {
                    console.error("Ошибка при обновлении лайка комментария:", error);
                });
        },

        // Обработчик добавления ответа к комментарию
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

        // Обработчик загрузки ответов для комментария
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

        // Рекурсивное обновление комментария по ID
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

        // Рекурсивное добавление ответа в нужный комментарий
        addReplyToComment(comments, commentId, newReply) {
            for (let comment of comments) {
                if (comment.id === commentId) {
                    // Если свойство replies отсутствует, добавляем его
                    if (!comment.replies) {
                        // Благодаря реактивности Vue 3, можно присвоить новое свойство
                        comment.replies = [];
                    }
                    comment.replies.unshift(newReply);
                    // Обновляем количество ответов
                    comment.replies_count = (comment.replies_count || 0) + 1;
                    // Отображаем ответы
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

        // Рекурсивное обновление ответов для комментария по ID
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
    },
};
</script>
