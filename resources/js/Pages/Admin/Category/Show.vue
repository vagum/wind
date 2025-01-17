<template>
    <ResourceShow
        resource-name="categories"
        :resource="category"
        :fields="[
            { key: 'id', label: 'ID' },
            { key: 'title', label: 'Title' },
        ]"
    />
</template>

<script>
import ResourceShow from "@/Pages/Admin/All/ResourceShow.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceShow },
    props: {
        category: Object,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
