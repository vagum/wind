<template>
    <div>
        <!-- Back Link -->
        <div class="mb-4">
            <Link :href="route(`admin.${resourceName}.index`)" class="text-blue-500 hover:underline">
                &laquo; Back
            </Link>
        </div>

        <!-- Form Container -->
        <div class="p-6 bg-white border border-gray-200 shadow-lg rounded-lg">
            <form @submit.prevent="storeResource" enctype="multipart/form-data">

                <!-- Цикл по всем полям -->
                <div v-for="field in fields" :key="field.key" class="mb-4">
                    <label
                        :for="field.key"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        {{ field.label }}
                    </label>

                    <!-- input для типов, отличных от textarea и select -->
                    <input
                        v-if="field.type !== 'textarea' && field.type !== 'select'"
                        :type="field.type"
                        v-model="resource[field.key]"
                        :placeholder="field.placeholder"
                        :id="field.key"
                        class="w-1/2 border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />

                    <!-- textarea -->
                    <textarea
                        v-else-if="field.type === 'textarea'"
                        v-model="resource[field.key]"
                        :placeholder="field.placeholder"
                        :id="field.key"
                        class="w-1/2 border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        rows="5"
                    ></textarea>

                    <!-- select -->
                    <select
                        v-else-if="field.type === 'select' && field.options && field.options.length"
                        v-model="resource[field.key]"
                        :id="field.key"
                        class="w-1/4 border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="null">
                            {{ field.placeholder || 'Select an option' }}
                        </option>
                        <option
                            v-for="option in field.options"
                            :key="option.id"
                            :value="option.id"
                        >
                            {{ option.title }}
                        </option>
                    </select>

                    <!-- Если options для select отсутствуют -->
                    <div
                        v-else-if="field.type === 'select'"
                        class="text-gray-500 italic"
                    >
                        Нет данных для выбора
                    </div>
                </div>

                <!-- Поле для загрузки изображения(й) – отображается, если передан пропс imageField.show -->
                <div v-if="imageField && imageField.show" class="mb-4">
                    <label
                        for="image-upload"
                        class="block text-sm font-medium text-gray-700 mb-2"
                    >
                        {{ imageField.multiple ? 'Upload Images' : 'Upload Image' }}
                    </label>
                    <input
                        id="image-upload"
                        ref="image_input"
                        @change="setImage"
                        type="file"
                        :multiple="imageField.multiple"
                        class="w-1/2 border border-gray-300 rounded-lg p-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Submit Button -->
                <div class="mt-6">
                    <button
                        type="submit"
                        class="w-1/4 px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Create {{ createName }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import axios from "axios";

export default {
    name: "ResourceCreate",
    props: {
        resourceName: {
            type: String,
            required: true
        },
        createName: {
            type: String,
            required: true
        },
        fields: {
            type: Array,
            default: () => []
        },
        initialValues: {
            type: Object,
            default: () => ({})
        },
        submitRoute: {
            type: String,
            required: true
        },
        // Пропс для настройки поля загрузки изображений.
        imageField: {
            type: Object,
            default: () => ({
                show: false,
                multiple: false
            })
        }
    },
    data() {
        return {
            // resource будет содержать все текстовые данные, а также изображения
            resource: { ...this.initialValues },
            // Для файлов используем отдельное хранилище
            images: this.imageField.multiple ? [] : null,
        };
    },
    methods: {
        async storeResource() {
            const formData = new FormData();

            // Добавляем все текстовые/прочие поля
            for (const key in this.resource) {
                formData.append(key, this.resource[key]);
            }

            // Добавляем файлы, если поле активировано
            if (this.imageField && this.imageField.show) {
                if (this.imageField.multiple) {
                    // Добавляем все выбранные файлы
                    for (let i = 0; i < this.images.length; i++) {
                        formData.append('images[]', this.images[i]);
                    }
                } else if (this.images) {
                    // Один файл
                    formData.append('image', this.images);
                }
            }

            try {
                await axios.post(route(this.submitRoute), formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });
                // Очистка формы при успешной отправке
                this.resource = { ...this.initialValues };
                // Очистка файлового поля
                if (this.$refs.image_input) {
                    this.$refs.image_input.value = "";
                }
                // Очистка переменной для файлов
                if (this.imageField.multiple) {
                    this.images = [];
                } else {
                    this.images = null;
                }
            } catch (error) {
                console.error("Error storing resource:", error);
            }
        },
        setImage(e) {
            if (this.imageField.multiple) {
                this.images = Array.from(e.target.files);
            } else {
                this.images = e.target.files[0];
            }
        }
    },
    components: {
        Link,
    },
};
</script>

<style scoped>
/* Можно добавить стили по необходимости */
</style>
