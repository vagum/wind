<template>
    <ResourceIndex
        :resources="profiles"
        :columns="[
            { key: 'id', label: 'ID' },
            { key: 'name', label: 'Name' },
            { key: 'gender', label: 'Gender' },
            { key: 'phone', label: 'Phone' },
            { key: 'birthed_at', label: 'Birthed' },
            { key: 'description', label: 'Description' },
        ]"
        resource-name="profiles"
    />
</template>

<script>
import ResourceIndex from "@/Pages/Admin/All/ResourceIndex.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceIndex },
    props: {
        profiles: Array,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
