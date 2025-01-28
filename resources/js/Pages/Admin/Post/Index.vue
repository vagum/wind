<script>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
export default {
    name: "Index",

    layout: AdminLayout,

    props: {
        posts: Array,
    },

    components: {
        Link
    }
}
</script>

<template>
    <!-- Таблица -->
    <div class="overflow-x-auto">
    <div class="mb-2">
        <Link :href="route('admin.posts.create')" class="text-blue-500 hover:underline">Create Post</Link>
    </div>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead>
            <tr class="bg-gray-100 border-b">
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    ID
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Title
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="post in posts" class="border-b hover:bg-gray-50">
                <td class="px-6 py-3 text-gray-700">
                    {{ post.id }}
                </td>
                <td class="px-6 py-3 text-gray-700">
                    <Link :href="route('admin.posts.show', post.id)">{{ post.title }}</Link>
                </td>
                <td class="px-6 text-sm text-gray-700">
                    <div class="flex space-x-2">
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
    </div>
</template>

<style scoped>

</style>
