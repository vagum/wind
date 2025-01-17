<template>
    <ResourceCreate
        resource-name="comments"
        submit-route="admin.comments.store"
        :fields="[
            // { key: 'title', label: 'Title', type: 'text', placeholder: 'Enter title' },
            { key: 'content', label: 'Content', type: 'textarea', placeholder: 'Enter content' },
        ]"
        :initial-values="{ title: '' }"
    />
</template>

<script>
import ResourceCreate from "@/Pages/Admin/All/ResourceCreate.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceCreate },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>

