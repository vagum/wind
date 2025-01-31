<script>
import {Link} from "@inertiajs/vue3";

export default {
    name: "ClientLayout" ,
    components: {Link}
}
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Header -->
        <div class="p-4 bg-white text-gray-800 shadow-md mx-4">
            <header class="text-center text-2xl font-semibold tracking-wide">
                HEADER
            </header>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col lg:flex-row">
            <!-- Main Article -->
            <article class="w-full bg-white shadow-lg rounded-lg p-6 m-4">
                <slot />
            </article>
        </div>
    </div>
</template>

<style scoped>

</style>
