<template>
    <div class="max-w-7xl mx-auto px-4">
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

        <!-- Карточки постов -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <PostItem
                v-for="post in postsData.data"
                :key="post.id"
                :post="post"
                :auth="auth"
                :format-date="formatDate"
                @toggle-like="toggleLike"
                @delete="openModal"
                class="bg-white rounded-lg shadow-md overflow-hidden"
            />
        </div>

        <!-- Индикатор загрузки -->
        <div v-if="isLoading" class="flex justify-center my-4">
            <span>Loading...</span>
        </div>

        <!-- Элемент для наблюдения за прокруткой -->
        <div ref="infiniteScrollTrigger" class="h-1"></div>

        <!-- Модальное окно подтверждения удаления с анимацией -->
        <transition name="modal">
            <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-lg shadow-lg w-96 transform transition-all duration-300">
                    <div class="px-6 py-4">
                        <h2 class="text-xl font-semibold mb-2">Confirm Delete</h2>
                        <p class="mb-2">Are you sure you want to delete:</p>
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
import PostItem from "@/Components/Post/PostItem.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

/**
 * Инициализация фильтров из query-параметров URL.
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
    layout: ClientLayout,
    components: {
        Link,
        PostItem,
    },
    props: {
        posts: {
            type: Object,
            default: () => ({ data: [], meta: {} }),
        },
        active_filters: {
            type: Object,
            default: () => ({
                title: "text",
                description: "text",
            }),
        },
        auth: {
            type: Object,
            required: true,
        },
    },
    data() {
        // Инициализируем фильтры
        const initFilters = initFiltersFromQuery(this.active_filters);

        // Если данные для первой страницы уже переданы, устанавливаем номер следующей страницы
        if (
            this.posts &&
            this.posts.meta &&
            this.posts.meta.current_page &&
            this.posts.meta.last_page
        ) {
            // Если первая страница уже загружена, следующая страница равна current_page + 1,
            // но если current_page уже последняя, то помечаем, что все данные загружены.
            if (this.posts.meta.current_page < this.posts.meta.last_page) {
                initFilters.page = this.posts.meta.current_page + 1;
            } else {
                initFilters.page = this.posts.meta.current_page;
            }
        }
        return {
            filter: initFilters,
            postsData: this.posts || { data: [], meta: { links: [], pagination: {} } },
            isLoading: false,
            observer: null,
            allLoaded:
                this.posts &&
                this.posts.meta &&
                this.posts.meta.current_page &&
                this.posts.meta.last_page &&
                this.posts.meta.current_page >= this.posts.meta.last_page,
            // Свойства для работы с модальным окном
            isModalOpen: false,
            postToDelete: {},
        };
    },
    mounted() {
        // Если данных для первой страницы нет, загружаем их.
        if (!this.postsData.data.length) {
            this.getPosts();
        }
        this.initInfiniteScroll();
    },
    beforeUnmount() {
        if (this.observer) {
            this.observer.disconnect();
        }
    },
    watch: {
        "filter.page"(newVal, oldVal) {
            if (newVal !== oldVal && !this.allLoaded) {
                this.getPosts();
            }
        },
    },
    methods: {
        onFilterInputChange() {
            // При изменении фильтра сбрасываем пагинацию и данные
            this.filter.page = 1;
            this.postsData.data = [];
            this.allLoaded = false;
            this.updateUrl();
            this.getPosts();
        },
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
            // Передаём null вместо реактивного объекта
            window.history.replaceState(null, "", newUrl);
        },
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
        getPosts() {
            if (this.isLoading || this.allLoaded) return;
            this.isLoading = true;
            axios
                .get(route("clients.posts.index"), {
                    params: this.getRequestParams(),
                })
                .then((res) => {
                    const newPosts = res.data.data;
                    const currentPage = res.data.meta.current_page;
                    const lastPage = res.data.meta.last_page;
                    if (newPosts.length) {
                        // Добавляем новые посты
                        this.postsData.data.push(...newPosts);
                        // Если текущая страница меньше последней, увеличиваем номер страницы
                        if (currentPage < lastPage) {
                            this.filter.page = currentPage + 1;
                        } else {
                            this.allLoaded = true;
                        }
                    } else {
                        this.allLoaded = true;
                    }
                })
                .catch((err) => {
                    console.error("Ошибка при загрузке:", err);
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        initInfiniteScroll() {
            const options = {
                root: null,
                rootMargin: "0px",
                threshold: 1.0,
            };
            this.observer = new IntersectionObserver(this.handleIntersect, options);
            if (this.$refs.infiniteScrollTrigger) {
                this.observer.observe(this.$refs.infiniteScrollTrigger);
            }
        },
        handleIntersect(entries) {
            const entry = entries[0];
            if (entry.isIntersecting && !this.isLoading && !this.allLoaded) {
                this.getPosts();
            }
        },
        toggleLike(postId) {
            if (!this.auth.user) {
                window.location.href = "/login";
                return;
            }
            const postIndex = this.postsData.data.findIndex((p) => p.id === postId);
            if (postIndex === -1) return;
            const post = this.postsData.data[postIndex];
            // Если уже идёт запрос по этому посту, не обрабатываем повторный клик
            if (post.isToggling) return;
            post.isToggling = true;
            axios
                .post(route("posts.likes.toggle", postId))
                .then((res) => {
                    // Обновляем данные поста согласно ответу сервера
                    post.is_liked = res.data.is_liked;
                    post.likes = res.data.likes;
                })
                .catch((error) => {
                    console.error("Ошибка при переключении лайка:", error);
                })
                .finally(() => {
                    post.isToggling = false;
                });
        },
        formatDate(dateStr) {
            const options = {year: "numeric", month: "long", day: "numeric"};
            return new Date(dateStr).toLocaleDateString(undefined, options);
        },
        confirmDelete() {
            if (!this.postToDelete || !this.postToDelete.id) return;
            axios
                .delete(route("clients.post.destroy", this.postToDelete.id))
                .then(() => {
                    this.postsData.data = this.postsData.data.filter(
                        (post) => post.id !== this.postToDelete.id
                    );
                    this.closeModal();
                })
                .catch((error) => {
                    console.error("Ошибка при удалении поста:", error);
                    this.closeModal();
                });
        },
        openModal(post) {
            this.postToDelete = post;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.postToDelete = {};
        },
    },
};
</script>

<style scoped>
/* Стили для анимации модального окна */
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
