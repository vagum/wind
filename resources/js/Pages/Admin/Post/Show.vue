<script>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
export default {
    name: "Show",

    layout: AdminLayout,

    props: {
        post: Object,
    },

    components: {
        Link
    }
}
</script>

<template>
        <div class="mb-2">
            <Link :href="route('admin.posts.index')" class="text-blue-500 hover:underline">&laquo; Back</Link>
        </div>
        <div class="mb-4 text-xl font-semibold text-gray-800 break-words">
            {{ post.title }}
        </div>
        <div v-if="post.image_url" class="mb-4 text-xl font-semibold text-gray-800 break-words">
            <img :src="post.image_url" :alt="post.title"/>
        </div>
        <div class="text-gray-600 break-words">
            {{ post.content }}
        </div>
</template>

<style scoped>

</style>
