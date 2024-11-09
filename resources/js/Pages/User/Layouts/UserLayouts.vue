<script setup>
import { onMounted } from 'vue'
import { initFlowbite } from 'flowbite'
import Header from './Header.vue';
import Footer from './Footer.vue';
import Hero from './Hero.vue';
import Nosotros from '../Nosotros.vue';
import Contactanos from '../Contactanos.vue';

// initialize components based on data attribute selectors
onMounted(() => {
    initFlowbite();
})



</script>
<template>
    <!-- header -->
    <Header></Header>
    <!-- end -->

    <!-- main content: inyecta el contenido de las páginas aquí -->
    <slot />
    <!-- end -->

    <!-- footer -->
    <Footer></Footer>
    <!-- end -->
</template>
