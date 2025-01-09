<template>
    <div class="antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Navbar -->
        <Navbar
            @toggle-sidebar="toggleSidebar"
            class="fixed top-0 z-30 w-full dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700"
        />
        <!-- Sidebar -->
        <Sidebar
            :is-open="sidebarOpen"
            class="fixed left-0 z-20 h-full"
            :class="[
                'transition-transform duration-300 ease-in-out',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'md:translate-x-0' // Siempre visible en escritorio
            ]"
        />
        <!-- Main Content -->
        <main class="p-4 md:ml-64 h-auto pt-20">
            <slot />
        </main>

        <!-- Overlay para móvil -->
        <div
            v-show="sidebarOpen"
            class="fixed inset-0 bg-gray-900 bg-opacity-50 z-10 transition-opacity duration-300 md:hidden"
            @click="toggleSidebar"
        ></div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { initFlowbite } from 'flowbite';
import Navbar from './Navbar.vue';
import Sidebar from './Sidebar.vue';

const sidebarOpen = ref(false); // Sidebar inicialmente cerrado en móviles

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value; // Alterna el estado del sidebar
};

const handleResize = () => {
    if (window.innerWidth >= 768) {
        sidebarOpen.value = false; // Cierra el sidebar automáticamente si cambia a escritorio
    }
};

onMounted(() => {
    initFlowbite();
    window.addEventListener('resize', handleResize);
    handleResize(); // Verifica el tamaño inicial de la pantalla
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});
</script>
