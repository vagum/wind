<template>
    <ResourceIndex
        :resources="comments"
        :columns="[
            { key: 'id', label: 'ID' },
            { key: 'content', label: 'Content' }
        ]"
        resource-name="comments"
    />
</template>

<script>
import ResourceIndex from "@/Pages/Admin/All/ResourceIndex.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceIndex },
    props: {
        comments: Array,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
