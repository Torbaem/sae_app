<template>
    <AdminLayout>
        <OrdersList 
            :orders="orders"
            :filters="filters"
        />
    </AdminLayout>
</template>

<script setup>
defineProps({
    orders: Array,
    filters: Object
})
import AdminLayout from '../Components/AdminLayout.vue'
import OrdersList from './OrdersList.vue'
</script>