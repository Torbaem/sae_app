<script setup>
import { ref, computed } from 'vue'
import UserLayouts from './Layouts/UserLayouts.vue'
import axios from 'axios'


const props = defineProps({
    orders: Array
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatPrice = (price) => {
    return Number(price).toLocaleString('es-MX', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })
}


const formatAddress = (address) => {
    if (!address) return 'No disponible'
    return `${address.address1}, ${address.city}, ${address.state}, ${address.zipcode}`
}

const getStatusClass = (status) => {
    const classes = {
        'Pendiente': 'bg-yellow-100 text-yellow-800',
        'en proceso': 'bg-blue-100 text-blue-800',
        'entregado': 'bg-green-100 text-green-800',
        'cancelado': 'bg-red-100 text-red-800',
        'unpaid': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

// Modificada para permitir marcar como entregado solo cuando está "en proceso"
const canMarkAsDelivered = (order) => {
    return order.shipping_status === 'en proceso' && order.payment_status === 'paid'
}

// Función para obtener la imagen del producto
const getImagePath = (item) => {
    if (item?.product?.image_url) {
        return item.product.image_url;
    }
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png";
};

// Computed property para filtrar órdenes no pagadas
const filteredOrders = computed(() => {
    return props.orders.filter(order => order.payment_status !== 'unpaid')
})

const updateStatus = async (order) => {
    try {
        await axios.put(`/orders/${order.id}/shipping-status`)
        order.shipping_status = 'Entregado'
    } catch (error) {
        console.error('Error al actualizar el estado:', error)
        alert(error.response?.data?.message || 'Error al actualizar el estado del pedido')
    }
}
</script>

<template>
    <UserLayouts>
        <div class="max-w-screen-xl py-8 mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
                Mis Pedidos
            </h2>

            <!-- Mensaje cuando no hay órdenes -->
            <div v-if="!orders.length" 
                 class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">
                    No tienes pedidos aún
                </h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    ¡Explora nuestro catálogo y realiza tu primera compra!
                </p>
                <a href="/products" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Ir a comprar
                </a>
            </div>

            <!-- Lista de pedidos -->
            <div v-else class="space-y-6">
                <div v-for="order in orders" :key="order.id" 
                     class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
                    <!-- Mensaje de error de pago si aplica -->
                    <div v-if="order.payment_status === 'unpaid'"
                         class="bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    El pedido no se podrá procesar debido a un error en el pago
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Cabecera del pedido -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 border-b dark:border-gray-600">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Pedido #{{ order.id }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Fecha: {{ formatDate(order.created_at) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="flex items-center gap-3">
                                    <span :class="getStatusClass(order.shipping_status)"
                                          class="px-3 py-1 rounded-full text-sm font-medium">
                                        {{ order.shipping_status }}
                                    </span>
                                    <button v-if="canMarkAsDelivered(order)"
                                            @click="updateStatus(order)"
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                                        Marcar como entregado
                                    </button>
                                </div>
                                <p class="mt-2 text-lg font-bold text-gray-900 dark:text-white">
                                    Total: ${{ formatPrice(order.total_price) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección de envío -->
                    <div class="p-4 border-b dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                            Dirección de envío
                        </h4>
                        <p class="text-gray-600 dark:text-gray-300 text-sm">
                            {{ formatAddress(order.user_address) }}
                        </p>
                    </div>

                    <!-- Lista de productos -->
                    <div class="p-4">
                        <div class="flow-root">
                            <div class="-mx-4 -my-2 overflow-x-auto">
                                <div class="inline-block min-w-full py-2 align-middle px-4">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white w-16">
                                                    <span class="sr-only">Imagen</span>
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                    Producto
                                                </th>
                                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-white">
                                                    Marca
                                                </th>
                                                <th class="px-4 py-3 text-right text-sm font-semibold text-gray-900 dark:text-white">
                                                    Precio
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                            <tr v-for="item in order.order_items" :key="item.id">
                                                <td class="px-4 py-4">
                                                    <img
                                                        :src="getImagePath(item)"
                                                        :alt="item.product.title"
                                                        class="w-20 h-20 object-cover object-fit-contain rounded" 
                                                    />
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white">
                                                    {{ item.product.title }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ item.product.brand.name }}
                                                </td>
                                                <td class="px-4 py-4 text-sm text-gray-900 dark:text-white text-right">
                                                    ${{ formatPrice(item.product.price) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayouts>
</template>