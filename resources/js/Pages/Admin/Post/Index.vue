<template>
    <div class="overflow-x-auto">
        <!-- Флеш-сообщение (если есть и не обнулено) -->
        <div v-if="flashMessage" class="p-4 mb-4 text-green-800 bg-green-200 rounded">
            {{ flashMessage }}
        </div>

        <!-- Кнопка создания нового поста -->
        <div class="mb-2">
            <Link :href="route('admin.posts.create')" class="text-blue-500 hover:underline">
                Create Post
            </Link>
        </div>

        <!-- Динамические фильтры -->
        <div class="mb-4 flex flex-wrap items-center space-x-3">
            <div
                v-for="(type, fieldName) in active_filters"
                :key="fieldName"
                class="flex flex-col"
            >
                <input
                    :type="type"
                    v-model="filter[fieldName]"
                    class="ml-1 border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :placeholder="`Filter by ${fieldName}`"
                    @input="onFilterInputChange"
                />
            </div>
        </div>

        <!-- Таблица постов -->
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
            <tr class="bg-gray-100 border-b">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Title
                </th>
                <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Published at
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            <tr
                v-for="post in postsData.data"
                :key="post.id"
                class="border-b hover:bg-gray-50"
            >
                <td class="px-6 py-3 text-gray-700">
                    {{ post.id }}
                </td>
                <td class="px-6 py-3 text-left text-gray-700">
                    <Link :href="route('admin.posts.show', post.id)">
                        {{ post.title }}
                    </Link>
                </td>
                <td class="px-6 py-3 text-center text-gray-700">
                    {{ post.published_at }}
                </td>
                <td class="px-6 py-3 text-sm text-gray-700">
                    <div class="flex justify-end space-x-2">
                        <!-- Кнопка редактирования -->
                        <div>
                            <Link
                                v-if="auth.user && auth.user.profile.id === post.profile_name.id"
                                :href="route('admin.posts.edit', post.id)"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="text-blue-500 h-5 w-5 cursor-pointer"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                                    />
                                </svg>
                            </Link>
                            <span v-else>
                              <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  fill="none"
                                  viewBox="0 0 24 24"
                                  stroke-width="1.5"
                                  stroke="currentColor"
                                  class="text-gray-500 h-5 w-5"
                              >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"
                                />
                              </svg>
                            </span>
                        </div>
                        <!-- Кнопка удаления (Index.vue удаляет пост без редиректа) -->
                        <div>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                :class="[
                                  auth.user && auth.user.profile.id === post.profile_name.id
                                    ? 'text-red-500 cursor-pointer'
                                    : 'text-gray-500'
                                  , 'size-5'
                                ]"
                                @click="auth.user && auth.user.profile.id === post.profile_name.id ? promptDeletePost(post) : null"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                                />
                            </svg>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>

        <!-- Пагинация -->
        <div class="mt-4 flex justify-center space-x-2">
            <span v-for="link in postsData.meta.links" :key="link.label">
                <button
                    :class="[
                        'px-3 py-1 rounded transition-colors duration-200',
                        link.active
                            ? 'bg-blue-500 text-white'
                            : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white',
                        !link.url ? 'cursor-not-allowed opacity-50' : ''
                    ]"
                    @click.prevent="getPostsByLink(link.url)"
                    :hidden="!link.url && link.label !== '...'"
                    v-html="link.label"
                />
            </span>
        </div>

        <!-- Модальное окно подтверждения удаления с анимацией -->
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
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

/**
 * Функция инициализации начальных значений фильтров.
 * Проходимся по всем фильтрам (ключ: тип), читаем query-параметры из URL.
 */
function initFiltersFromQuery(activeFilters) {
    const params = new URLSearchParams(window.location.search);
    const result = {};

    for (const [fieldName] of Object.entries(activeFilters)) {
        result[fieldName] = params.get(fieldName) || "";
    }
    result.page = parseInt(params.get("page") || "1", 10);

    return result;
}

