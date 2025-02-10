<script>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
    name: "Edit",
    layout: AdminLayout,

    components: {
        Link,
    },

    props: {
        post: Object,
        categories: {
            type: Array,
            required: true,
        },
    },

    data() {
        return {
            entries: {
                post: {
                    category_id: this.getCategoryIdByTitle(this.post.category_title) || null,
                    image_url: this.post.image_url || '',
                    title: this.post.title || '',
                    description: this.post.description || '',
                    content: this.post.content || '',
                    published_at: this.post.published_at
                        ? new Date(this.post.published_at).toISOString().split('T')[0]
                        : '',
                    image: null, // Новое поле для загрузки изображения
                },
                tags: this.post.tags_title ? this.post.tags_title.join(', ') : '',
            },
            originalEntries: {
                post: {
                    category_id: this.getCategoryIdByTitle(this.post.category_title) || null,
                    image_url: this.post.image_url || '',
                    title: this.post.title || '',
                    description: this.post.description || '',
                    content: this.post.content || '',
                    published_at: this.post.published_at
                        ? new Date(this.post.published_at).toISOString().split('T')[0]
                        : '',
                },
            },
            originalTags: this.post.tags_title ? this.post.tags_title.join(', ') : '',
            errors: {},        // Ошибки валидации
            isSuccess: false,  // Флаг успешного обновления
            editedPost: null,  // Обновлённые данные поста
            errorMessage: '',  // Сообщение об ошибке (например, неавторизованный доступ)
            ignoreEntriesWatch: false,
        };
    },

    watch: {
        // Следим за изменениями в prop 'post' для обновления данных
        post: {
            handler(newPost) {
                this.entries.post = {
                    category_id: this.getCategoryIdByTitle(newPost.category_title) || null,
                    image_url: newPost.image_url || '',
                    title: newPost.title || '',
                    description: newPost.description || '',
                    content: newPost.content || '',
                    published_at: newPost.published_at
                        ? new Date(newPost.published_at).toISOString().split('T')[0]
                        : '',
                    image: null,
                };
                this.entries.tags = newPost.tags_title ? newPost.tags_title.join(', ') : '';
                this.originalEntries.post = { ...this.entries.post };
                this.originalTags = newPost.tags_title ? newPost.tags_title.join(', ') : '';
            },
            immediate: true,
            deep: true,
        },
        // При изменении любых полей формы сбрасываем сообщение об успехе и ошибке
        entries: {
            deep: true,
            handler(newVal, oldVal) {
                if (this.ignoreEntriesWatch) {
                    this.ignoreEntriesWatch = false;
                    return;
                }
                if (this.isSuccess) {
                    this.isSuccess = false;
                }
                if (this.errorMessage) {
                    this.errorMessage = '';
                }
            },
        },
    },

    methods: {
        getCategoryIdByTitle(title) {
            const category = this.categories.find(cat => cat.title === title);
            return category ? category.id : null;
        },

        /**
         * Сравнение строк тегов без учёта порядка элементов.
         * @param {String} str1
         * @param {String} str2
         * @returns {Boolean}
         */
        tagsEqual(str1, str2) {
            const a1 = str1.split(',').map(tag => tag.trim()).filter(tag => tag);
            const a2 = str2.split(',').map(tag => tag.trim()).filter(tag => tag);
            if (a1.length !== a2.length) return false;
            const sorted1 = [...a1].sort();
            const sorted2 = [...a2].sort();
            for (let i = 0; i < sorted1.length; i++) {
                if (sorted1[i] !== sorted2[i]) return false;
            }
            return true;
        },

        /**
         * Обработка отправки формы редактирования поста.
         */
        editPost() {
            const formData = new FormData();

            // Добавляем обязательное поле title
            const requiredPostFields = ['title'];
            requiredPostFields.forEach(field => {
                formData.append(`post[${field}]`, this.entries.post[field]);
            });

            // Если выбрано новое изображение – добавляем его
            if (this.entries.post.image) {
                formData.append('post[image]', this.entries.post.image);
            }

            // Обработка тегов: отправляем только если изменились
            const currentTags = this.entries.tags
                .split(',')
                .map(tag => tag.trim())
                .filter(tag => tag);
            const tagsHaveChanged = !this.tagsEqual(this.entries.tags, this.originalTags);
            if (tagsHaveChanged) {
                const tagsString = currentTags.join(', ');
                formData.append('tags', tagsString);
            }

            // Используем метод PATCH
            formData.append('_method', 'patch');

            // Вывод данных формы для отладки
            console.log('FormData Entries:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Отправляем запрос на сервер
            axios
                .post(route('admin.posts.update', { post: this.post.id }), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                .then((res) => {
                    // Сбрасываем сообщение об ошибке и ошибки валидации
                    this.errorMessage = '';
                    this.errors = {};

                    const updatedCategoryId = this.getCategoryIdByTitle(res.data.category_title);
                    this.entries.post = {
                        ...this.entries.post,
                        category_id: updatedCategoryId,
                        title: res.data.title,
                        description: res.data.description,
                        content: res.data.content,
                        published_at: res.data.published_at
                            ? new Date(res.data.published_at).toISOString().split('T')[0]
                            : '',
                        image_url: res.data.image_url,
                        image: null, // Сбрасываем выбранное изображение
                    };

                    this.entries.tags = res.data.tags_title ? res.data.tags_title.join(', ') : '';
                    this.originalTags = res.data.tags_title ? res.data.tags_title.join(', ') : '';
                    this.originalEntries.post = { ...this.entries.post };

                    if (this.$refs.image_input) {
                        this.$refs.image_input.value = null;
                    }

                    this.ignoreEntriesWatch = true;
                    this.isSuccess = true;
                    this.editedPost = res.data;
                })
                .catch((error) => {
                    if (error.response && error.response.data.errors) {
                        this.errors = error.response.data.errors;
                    } else if (error.response && error.response.data.message) {
                        this.errorMessage = error.response.data.message;
                    } else {
                        console.error('Ошибка при сохранении поста:', error);
                    }
                });
        },

        setImage(e) {
            const file = e.target.files[0];
            console.log('Selected file:', file);
            this.entries.post.image = file;
        },
    },
};
</script>

<template>
    <div>
        <!-- Ссылка "Назад" -->
        <div class="mb-4">
            <Link :href="route('admin.posts.index')" class="text-blue-500 hover:underline">&laquo; Back</Link>
        </div>

        <div class="p-6 bg-white border border-gray-200 shadow-lg rounded-lg">
            <!-- Сообщение об успешном обновлении -->
            <div v-if="isSuccess" class="mb-4 w-1/2 p-4 bg-green-200 text-green-800 font-medium rounded">
                Success!
                <Link :href="route('admin.posts.show', editedPost.id)">View post</Link>
            </div>

            <!-- Сообщение об ошибке -->
            <div v-if="errorMessage" class="mb-4 w-1/2 p-4 bg-red-200 text-red-800 font-medium rounded">
                {{ errorMessage }}
            </div>

            <!-- Текущее изображение -->
            <div v-if="entries.post.image_url" class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                <img :src="entries.post.image_url" :alt="entries.post.title"
                     class="rounded-lg shadow-md w-1/2"/>
            </div>

            <!-- Поля формы -->
            <div v-for="(field, index) in ['title', 'description', 'content']" :key="index" class="mb-4">
                <label :for="field" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ field.charAt(0).toUpperCase() + field.slice(1) }}
                </label>
                <input
                    v-if="field !== 'content'"
                    type="text"
                    v-model="entries.post[field]"
                    :class="[
                        'w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                        errors[`post.${field}`] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                    ]"
                    :placeholder="'Enter ' + field"
                />
                <textarea
                    v-else
                    v-model="entries.post[field]"
                    :class="[
                        'w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                        errors[`post.${field}`] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                    ]"
                    rows="5"
                    :placeholder="'Enter ' + field"
                ></textarea>
                <p
                    v-if="errors[`post.${field}`]"
                    class="text-red-500 text-sm mt-1"
                    v-for="(error, idx) in errors[`post.${field}`]"
                    :key="idx"
                >
                    {{ error }}
                </p>
            </div>

            <!-- Дата публикации -->
            <div class="mb-4">
                <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">Published At</label>
                <input
                    type="date"
                    v-model="entries.post.published_at"
                    :class="[
                        'w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                        errors['post.published_at'] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                    ]"
                />
                <p
                    v-if="errors['post.published_at']"
                    class="text-red-500 text-sm mt-1"
                    v-for="(error, idx) in errors['post.published_at']"
                    :key="idx"
                >
                    {{ error }}
                </p>
            </div>

            <!-- Редактирование тегов -->
            <div class="mb-4">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                <input
                    type="text"
                    v-model="entries.tags"
                    :class="[
                        'w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                        errors['tags'] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                    ]"
                    placeholder="Enter tags separated by commas"
                />
                <p
                    v-if="errors['tags']"
                    class="text-red-500 text-sm mt-1"
                    v-for="(error, idx) in errors['tags']"
                    :key="idx"
                >
                    {{ error }}
                </p>
            </div>

            <!-- Выбор категории -->
            <div class="mb-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select
                    v-model="entries.post.category_id"
                    :class="[
                        'w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                        errors['post.category_id'] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                    ]"
                >
                    <option value="">Выберите категорию</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.title }}
                    </option>
                </select>
                <p
                    v-if="errors['post.category_id']"
                    class="text-red-500 text-sm mt-1"
                    v-for="(error, idx) in errors['post.category_id']"
                    :key="idx"
                >
                    {{ error }}
                </p>
            </div>

            <!-- Загрузка нового изображения -->
            <div class="mb-4">
                <label for="image_path" class="block text-sm font-medium text-gray-700 mb-2">Upload Image</label>
                <input
                    ref="image_input"
                    @change="setImage"
                    type="file"
                    accept="image/*"
                    :class="[
                        'w-1/2 border rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2',
                        errors['post.image'] ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'
                    ]"
                />
                <p
                    v-if="errors['post.image']"
                    class="text-red-500 text-sm mt-1"
                    v-for="(error, idx) in errors['post.image']"
                    :key="idx"
                >
                    {{ error }}
                </p>
            </div>

            <!-- Кнопка отправки формы -->
            <div class="mt-6">
                <button
                    @click.prevent="editPost"
                    class="w-1/2 px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    Edit Post
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Добавьте стили по необходимости */
</style>
