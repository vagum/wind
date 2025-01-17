<template>
    <ResourceShow
        resource-name="tags"
        :resource="tag"
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
        tag: Object,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
