<script>
import {Link} from "@inertiajs/vue3";

export default {
    name: "AdminLayout" ,
    components: {Link}
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <div class="p-4 bg-white text-gray-800 shadow-md mx-4">
            <header class="text-center text-2xl font-semibold tracking-wide">
                HEADER
            </header>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row">
            <!-- Sidebar -->
            <aside class="w-full lg:w-1/4 bg-white shadow-lg rounded-lg p-6 m-4">
                <h2 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Sidebar</h2>
                <ul class="space-y-2">
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.users.index`)">Users</Link></li>
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.profiles.index`)">Profiles</Link></li>
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.posts.index`)">Posts</Link></li>
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.comments.index`)">Comments</Link></li>
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.categories.index`)">Categories</Link></li>
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.tags.index`)">Tags</Link></li>
                    <li class="text-gray-600 hover:text-blue-500 cursor-pointer"><Link :href="route(`admin.roles.index`)">Roles</Link></li>
                </ul>
            </aside>

            <!-- Main Article -->
            <article class="w-full lg:w-3/4 bg-white shadow-lg rounded-lg p-6 m-4">
                <slot />
            </article>
        </div>
    </div>
</template>

<style scoped>

</style>
