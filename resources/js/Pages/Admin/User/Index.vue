<template>
    <ResourceIndex
        :resources="users"
        :columns="[
            { key: 'id', label: 'ID' },
            { key: 'name', label: 'Name' },
            { key: 'email', label: 'Email' }
        ]"
        resource-name="users"
    />
</template>

<script>
import ResourceIndex from "@/Pages/Admin/All/ResourceIndex.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceIndex },
    props: {
        users: Array,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
