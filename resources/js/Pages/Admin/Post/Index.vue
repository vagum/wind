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
    layout: AdminLayout,

    props: {
        posts: Object,          // данные для таблицы (PostResource Collection)
        active_filters: Object, // объект вида { title: 'text', content: 'text', ... }
    },

    data() {
        return {
            // Инициализируем объект filter из URL-параметров, исходя из props.active_filters
            filter: initFiltersFromQuery(this.active_filters),
            postsData: this.posts || null, // либо данные из props, либо null
        };
    },

    components: {
        Link,
    },

    mounted() {
        // Если Inertia не передал посты (или решили сбрасывать), грузим данные вручную при первом монтировании
        if (!this.postsData) {
            this.getPosts();
        }
    },

    watch: {
        /**
         * Отслеживаем только номер страницы отдельно,
         * чтобы при клике на пагинацию не сбрасывать страницу в 1,
         * а также не вызывать двойных запросов.
         */
        "filter.page"(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.updateUrl();
                this.getPosts();
            }
        },
    },

    methods: {
        /**
         * Срабатывает при вводе в любой фильтр (кроме page).
         * Сбрасываем page в 1, обновляем URL и делаем запрос.
         */
        onFilterInputChange() {
            this.filter.page = 1;
            this.updateUrl();
            this.getPosts();
        },

        /**
         * Обновляем адресную строку, формируем query-параметры только из непустых значений.
         */
        updateUrl() {
            const params = new URLSearchParams();

            // Пробегаемся по всем полям фильтра (кроме page), чтобы записать непустые
            for (const [fieldName, fieldType] of Object.entries(this.active_filters)) {
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
         * При клике на ссылку пагинации извлекаем из неё параметр page
         * и пишем в this.filter.page.
         */
        getPostsByLink(url) {
            if (!url) return;
            const parsedUrl = new URL(url);
            const page = parseInt(parsedUrl.searchParams.get("page") || "1", 10);
            this.filter.page = page;
        },
    },
};
</script>

<template>
    <div class="overflow-x-auto">
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
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Title</th>
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
                        <button class="px-4 py-1 text-white bg-blue-500 rounded hover:bg-blue-600">
                            Edit
                        </button>
                        <button class="px-4 py-1 text-white bg-red-500 rounded hover:bg-red-600">
                            Delete
                        </button>
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
    </div>
</template>
