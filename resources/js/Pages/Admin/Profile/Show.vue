<template>
    <ResourceShow
        resource-name="profiles"
        :resource="profile"
        :fields="[
            { key: 'id', label: 'ID' },
            { key: 'name', label: 'Name' },
            { key: 'gender', label: 'Gender' },
            { key: 'phone', label: 'Phone' },
            { key: 'birthed_at', label: 'Birthed At' },
            { key: 'avatar', label: 'Avatar' },
            { key: 'description', label: 'Description' }
        ]"
    />
</template>

<script>
import ResourceShow from "@/Pages/Admin/All/ResourceShow.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceShow },
    props: {
        profile: Object,
    },
    layout: AdminLayout, // Здесь подключаем макет
};
</script>
