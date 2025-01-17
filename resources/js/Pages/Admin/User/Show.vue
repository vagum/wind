<template>
    <ResourceShow
        resource-name="users"
        :resource="user"
        :fields="[
            { key: 'id', label: 'ID' },
            { key: 'name', label: 'Name' },
            { key: 'email', label: 'Email' },
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
        user: Object,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
