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
            <div
                v-for="post in postsData.data"
                :key="post.id"
                class="bg-white rounded-lg shadow-md overflow-hidden"
            >
                <!-- Изображение поста (отображается только если image_url существует) -->
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

                    <!-- Информация о профайле -->
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

                        <Link :href="route('clients.posts.show', post.id)" class="text-blue-500 hover:underline">
                            Read Post
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Индикатор загрузки -->
        <div v-if="isLoading" class="flex justify-center my-4">
            <span>Loading...</span>
        </div>

        <!-- Элемент для наблюдения за прокруткой -->
        <div ref="infiniteScrollTrigger" class="h-1"></div>
    </div>
</template>

<script>
import ClientLayout from "@/Layouts/Client/ClientLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

/**
 * Функция инициализации начальных значений фильтров.
 * Проходимся по всем фильтрам (ключ: тип), читаем query-параметры из URL.
 */
function initFiltersFromQuery(activeFilters) {
    const params = new URLSearchParams(window.location.search);
    const result = {};

    // Пробегаемся по всем ключам из active_filters
    for (const [fieldName, fieldType] of Object.entries(activeFilters)) {
        // Если в query-параметрах есть значение, используем его, иначе пустая строка
        result[fieldName] = params.get(fieldName) || "";
    }
    // Отдельно обрабатываем page
    result.page = parseInt(params.get("page") || "1", 10);

    return result;
}

export default {
    name: "Index",
    layout: ClientLayout,

    props: {
        posts: Object,          // данные для таблицы (PostResource Collection)
        active_filters: Object, // объект вида { title: 'text', content: 'text', ... }
        auth: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            // Инициализируем объект filter из URL-параметров, исходя из props.active_filters
            filter: initFiltersFromQuery(this.active_filters),
            postsData: this.posts || { data: [], meta: { links: [], pagination: {} } }, // либо данные из props, либо пустой объект
            isLoading: false,      // Флаг загрузки данных
            observer: null,        // Экземпляр IntersectionObserver
            allLoaded: false,      // Флаг, указывающий, что все посты загружены
        };
    },

    components: {
        Link,
    },

    mounted() {
        // Если Inertia не передал посты (или решили сбрасывать), грузим данные вручную при первом монтировании
        if (!this.postsData.data.length) {
            this.getPosts();
        }
        this.initInfiniteScroll();
    },

    beforeUnmount() {
        // Отключаем наблюдатель при размонтировании компонента
        if (this.observer) {
            this.observer.disconnect();
        }
    },

    watch: {
        /**
         * Отслеживаем только номер страницы отдельно,
         * чтобы при изменении фильтров не сбрасывать страницу в 1,
         * а также не вызывать двойных запросов.
         */
        "filter.page"(newVal, oldVal) {
            if (newVal !== oldVal && !this.allLoaded) {
                this.getPosts();
            }
        },
    },

    methods: {
        /**
         * Срабатывает при вводе в любой фильтр (кроме page).
         * Сбрасываем страницу и данные, обновляем URL и делаем запрос.
         */
        onFilterInputChange() {
            this.filter.page = 1;
            this.postsData.data = [];    // Очистить текущие посты
            this.allLoaded = false;      // Сбросить флаг окончания загрузки
            this.updateUrl();
            this.getPosts();
        },

        /**
         * Обновляем адресную строку, формируем query-параметры только из непустых значений.
         */
        updateUrl() {
            const params = new URLSearchParams();

            // Пробегаемся по всем полям фильтра (кроме page и исключённых полей), чтобы записать непустые
            for (const [fieldName, fieldType] of Object.entries(this.active_filters)) {
                if (fieldName === 'image_url') continue; // Исключаем image_url из фильтров
                if (this.filter[fieldName]) {
                    params.set(fieldName, this.filter[fieldName]);
                }
            }

            // Если страница не 1, тоже пишем в параметры
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
         * Формируем объект для отправки на сервер (axios), исключая пустые поля и page=1.
         */
        getRequestParams() {
            const result = {};

            for (const [fieldName, fieldType] of Object.entries(this.active_filters)) {
                if (fieldName === 'image_url') continue; // Исключаем image_url из фильтров
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
         * Запрашиваем посты через axios, передавая нужные query-параметры.
         * При успешном ответе добавляем новые посты к существующим.
         */
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
                        // Добавляем новые посты к существующим
                        this.postsData.data.push(...newPosts);
                        // Обновляем номер следующей страницы
                        this.filter.page = currentPage + 1;
                        // Проверяем, достигли ли последней страницы
                        if (currentPage >= lastPage) {
                            this.allLoaded = true;
                        }
                    } else {
                        // Если новых постов нет, устанавливаем флаг окончания загрузки
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

        /**
         * Инициализируем бесконечную прокрутку с помощью IntersectionObserver.
         */
        initInfiniteScroll() {
            const options = {
                root: null,               // наблюдать относительно viewport
                rootMargin: '0px',
                threshold: 1.0             // вызывать когда элемент полностью виден
            };

            this.observer = new IntersectionObserver(this.handleIntersect, options);
            if (this.$refs.infiniteScrollTrigger) {
                this.observer.observe(this.$refs.infiniteScrollTrigger);
            }
        },

        /**
         * Обработчик пересечения элемента-триггера с областью видимости.
         * Если элемент виден, загружаем новые посты.
         */
        handleIntersect(entries) {
            const entry = entries[0];
            if (entry.isIntersecting && !this.isLoading && !this.allLoaded) {
                this.getPosts();
            }
        },

        /**
         * Переключение лайка для поста.
         * @param {Number} postId - ID поста.
         */
        toggleLike(postId) {
            // Проверяем, аутентифицирован ли пользователь и если нет, то редирект на логин
            if (!this.auth.user) {
                window.location.href = '/login';
                return;
            }

            // Находим индекс поста в массиве
            const postIndex = this.postsData.data.findIndex(p => p.id === postId);
            if (postIndex === -1) return;

            const post = this.postsData.data[postIndex];

            // Оптимистическое обновление UI
            const originalIsLiked = post.is_liked;
            const originalLikes = post.likes;

            post.is_liked = !post.is_liked;
            post.likes += post.is_liked ? 1 : -1;

            axios.post(route("posts.likes.toggle", postId))
                .then((res) => {
                    // Обновляем данные из ответа сервера для точности
                    post.is_liked = res.data.is_liked;
                    post.likes = res.data.likes;
                })
                .catch((error) => {
                    console.error('Ошибка при переключении лайка:', error);
                    // Откатываем изменения в случае ошибки
                    post.is_liked = originalIsLiked;
                    post.likes = originalLikes;
                });
        },

        /**
         * Форматирование даты.
         * @param {String} dateStr - Строка даты.
         * @returns {String} Форматированная дата.
         */
        formatDate(dateStr) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateStr).toLocaleDateString(undefined, options);
        }
    },
};
</script>

<style scoped>
/* Добавьте стили по необходимости */
</style>
