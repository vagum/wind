<template>
    <ResourceShow
        resource-name="roles"
        :resource="role"
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
        role: Object,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
