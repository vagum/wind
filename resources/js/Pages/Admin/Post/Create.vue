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
            entries: {
                post: {
                    category_id: null,
                    image: null,
                    title: '',
                    description: '',
                    content: '',
                    published_at: ''
                },
                tags: '',
            },
            errors: {}, // Для хранения ошибок
            isSuccess: false,
            createdPost: null,
            // Специальный флаг, чтобы «игнорировать»
            // изменения при сбросе формы
            ignoreEntriesWatch: false,
        }
    },

    methods: {
        storePost() {
            axios.post(route('admin.posts.store'), this.entries, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then(res => {
                    this.errors = {}; // Очистка ошибок

                    // Сохраняем данные созданного поста
                    this.createdPost = res.data;

                    // Перед сбросом формы включаем флаг игнорирования
                    this.ignoreEntriesWatch = true;

                    // Очищаем форму (программное изменение)
                    this.entries = {
                        post: {
                            category_id: null,
                            image: null,
                            title: '',
                            description: '',
                            content: '',
                            published_at: ''
                        },
                        tags: '',
                    };

                    // Показываем сообщение об успехе
                    this.isSuccess = true;

                    // Сбрасываем input типа file
                    this.$refs.image_input.value = null;
                })
                .catch((error) => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors; // Сохраняем ошибки
                    } else {
                        console.error('Ошибка при сохранении поста:', error);
                    }
                });
        },

        setImage(e) {
            this.entries.post.image = e.target.files[0];
        },
    },

    watch: {
        // Глубокое наблюдение за объектом entries
        entries: {
            deep: true,
            handler(newVal, oldVal) {
                // Если включён флаг игнорирования,
                // значит изменяем форму «программно» во время очистки после сабмита
                if (this.ignoreEntriesWatch) {
                    // Снимаем флаг — следующая смена данных
                    // уже будет считаться пользовательской
                    this.ignoreEntriesWatch = false;
                    return;
                }
                // Если сообщение об успехе активно
                // и данные меняются именно пользователем — скрываем сообщение
                if (this.isSuccess) {
                    this.isSuccess = false;
                }
            }
        }
    }
}
</script>

<template>
    <div>
        <!-- Ссылка Назад -->
        <div class="mb-4">
            <Link :href="route('admin.posts.index')" class="text-blue-500 hover:underline">&laquo; Back</Link>
        </div>

        <!-- Форма -->
        <div class="p-6 bg-white border border-gray-200 shadow-lg rounded-lg">
            <!-- Сообщение об успехе -->
            <div v-if="isSuccess" class="mb-4 w-1/2 p-4 bg-green-200 text-green-800 font-medium rounded">
                Success!
                <Link :href="route('admin.posts.show', createdPost.id)">View post</Link>
            </div>

            <!-- Заголовок -->
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                <input
                    type="text"
                    v-model="entries.post.title"
                    class="w-1/2 border border-gray-300 rounded-lg p-2 text-gray-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors[`post.title`] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                    placeholder="Enter title"
                />
                <p v-if="errors[`post.title`]" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.title']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Описание -->
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <input
                    type="text"
                    v-model="entries.post.description"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors[`post.description`] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                    placeholder="Enter description"
                />
                <p v-if="errors[`post.description`]" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.description']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Контент -->
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                <textarea
                    v-model="entries.post.content"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors[`post.content`] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                    rows="5"
                    placeholder="Enter content"
                ></textarea>
                <p v-if="errors[`post.content`]" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.content']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Дата публикации -->
            <div class="mb-4">
                <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published At</label>
                <input
                    type="date"
                    v-model="entries.post.published_at"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors['post.published_at'] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                />
                <p v-if="errors[`post.published_at`]" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.published_at']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Теги -->
            <div class="mb-4">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <input
                    type="text"
                    v-model="entries.tags"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors[`post.tags`] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                    placeholder="Enter tags"
                />
                <p v-if="errors[`post.tags`]" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.tags']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Выбор категории -->
            <div class="mb-4">
                <select
                    v-model="entries.post.category_id"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors['post.category_id'] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                >
                    <option value="null">Выберите категорию</option>
                    <option
                        v-for="category in categories"
                        :key="category.id"
                        :value="category.id"
                    >
                        {{ category.title }}
                    </option>
                </select>
                <p v-if="errors['post.category_id']" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.category_id']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Загрузка изображения -->
            <div class="mb-4">
                <label
                    for="image_path"
                    class="block text-sm font-medium text-gray-700 mb-2"
                >
                    Upload Image
                </label>
                <input
                    ref="image_input"
                    @change="setImage"
                    type="file"
                    :class="['w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                     errors['post.image'] ? 'border-red-500 focus:ring-red-500' :
                      'border-gray-300 focus:ring-blue-500']"
                    placeholder="image"
                />
                <!-- Перебор ошибок для изображения -->
                <p v-if="errors['post.image']" class="text-red-500 text-sm mt-1" v-for="(error, index) in errors['post.image']" :key="index">
                    {{ error }}
                </p>
            </div>

            <!-- Кнопка отправки -->
            <div class="mt-6">
                <button
                    @click.prevent="storePost"
                    class="w-1/2 px-4 py-2 text-white bg-blue-500 rounded-lg
                           hover:bg-blue-600 focus:outline-none
                           focus:ring-2 focus:ring-blue-500"
                >
                    Create Post
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Ваши стили, если нужны */
</style>
