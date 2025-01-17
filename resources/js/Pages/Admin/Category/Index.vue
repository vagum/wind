<template>
  <ResourceIndex
      :resources="categories"
      :columns="[
            { key: 'id', label: 'ID' },
            { key: 'title', label: 'Title' }
        ]"
      resource-name="categories"
  />
</template>

<script>
import ResourceIndex from "@/Pages/Admin/All/ResourceIndex.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
  components: { ResourceIndex },
  props: {
    categories: Array,
  },
  layout: AdminLayout, // Здесь подключаем макет
};
</script>
