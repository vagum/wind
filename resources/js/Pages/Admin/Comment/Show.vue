<template>
    <ResourceShow
        resource-name="comments"
        :resource="comment"
        :fields="[
            { key: 'id', label: 'ID' },
            { key: 'content', label: 'Content' },
            { key: 'created_at', label: 'Created At' },
        ]"
    />
</template>

<script>
import ResourceShow from "@/Pages/Admin/All/ResourceShow.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceShow },
    props: {
        comment: Object,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
