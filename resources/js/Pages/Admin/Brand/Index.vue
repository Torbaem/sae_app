<template>
    <AdminLayout>
        <BrandsList :brands="brands" />
    </AdminLayout>
</template>

<script setup>
defineProps({
    brands: Array
});

import AdminLayout from '../Components/AdminLayout.vue';
import BrandsList from './BrandsList.vue';
</script>