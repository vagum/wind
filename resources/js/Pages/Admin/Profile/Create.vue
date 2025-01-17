<template>
    <ResourceCreate
        resource-name="profiles"
        create-name="Profile"
        submit-route="admin.profiles.store"
        :fields="[
            { key: 'name', label: 'Name', type: 'text', placeholder: 'Enter name' },
            { key: 'gender', label: 'Gender', type: 'text', placeholder: 'Enter gender' },
            { key: 'birthed_at', label: 'Birthday', type: 'date' },
            { key: 'address', label: 'Address', type: 'text', placeholder: 'Enter address' },
            { key: 'phone', label: 'Phone', type: 'text', placeholder: 'Enter phone' },
            { key: 'description', label: 'Description', type: 'textarea', placeholder: 'Enter description' },
        ]"
        :initial-values="{ name: '', gender: '', birthed_at: '', address: '', phone: '', description: '' }"
        :image-field="{ show: true, multiple: false }"
    />
    <!-- Или для множественной загрузки:  :image-field="{ show: true, multiple: true }" -->

</template>

<script>
import ResourceCreate from "@/Pages/Admin/All/ResourceCreate.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceCreate },
    layout: AdminLayout,
    // props: {
    //     roles: Array,
    // },
};
</script>
