<template>
    <AdminLayout>
        <ContactMessagesList :messages="messages" />
    </AdminLayout>
</template>

<script setup>
defineProps({
    messages: Array
});

import AdminLayout from '../Components/AdminLayout.vue';
import ContactMessagesList from './ContactMessagesList.vue';
</script>