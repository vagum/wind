<script>
import { Link } from "@inertiajs/vue3";

export default {
    name: "ResourceShow",

    props: {
        resourceName: String, // Название сущности (например, 'posts', 'categories')
        resource: Object, // Данные сущности для отображения
        fields: Array, // Поля, которые нужно отобразить [{ key: 'title', label: 'Title' }, { key: 'content', label: 'Content' }]
    },

    components: {
        Link,
    },
};
</script>

<template>
    <div>
        <!-- Back Link -->
        <div class="mb-4">
            <Link :href="route(`admin.${resourceName}.index`)" class="text-blue-500 hover:underline flex items-center">&laquo; Back</Link>
        </div>

        <!-- Resource Details -->
        <div class="p-6 bg-white border border-gray-200 shadow-lg rounded-lg">
            <div v-for="field in fields" :key="field.key" class="mb-6">
                <!-- Field Label -->
                <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">
                    {{ field.label }}
                </div>
                <!-- Field Value -->
                <div class="text-base text-gray-900 bg-gray-50 rounded-lg p-3 shadow-sm">
                    {{ resource[field.key] || 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
</style>
