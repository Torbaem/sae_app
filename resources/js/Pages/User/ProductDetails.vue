<script setup>
import UserLayouts from "./Layouts/UserLayouts.vue";
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();
const product = ref(page.props.product);
const selectedImageIndex = ref(0);
const quantity = ref(1);
const showImageModal = ref(false);

// Computed property para la imagen principal
const mainImage = computed(() => {
    if (!product.value?.product_images?.length) {
        return 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png';
    }
    return `/${product.value.product_images[selectedImageIndex.value].image}`;
});

// Computed property para la imagen modal
const modalImage = computed(() => mainImage.value);

// Establecer imagen principal por índice
const setMainImage = (index) => {
    selectedImageIndex.value = index;
};

// Navegación de imágenes en el modal
const previousImage = () => {
    if (selectedImageIndex.value > 0) {
        selectedImageIndex.value--;
    }
};

const nextImage = () => {
    if (selectedImageIndex.value < (product.value?.product_images?.length - 1)) {
        selectedImageIndex.value++;
    }
};

// Manejador del modal
const toggleImageModal = () => {
    showImageModal.value = !showImageModal.value;
};

// Cerrar modal con tecla escape
const handleKeyDown = (event) => {
    if (event.key === 'Escape' && showImageModal.value) {
        toggleImageModal();
    }
    if (event.key === 'ArrowLeft' && showImageModal.value) {
        previousImage();
    }
    if (event.key === 'ArrowRight' && showImageModal.value) {
        nextImage();
    }
};

// Añadir al carrito con validación
const addToCart = async (product) => {
    if (quantity.value < 1 || quantity.value > product.quantity) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Cantidad no válida',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }

    router.post(route('cart.store', product), {
        data: { quantity: quantity.value },
        onSuccess: (page) => {
            if (page.props.flash.success) {
                Swal.fire({
                    toast: true,
                    icon: "success",
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    title: page.props.flash.success
                });
            }
        },
    });
};

// Actualizar cantidad con validación
const updateQuantity = (value) => {
    const newQuantity = quantity.value + value;
    if (newQuantity >= 1 && newQuantity <= product.value.quantity) {
        quantity.value = newQuantity;
    }
};

// Event listeners para el teclado
onMounted(() => {
    window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
    <user-layouts>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div v-if="product" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Galería de imágenes -->
                <div class="space-y-4">
                    <div 
                        class="relative aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-lg bg-gray-100 hover:bg-gray-200 transition"
                    >
                        <img
                            :src="mainImage"
                            :alt="product.title"
                            class="w-full h-full object-contain cursor-zoom-in transition duration-300 ease-in-out transform hover:scale-105"
                            @click="toggleImageModal"
                        />
                    </div>
                    
                    <!-- Miniaturas -->
                    <div class="grid grid-cols-4 gap-2">
                        <div
                            v-for="(image, index) in product.product_images"
                            :key="image.id"
                            class="relative aspect-w-1 aspect-h-1 overflow-hidden rounded-md cursor-pointer"
                            :class="{
                                'ring-2 ring-blue-500': selectedImageIndex === index,
                                'hover:ring-2 hover:ring-gray-300': selectedImageIndex !== index
                            }"
                            @click="setMainImage(index)"
                        >
                            <img
                                :src="`/${image.image}`"
                                :alt="`${product.title} - imagen ${index + 1}`"
                                class="w-full h-full object-contain hover:opacity-75 transition"
                            />
                        </div>
                    </div>
                </div>

                <!-- Información del producto -->
                <div class="space-y-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ product.title }}</h1>
                    <div class="space-y-2">
                        <p class="text-2xl font-semibold text-gray-900">${{ Number(product.price).toFixed(2) }}</p>
                        <p class="text-sm text-gray-500" :class="{ 'text-red-500': product.quantity < 10 }">
                            Stock disponible: {{ product.quantity }}
                        </p>
                    </div>

                    <!-- Control de cantidad -->
                    <div class="flex items-center space-x-4">
                        <label class="text-gray-700 font-medium">Cantidad:</label>
                        <div class="flex items-center border border-gray-300 rounded-md shadow-sm">
                            <button 
                                class="px-3 py-1 border-r border-gray-300 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                @click="updateQuantity(-1)"
                                :disabled="quantity <= 1"
                            >
                                -
                            </button>
                            <input 
                                type="number" 
                                v-model="quantity" 
                                class="w-16 text-center py-1 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                :max="product.quantity"
                                min="1"
                            >
                            <button 
                                class="px-3 py-1 border-l border-gray-300 hover:bg-gray-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                @click="updateQuantity(1)"
                                :disabled="quantity >= product.quantity"
                            >
                                +
                            </button>
                        </div>
                    </div>

                    <!-- Botón de agregar al carrito -->
                    <div class="space-y-3">
                        <button 
                            @click="addToCart(product)"
                            class="w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 active:bg-blue-800 transition-colors duration-200 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            Agregar al carrito
                        </button>
                    </div>

                    <!-- Información adicional -->
                    <div class="border-t border-gray-200 pt-4">
                        <dl class="space-y-2">
                            <div class="flex">
                                <dt class="font-medium text-gray-500 w-24">Categoría:</dt>
                                <dd class="text-gray-900">{{ product.category?.name || 'No especificada' }}</dd>
                            </div>
                            <div class="flex">
                                <dt class="font-medium text-gray-500 w-24">Marca:</dt>
                                <dd class="text-gray-900">{{ product.brand?.name || 'No especificada' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Descripción del producto -->
            <div class="mt-12 bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-2xl font-bold mb-4">Descripción del producto</h2>
                <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ product?.description || 'Sin descripción disponible' }}</p>
            </div>

            <!-- Modal de imagen mejorado -->
            <div v-if="showImageModal" 
                class="fixed inset-0 z-50 overflow-hidden bg-black bg-opacity-90 flex items-center justify-center"
                @click.self="toggleImageModal">
                <div class="relative max-w-6xl w-full mx-auto p-4">
                    <!-- Botón cerrar -->
                    <button 
                        @click="toggleImageModal" 
                        class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors z-50"
                    >
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Botones de navegación -->
                    <button 
                        v-if="selectedImageIndex > 0"
                        @click="previousImage" 
                        class="absolute left-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors"
                    >
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <button 
                        v-if="selectedImageIndex < (product?.product_images?.length - 1)"
                        @click="nextImage" 
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-white hover:text-gray-300 transition-colors"
                    >
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Imagen -->
                    <img 
                        :src="modalImage"
                        :alt="product?.title"
                        class="max-h-[85vh] w-auto mx-auto object-contain"
                    />

                    <!-- Indicador de imagen actual -->
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-white text-sm">
                        {{ selectedImageIndex + 1 }} / {{ product?.product_images?.length }}
                    </div>
                </div>
            </div>
        </div>
    </user-layouts>
</template>

<style scoped>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
input[type="number"] {
    -moz-appearance: textfield;
}
</style>