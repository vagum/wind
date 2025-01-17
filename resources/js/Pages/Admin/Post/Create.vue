<script>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import config from "tailwindcss/defaultConfig.js";
export default {
    name: "Create",

    layout: AdminLayout,

    components: {
        Link
    },
    props: {
        categories: Array,
    },

    data() {
        return {
            post: {
                category_id: null,
                image: null,
            },
            isSuccess: false,
        }
    },

    methods: {
        storePost () {
            axios.post(route('admin.posts.store'), this.post, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then (res => {
                    this.post = {
                        category_id: null,
                    }
                    this.isSuccess = true
                    this.$refs.image_input.value =null
                })
                .catch(e => {})
                .finally(() => {})
        },
        setImage(e) {
            this.post.image = e.target.files[0];
        }
    }

}
</script>

<template>
    <div>
        <!-- Back Link -->
        <div class="mb-4">
            <Link :href="route('admin.posts.index')" class="text-blue-500 hover:underline text-sm">&laquo; Back</Link>
        </div>

        <!-- Form Container -->
        <div class="p-6 bg-white border border-gray-200 shadow-lg rounded-lg">

            <!-- Success -->
            <div v-if="isSuccess" class="mb-4 w-full p-4 bg-green-200 text-green-800 font-medium rounded">
                Success!
            </div>

            <!-- Title -->
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input
                    type="text"
                    v-model="post.title"
                    class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter title">
            </div>

            <!-- Description -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input
                    type="text"
                    v-model="post.description"
                    class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Enter description">
            </div>

            <!-- Content -->
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <textarea
                    v-model="post.content"
                    class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="5"
                    placeholder="Enter content"></textarea>
            </div>

            <!-- Published At -->
            <div class="mb-4">
                <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published At</label>
                <input
                    v-model="post.published_at"
                    type="date"
                    class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Categories Select -->
            <div class="mb-4">
                <select v-model="post.category_id">
                    <option value="null">Выберите категорию</option>
                    <option v-for="category in categories" :value="category.id">{{ category.title }}</option>
                </select>
            </div>

            <!-- Image Path -->
            <div class="mb-4">
                <label for="image_path" class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                <input ref="image_input" @change="setImage"
                    type="file"
                    class="w-full border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="image">
            </div>

            <!-- Submit Button -->
            <div class="mt-6">
                <button @click.prevent="storePost"
                    class="w-full px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create Post
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