export default {
    name: "Index",
    layout: AdminLayout,

    props: {
        posts: Object,          // Данные для таблицы
        active_filters: Object, // Объект вида { title: 'text', content: 'text', ... }
        auth: {
            type: Object,
            required: true,
        },
    },

    data() {
        return {
            // Фильтры
            filter: initFiltersFromQuery(this.active_filters),

            // Данные с сервера
            postsData: this.posts || null,

            // Флеш-сообщение (показываем при удалении или если пришло из localStorage)
            flashMessage: null,

            // Свойства для модалки удаления
            isModalOpen: false,
            postToDelete: null,
        };
    },

    components: {
        Link,
    },

    mounted() {
        // Считываем флеш-сообщение из localStorage (если приходим со страницы Show.vue)
        const msg = localStorage.getItem("flashMessage");
        if (msg) {
            this.flashMessage = msg;
            localStorage.removeItem("flashMessage");
        }

        // Если нет данных (например, при прямом переходе или сбросе),
        // то подгружаем посты через getPosts().
        if (!this.postsData) {
            this.getPosts();
        }
    },

    watch: {
        // Следим только за сменой страницы
        "filter.page"(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.updateUrl();
                this.getPosts();
            }
        },
    },

    methods: {
        /**
         * Сработает при вводе в любой текстовый фильтр (кроме page).
         * 1) Сбрасываем flashMessage, чтобы не мешала
         * 2) Ставим страницу в 1
         * 3) Обновляем URL
         * 4) Делаем запрос
         */
        onFilterInputChange() {
            this.flashMessage = null; // обнуляем флеш-сообщение
            this.filter.page = 1;
            this.updateUrl();
            this.getPosts();
        },

        /**
         * Обновляем адресную строку без перезагрузки,
         * формируя query-параметры только из непустых значений.
         */
        updateUrl() {
            const params = new URLSearchParams();

            for (const [fieldName] of Object.entries(this.active_filters)) {
                if (this.filter[fieldName]) {
                    params.set(fieldName, this.filter[fieldName]);
                }
            }
            if (this.filter.page && this.filter.page !== 1) {
                params.set("page", this.filter.page);
            }

            const queryString = params.toString();
            const newUrl = queryString
                ? `${window.location.pathname}?${queryString}`
                : window.location.pathname;

            window.history.replaceState({}, "", newUrl);
        },

        /**
         * Формируем объект для axios (непустые поля + page, если не 1).
         */
        getRequestParams() {
            const result = {};
            for (const [fieldName] of Object.entries(this.active_filters)) {
                if (this.filter[fieldName]) {
                    result[fieldName] = this.filter[fieldName];
                }
            }
            if (this.filter.page && this.filter.page !== 1) {
                result.page = this.filter.page;
            }
            return result;
        },

        /**
         * Запрашиваем посты через axios с учётом фильтров.
         */
        getPosts() {
            axios
                .get(route("admin.posts.index"), {
                    params: this.getRequestParams(),
                })
                .then((res) => {
                    this.postsData = res.data;
                })
                .catch((err) => {
                    console.error("Ошибка при загрузке:", err);
                });
        },

        /**
         * При клике на ссылку пагинации
         * 1) Сбрасываем flashMessage
         * 2) Извлекаем page
         * 3) Запускаем getPosts с новым page
         */
        getPostsByLink(url) {
            if (!url) return;
            this.flashMessage = null; // обнуляем флеш-сообщение

            const parsedUrl = new URL(url);
            const page = parseInt(parsedUrl.searchParams.get("page") || "1", 10);
            this.filter.page = page;
        },

        /**
         * Открываем модалку подтверждения удаления (Index.vue).
         */
        promptDeletePost(post) {
            this.postToDelete = post;
            this.isModalOpen = true;
        },

        /**
         * Подтверждаем удаление (Index.vue), БЕЗ редиректа.
         * Выводим сообщение о том, что пост удалён (без localStorage).
         */
        confirmDelete() {
            if (!this.postToDelete) return;

            // Сохраняем заголовок в переменную,
            // чтобы не потерять его после closeModal()
            const deletedTitle = this.postToDelete.title;

            axios
                .delete(route("admin.posts.destroy", this.postToDelete.id))
                .then(() => {
                    // Обновляем список постов
                    this.getPosts();

                    // Закрываем модалку (обнуляет postToDelete)
                    this.closeModal();

                    // Устанавливаем флеш-сообщение (в памяти)
                    this.flashMessage = `The post "${deletedTitle}" was successfully deleted.`;
                })
                .catch((err) => {
                    console.error("Ошибка при удалении:", err);
                    this.closeModal();
                });
        },

        /**
         * Закрыть модалку удаления
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
