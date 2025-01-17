<template>
    <ResourceIndex
        :resources="tags"
        :columns="[
            { key: 'id', label: 'ID' },
            { key: 'title', label: 'Title' }
        ]"
        resource-name="tags"
    />
</template>

<script>
import ResourceIndex from "@/Pages/Admin/All/ResourceIndex.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceIndex },
    props: {
        tags: Array,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
