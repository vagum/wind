<template>
    <ResourceCreate
        resource-name="users"
        create-name="User"
        submit-route="admin.users.store"
        :fields="[
            { key: 'name', label: 'Name', type: 'text', placeholder: 'Enter name' },
            { key: 'email', label: 'Email', type: 'text', placeholder: 'Enter email' },
            { key: 'password', label: 'Password', type: 'password', placeholder: 'Enter password' },
            {
                key: 'role_id',
                label: 'Role',
                type: 'select',
                placeholder: 'Choose a role',
                options: roles // массив [{ id: 1, title: 'Admin' }, ...]
            }
        ]"
        :initial-values="{ name: '', email: '', password: '', role_id: null }"
    />
</template>

<script>
import ResourceCreate from "@/Pages/Admin/All/ResourceCreate.vue";
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";

export default {
    components: { ResourceCreate },
    layout: AdminLayout, // Здесь подключаем макет
    props: {
        roles: Array,
    },
};
</script>

