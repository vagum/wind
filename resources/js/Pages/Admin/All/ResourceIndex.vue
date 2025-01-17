<template>
    <div class="overflow-x-auto">
        <div class="mb-2">
            <Link :href="route(`admin.${resourceName}.create`)" class="text-blue-500 hover:underline">
                Create {{ resourceName }}
            </Link>
        </div>

        <table class="min-w-full table-fixed bg-white border border-gray-200 rounded-lg">
            <thead>
            <tr class="bg-gray-100 border-b">
                <th
                    v-for="column in columns"
                    :key="column.key"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider"
                >
                    {{ column.label }}
                </th>
                <th class="px-6 py-3 text-xs font-medium text-gray-700 uppercase tracking-wider text-right">
                    Actions
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="resource in resources" :key="resource.id" class="border-b hover:bg-gray-50">
                <!-- Динамическая генерация ячеек -->
                <td
                    v-for="(column, index) in columns"
                    :key="column.key"
                    class="px-6 py-3 text-gray-700"
                >
                    <!-- Если это вторая колонка -->
                    <Link
                        v-if="index === 1"
                        :href="route(`admin.${resourceName}.show`, resource.id)"
                        :class="column.key === 'name' ? 'text-blue-500 hover:underline whitespace-nowrap' : 'text-blue-500 hover:underline break-words'"
                    >
                        {{ resource[column.key] }}
                    </Link>
                    <!-- Остальные колонки -->
                    <span v-else>
                            {{ resource[column.key] }}
                        </span>
                </td>

                <td class="px-6 py-3 text-sm text-gray-700 text-right">
                    <div class="flex justify-end space-x-2">
                        <Link
                            :href="route(`admin.${resourceName}.show`, resource.id)"
                            class="px-4 py-1 text-white bg-blue-500 rounded hover:bg-blue-600"
                        >
                            Edit
                        </Link>
                        <button
                            @click="deleteResource(resource.id)"
                            class="px-4 py-1 text-white bg-red-500 rounded hover:bg-red-600"
                        >
                            Delete
                        </button>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
import { Link } from "@inertiajs/vue3";

export default {
    props: {
        resources: Array,
        columns: Array,
        resourceName: String,
    },
    components: {Link},
    // methods: {
    //     deleteResource(id) {
    //         console.log(`Delete ${this.resourceName} with ID: ${id}`);
    //     },
    // },
};
</script>
