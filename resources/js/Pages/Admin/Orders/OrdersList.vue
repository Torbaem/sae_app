<template>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow mb-8 p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="orderId" class="block text-sm font-medium text-gray-700">ID de Orden</label>
                        <input
                            type="text"
                            id="orderId"
                            v-model="searchFilters.orderId"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Buscar por ID"
                        >
                    </div>
                    <div>
                        <label for="shippingStatus" class="block text-sm font-medium text-gray-700">Estado de Envío</label>
                        <select
                            id="shippingStatus"
                            v-model="searchFilters.shippingStatus"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="en proceso">En Proceso</option>
                            <option value="cancelado">Cancelado</option>
                            <option value="entregado">Entregado</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-3">
                    <button
                        @click="clearFilters"
                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Limpiar Filtros
                    </button>
                    <button
                        @click="applyFilters"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Aplicar Filtros
                    </button>
                </div>
            </div>
            <!-- Primera tabla: Resumen de órdenes -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Resumen de Órdenes</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Número de Orden
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado de Pago
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado de Envío
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Fecha de Compra
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="order in displayedOrders" :key="order.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ order.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span :class="{
                                            'px-2 py-1 rounded-full text-xs font-medium': true,
                                            'bg-green-100 text-green-800': order.status === 'Pagado',
                                            'bg-red-100 text-red-800': order.status === 'Sin Pagar'
                                        }">
                                            {{ order.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select 
                                            v-model="order.shipping_status"
                                            @change="updateShippingStatus(order.id, $event)"
                                            :class="{
                                                'block w-full pl-3 pr-10 py-2 text-sm rounded-md': true,
                                                'bg-yellow-50 text-yellow-700 border-yellow-300': order.shipping_status === 'Pendiente',
                                                'bg-blue-50 text-blue-700 border-blue-300': order.shipping_status === 'en proceso',
                                                'bg-red-50 text-red-700 border-red-300': order.shipping_status === 'cancelado',
                                                'bg-green-50 text-green-700 border-green-300': order.shipping_status === 'entregado'
                                            }"
                                        >
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="en proceso">En Proceso</option>
                                            <option value="cancelado">Cancelado</option>
                                            <option value="entregado">Entregado</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ order.total_price }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ order.created_at }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Segunda tabla: Detalles de órdenes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-5 sm:p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Detalles de Órdenes</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Número de Orden
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Imagen
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Productos
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Dirección
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total de Orden
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="order in displayedOrders" :key="order.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        #{{ order.id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div v-for="item in order.items" :key="item.product_name" class="flex flex-col space-y-2">
                                            <img 
                                                :src="getImagePath(item)"
                                                :alt="item.product_name"
                                                class="h-16 w-16 object-cover rounded"
                                            />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div v-for="item in order.items" :key="item.product_name" class="mb-2">
                                            <div class="font-medium">{{ item.product_name }}</div>
                                            <div class="text-gray-500">
                                                Cantidad: {{ item.quantity }} x ${{ item.unit_price }}
                                                = ${{ item.total }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ order.user_name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                    <button
                                        @click="openAddressModal(order.user_address)"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Ver dirección completa
                                    </button>
                                </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ${{ order.total_price }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div v-if="showAddressModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50">
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                    Dirección Completa
                                </h3>
                                <div class="mt-2 text-left">
                                    <div class="space-y-2">
                                        <p class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">Tipo:</span> 
                                            {{ selectedAddress?.type }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">Dirección:</span> 
                                            {{ selectedAddress?.address1 }}
                                        </p>
                                        <p v-if="selectedAddress?.address2" class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">Dirección adicional:</span> 
                                            {{ selectedAddress?.address2 }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">Ciudad:</span> 
                                            {{ selectedAddress?.city }}
                                        </p>
                                        <p v-if="selectedAddress?.state" class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">Estado:</span> 
                                            {{ selectedAddress?.state }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">Código Postal:</span> 
                                            {{ selectedAddress?.zipcode }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <span class="font-bold text-black underline">País:</span> 
                                            {{ selectedAddress?.country_code }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button
                                type="button"
                                class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                @click="closeAddressModal"
                            >
                                Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    orders: {
        type: Array,
        required: true
    }
})

// Estado para los filtros de búsqueda
const searchFilters = ref({
    orderId: '',
    shippingStatus: ''
})

// Estado para los filtros aplicados
const activeFilters = ref({
    orderId: '',
    shippingStatus: ''
})

// Computed property para filtrar las órdenes
const displayedOrders = computed(() => {
    return props.orders.filter(order => {
        const matchesId = !activeFilters.value.orderId || 
            order.id.toString().includes(activeFilters.value.orderId.toString())
        
        const matchesStatus = !activeFilters.value.shippingStatus || 
            order.shipping_status === activeFilters.value.shippingStatus
        
        return matchesId && matchesStatus
    })
})

// Función para aplicar los filtros
const applyFilters = () => {
    activeFilters.value = { ...searchFilters.value }
}

// Función para limpiar los filtros
const clearFilters = () => {
    searchFilters.value = {
        orderId: '',
        shippingStatus: ''
    }
    activeFilters.value = {
        orderId: '',
        shippingStatus: ''
    }
}

/**
 * Obtiene la ruta de la imagen para un item del pedido
 * @param {Object} item - Objeto del item del pedido
 * @returns {string} - URL de la imagen o imagen placeholder
 */
const getImagePath = (item) => {
    if (item?.image) {
        // Si la imagen ya comienza con '/', no agregamos otro
        return item.image.startsWith('/') ? item.image : `/${item.image}`;
    }
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png";
};


const updateShippingStatus = (orderId, event) => {
    router.put(`/admin/orders/${orderId}/shipping-status`, {
        shipping_status: event.target.value
    }, {
        preserveScroll: true
    })
}


// Estado para el modal
const showAddressModal = ref(false)
const selectedAddress = ref(null)

// Funciones para el modal
const openAddressModal = (address) => {
    console.log('Address data:', address)
    selectedAddress.value = address
    showAddressModal.value = true
}

const closeAddressModal = () => {
    showAddressModal.value = false
    selectedAddress.value = null  
}
</script>