<template>
    <AdminLayout>
        <UserManagement :users="users"></UserManagement>
    </AdminLayout>
</template>

<script setup>
defineProps({
    users: Array
})
import AdminLayout from '../Components/AdminLayout.vue'
import UserManagement from './UserManagement.vue'
</script>