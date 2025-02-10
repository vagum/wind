<template>
    <div class="overflow-x-auto">
        <!-- Динамические фильтры -->
        <div class="mb-4 mt-1 flex flex-wrap items-center space-x-3">
            <div v-for="(type, fieldName) in active_filters" :key="fieldName" class="flex flex-col">
                <input
                    :type="type"
                    v-model="filter[fieldName]"
                    class="ml-1 border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :placeholder="`Filter by ${fieldName}`"
                    @input="onFilterInputChange"
                />
            </div>
        </div>

        <!-- Таблица статов -->
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
            <tr class="bg-gray-100 border-b">
                <th class="px-2 py-3 pl-8 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Date
                    <button @click="sortBy('date', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('date', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Posts Count
                    <button @click="sortBy('posts_count', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('posts_count', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Comments Count
                    <button @click="sortBy('comments_count', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('comments_count', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Replies Count
                    <button @click="sortBy('replies_count', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('replies_count', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Likes Count
                    <button @click="sortBy('likes_count', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('likes_count', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Views Count
                    <button @click="sortBy('views_count', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('views_count', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Likes/Views
                    <button @click="sortBy('likes_views', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('likes_views', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
                <th class="px-2 py-3 text-center text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Likes/Comments
                    <button @click="sortBy('likes_comments', 'asc')" class="ml-0.5 text-xs">▲</button>
                    <button @click="sortBy('likes_comments', 'desc')" class="ml-0.5 text-xs">▼</button>
                </th>
            </tr>

            </thead>
            <tbody>
            <tr v-for="stat in statsData.data" :key="stat.id" class="border-b hover:bg-gray-50">
                <td class="px-6 py-3 text-left text-gray-700">{{ stat.date }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.posts_count }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.comments_count }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.replies_count }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.likes_count }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.views_count }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.likes_views }}</td>
                <td class="px-6 py-3 text-center text-gray-700">{{ stat.likes_comments }}</td>
            </tr>
            </tbody>
        </table>

        <!-- Пагинация -->
        <div class="mt-4 flex justify-center space-x-2">
      <span v-for="link in statsData.meta.links" :key="link.label">
        <button
            :class="[
            'px-3 py-1 rounded transition-colors duration-200',
            link.active
              ? 'bg-blue-500 text-white'
              : 'bg-gray-200 text-gray-700 hover:bg-blue-500 hover:text-white',
            !link.url ? 'cursor-not-allowed opacity-50' : ''
          ]"
            @click.prevent="getStatsByLink(link.url)"
            :hidden="!link.url && link.label !== '...'"
            v-html="link.label"
        />
      </span>
        </div>
    </div>
</template>

<script>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

/**
 * Инициализация фильтров из URL (без изменений)
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
        stats: Object,
        active_filters: Object,
    },
    data() {
        return {
            filter: initFiltersFromQuery(this.active_filters),
            statsData: this.stats || null,
            // Добавляем состояние сортировки
            sort: {
                column: '',       // например, 'date' или 'posts_count'
                direction: ''     // 'asc' или 'desc'
            }
        };
    },
    components: { Link },
    mounted() {
        if (!this.statsData) {
            this.getStats();
        }
    },
    watch: {
        "filter.page"(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.updateUrl();
                this.getStats();
            }
        },
    },
    methods: {
        onFilterInputChange() {
            this.filter.page = 1;
            this.updateUrl();
            this.getStats();
        },
        updateUrl() {
            const params = new URLSearchParams();
            // Добавляем параметры фильтров
            for (const [fieldName] of Object.entries(this.active_filters)) {
                if (this.filter[fieldName]) {
                    params.set(fieldName, this.filter[fieldName]);
                }
            }
            if (this.filter.page && this.filter.page !== 1) {
                params.set("page", this.filter.page);
            }
            // Добавляем параметры сортировки
            if (this.sort.column) {
                params.set("sort_column", this.sort.column);
                params.set("sort_direction", this.sort.direction);
            }
            const queryString = params.toString();
            const newUrl = queryString
                ? `${window.location.pathname}?${queryString}`
                : window.location.pathname;
            window.history.replaceState({}, "", newUrl);
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
            if (this.sort.column) {
                result.sort_column = this.sort.column;
                result.sort_direction = this.sort.direction;
            }
            return result;
        },
        getStats() {
            axios
                .get(route("admin.stats.index"), {
                    params: this.getRequestParams(),
                })
                .then((res) => {
                    this.statsData = res.data;
                })
                .catch((err) => {
                    console.error("Ошибка при загрузке:", err);
                });
        },
        getStatsByLink(url) {
            if (!url) return;
            const parsedUrl = new URL(url);
            const page = parseInt(parsedUrl.searchParams.get("page") || "1", 10);
            this.filter.page = page;
        },
        // Метод для сортировки
        sortBy(column, direction) {
            this.sort.column = column;
            this.sort.direction = direction;
            this.filter.page = 1; // Сбрасываем на первую страницу при изменении сортировки
            this.updateUrl();
            this.getStats();
        }
    },
};
</script>

<style scoped>
/* Ваши стили */
</style>
