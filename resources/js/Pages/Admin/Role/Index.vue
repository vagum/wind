<template>
    <ResourceIndex
        :resources="roles"
        :columns="[
            { key: 'id', label: 'ID' },
            { key: 'title', label: 'Title' }
        ]"
        resource-name="roles"
    />
</template>

<script>
import ResourceIndex from "@/Pages/Admin/All/ResourceIndex.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceIndex },
    props: {
        roles: Array,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
