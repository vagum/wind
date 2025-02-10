<template>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Изображение поста (отображается, если image_url существует) -->
        <img
            v-if="post.image_url"
            :src="post.image_url"
            alt="Post Image"
            class="w-full h-48 object-cover"
            loading="lazy"
        />

        <div class="p-4">
            <!-- Заголовок поста -->
            <h2 class="text-xl font-semibold mb-2">
                <Link :href="route('clients.posts.show', post.id)">
                    {{ post.title }}
                </Link>
            </h2>

            <!-- Описание поста -->
            <p class="text-gray-700 mb-4">
                {{ post.description }}
            </p>

            <!-- Информация о профиле -->
            <div class="flex items-center mb-4">
                <img
                    :src="post.profile_name.avatar"
                    alt="Profile Avatar"
                    class="w-10 h-10 rounded-full mr-3"
                />
                <div>
                    <p class="text-gray-900 font-medium">
                        <Link :href="route('admin.profiles.show', post.profile_name.id)">
                            {{ post.profile_name.name }}
                        </Link>
                    </p>
                    <p class="text-gray-600 text-sm">{{ formatDate(post.published_at) }}</p>
                </div>
            </div>

            <!-- Категория и теги -->
            <div class="flex items-center justify-between mb-4">
        <span class="bg-blue-100 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">
          {{ post.category_title }}
        </span>
                <div class="flex flex-wrap">
          <span
              v-for="(tag, index) in post.tags_title"
              :key="index"
              class="bg-gray-200 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded"
          >
            {{ tag }}
          </span>
                </div>
            </div>

            <!-- Лайки и действия -->
            <div class="flex items-center justify-between">
                <button @click="toggleLike(post.id)" class="flex items-center text-gray-700 focus:outline-none">
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

                <Link :href="route('clients.posts.show', post.id)" class="text-gray-800 hover:underline">
                    Read
                </Link>

                <!-- Ссылка Edit будет отображаться только если пользователь авторизован и пост его -->
                <Link
                    v-if="auth.user && auth.user.profile.id === post.profile_name.id"
                    :href="route('admin.posts.edit', post.id)" class="text-blue-500 hover:underline">
                    Edit
                </Link>

                <!-- Ссылка Delete будет отображаться только если пользователь авторизован и пост его -->
                <a
                    v-if="auth.user && auth.user.profile.id === post.profile_name.id"
                    @click.prevent="deletePost"
                    href="#"
                    class="text-red-500 hover:underline"
                >
                    Delete
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
    name: "PostItem",
    props: {
        post: {
            type: Object,
            required: true,
        },
        formatDate: {
            type: Function,
            required: true,
        },
        auth: {               // Обязательно объявляем проп auth
            type: Object,
            required: true,
        },
    },
    components: { Link },

    methods: {
        toggleLike(postId) {
            this.$emit("toggle-like", postId);
        },
        // Вместо непосредственного удаления эмитим событие для родительского компонента
        deletePost() {
            this.$emit("delete", this.post);
        },
    },
};
</script>

<style scoped>
/* Здесь можно добавить стили для PostItem */
</style>
