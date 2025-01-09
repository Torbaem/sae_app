<script setup>
import { computed, reactive,watch } from 'vue'

import UserLayouts from './Layouts/UserLayouts.vue';
import { router, usePage } from '@inertiajs/vue3';

defineProps({
    userAddress: Object
})

const carts = computed(() => usePage().props.cart.data.items)
const products = computed(() => usePage().props.cart.data.products)
const total = computed(() => usePage().props.cart.data.total)
const itemId = (id) => carts.value.findIndex((item) => item.product_id === id);

// Watcher para productos
watch(products, (newProducts) => {
    if (!newProducts || newProducts.length === 0) {
        // Redirigir al home cuando no hay productos
        router.visit(route('home'));
    }
}, { deep: true })

// Definir las opciones de países
const countryOptions = [
    { name: 'México', code: 'MEX' },
    { name: 'Estados Unidos', code: 'USA' },
    { name: 'Canadá', code: 'CAN' }
]

// Definir las opciones de tipo de dirección
const addressTypes = [
    { name: 'Casa', value: 'Casa' },
    { name: 'Trabajo', value: 'Trabajo' }
]

const form = reactive({
    address1: null,
    state: null,
    city: null,
    zipcode: null,
    country_code: null,
    type: null,

})
const formFilled = computed(()=>{
   return (form.address1 !== null &&
    form.state !== null &&
    form.city !== null &&
    form.zipcode !== null &&
    form.country_code !== null &&
    form.type !== null )
})



const update = (product, quantity) =>
    router.patch(route('cart.update', product), {
        quantity,
    });
//remove form cart 
const remove = (product) => {
    router.delete(route('cart.delete', product), {
        preserveScroll: true,
        onSuccess: (page) => {
        },
        onError: (errors) => {
            console.error('Error al eliminar el producto:', errors);
        }
    });
};
//confirm order 

function submit() {
    router.visit(route('checkout.store'), {
        method: 'post',
        data: {
            carts: usePage().props.cart.data.items,
            products: usePage().props.cart.data.products,
            total: usePage().props.cart.data.total,
            address: form
        }
    })
}



</script>

<template>
    <UserLayouts>
        <section class="text-gray-600 body-font relative">
            <div class="container px-4 sm:px-5 py-8 sm:py-24 mx-auto flex flex-col lg:flex-row">
                <!-- Contenedor del carrito -->
                <div class="w-full lg:w-2/3 lg:pr-8 mb-8 lg:mb-0">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- Vista móvil de productos -->
                        <div class="block lg:hidden">
                            <div v-for="product in products" :key="product.id" 
                                 class="border-b last:border-b-0 p-4">
                                <div class="flex items-center space-x-4">
                                    <!-- Imagen del producto -->
                                    <div class="flex-shrink-0 w-24 h-24">
                                        <img v-if="product.product_images.length > 0"
                                            :src="`/${product.product_images[0].image}`"
                                            :alt="product.title"
                                            class="w-full h-full object-cover rounded">
                                        <img v-else
                                            src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                            :alt="product.title"
                                            class="w-full h-full object-cover rounded">
                                    </div>
                                    <!-- Detalles del producto -->
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900 mb-1">{{ product.title }}</h3>
                                        <p class="text-lg font-semibold text-gray-900 mb-2">${{ product.price }}</p>
                                        <!-- Controles de cantidad -->
                                        <div class="flex items-center space-x-2 mb-2">
                                            <button @click.prevent="update(product, carts[itemId(product.id)].quantity - 1)"
                                                :disabled="carts[itemId(product.id)].quantity <= 1"
                                                class="w-8 h-8 flex items-center justify-center rounded-full border">
                                                <span class="sr-only">Reducir cantidad</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                                </svg>
                                            </button>
                                            <input type="number"
                                                v-model="carts[itemId(product.id)].quantity"
                                                class="w-16 text-center border rounded-lg"
                                                min="1">
                                            <button @click.prevent="update(product, carts[itemId(product.id)].quantity + 1)"
                                                class="w-8 h-8 flex items-center justify-center rounded-full border">
                                                <span class="sr-only">Aumentar cantidad</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <!-- Botón eliminar -->
                                        <button @click="remove(product)"
                                            class="text-red-600 text-sm font-medium hover:text-red-700">
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vista desktop de productos (tabla) -->
                        <div class="hidden lg:block">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Producto
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Cantidad
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Precio
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="product in products" :key="product.id">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-16 w-16 flex-shrink-0">
                                                    <img v-if="product.product_images.length > 0"
                                                        :src="`/${product.product_images[0].image}`"
                                                        :alt="product.title"
                                                        class="h-16 w-16 object-cover rounded">
                                                    <img v-else
                                                        src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png"
                                                        :alt="product.title"
                                                        class="h-16 w-16 object-cover rounded">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="font-medium text-gray-900">{{ product.title }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <button @click.prevent="update(product, carts[itemId(product.id)].quantity - 1)"
                                                    :disabled="carts[itemId(product.id)].quantity <= 1"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full border">-</button>
                                                <input type="number"
                                                    v-model="carts[itemId(product.id)].quantity"
                                                    class="w-16 text-center border rounded-lg">
                                                <button @click.prevent="update(product, carts[itemId(product.id)].quantity + 1)"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full border">+</button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-900">${{ product.price }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="remove(product)"
                                                class="text-red-600 hover:text-red-900">Eliminar</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Contenedor del formulario -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Resumen de la orden</h2>
                        <p class="text-2xl font-bold mb-6">Total: ${{ total }}</p>

                        <!-- Dirección existente -->
                        <div v-if="userAddress" class="mb-6">
                            <h3 class="font-medium text-gray-900 mb-2">Dirección de envío</h3>
                            <p class="text-gray-600">
                                {{ userAddress.address1 }}, {{ userAddress.city }}, {{ userAddress.zipcode }}
                            </p>
                            <p class="text-sm text-gray-500 mt-2">O añade una nueva dirección abajo</p>
                        </div>

                        <!-- Formulario de dirección -->
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                <input type="text" v-model="form.address1"
                                    class="w-full rounded-lg border-gray-300 shadow-sm">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Ciudad</label>
                                    <input type="text" v-model="form.city"
                                        class="w-full rounded-lg border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <input type="text" v-model="form.state"
                                        class="w-full rounded-lg border-gray-300 shadow-sm">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Código Postal</label>
                                <input type="text" v-model="form.zipcode"
                                    class="w-full rounded-lg border-gray-300 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">País</label>
                                <select v-model="form.country_code"
                                    class="w-full rounded-lg border-gray-300 shadow-sm">
                                    <option value="">Selecciona un país</option>
                                    <option value="MEX">México</option>
                                    <option value="USA">Estados Unidos</option>
                                    <option value="CAN">Canadá</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Dirección</label>
                                <select v-model="form.type"
                                    class="w-full rounded-lg border-gray-300 shadow-sm">
                                    <option value="">Selecciona el tipo</option>
                                    <option value="home">Casa</option>
                                    <option value="work">Trabajo</option>
                                </select>
                            </div>

                            <button type="submit"
                                :class="[formFilled || userAddress ? 
                                    'bg-indigo-600 hover:bg-indigo-700' : 
                                    'bg-gray-400 cursor-not-allowed',
                                    'w-full py-3 px-4 rounded-lg text-white font-medium transition-colors']">
                                {{ formFilled || userAddress ? 'Proceder al pago' : 'Añade una dirección para continuar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </UserLayouts>
</template>