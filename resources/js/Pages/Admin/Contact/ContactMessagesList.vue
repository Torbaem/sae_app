<template>
    <div class="p-6">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">
                    Mensajes de Contacto
                </h1>
                <p class="mt-2 text-sm text-gray-700">
                    Lista de mensajes recibidos a través del formulario de
                    contacto
                </p>
            </div>
        </div>

                <!-- Agregar sección de filtros -->
                <div class="mb-6 bg-white p-4 rounded-lg shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input
                        type="text"
                        v-model="filters.name"
                        class="w-full rounded-md border-gray-300 text-sm"
                        placeholder="Buscar por nombre..."
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        type="text"
                        v-model="filters.email"
                        class="w-full rounded-md border-gray-300 text-sm"
                        placeholder="Buscar por email..."
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select
                        v-model="filters.status"
                        class="w-full rounded-md border-gray-300 text-sm">
                        <option value="">Todos</option>
                        <option value="nuevo">Nuevo</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="completado">Completado</option>
                        <option value="spam">Spam</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end space-x-3">
                <button
                    @click="applyFilters"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm hover:bg-indigo-500"
                >
                    Aplicar Filtros
                </button>
                <button
                    @click="clearFilters"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-300"
                >
                    Limpiar Filtros
                </button>
            </div>
        </div>

<!-- Vista de Escritorio -->
<div class="hidden md:block">
    <div class="mt-8 flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">
                                    Nombre
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Email
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Mensaje
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Estado
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Fecha
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="message in filteredMessages" :key="message.id" :class="{ 'bg-gray-50': !message.is_read }">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                    {{ message.name }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ message.email }}
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-500">
                                    <button @click="openMessageModal(message)" 
                                            class="text-blue-600 hover:text-blue-900 hover:underline">
                                        Ver Mensaje completo
                                    </button>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <select v-model="message.status" 
                                            @change="updateStatus(message.id, $event)"
                                            :class="{
                                                'rounded-md text-sm block w-full pl-3 pr-10 py-2': true,
                                                'bg-yellow-50 text-yellow-700 border-yellow-300': message.status === 'nuevo',
                                                'bg-blue-50 text-blue-700 border-blue-300': message.status === 'en_proceso',
                                                'bg-orange-100 text-orange-700 border-orange-300': message.status === 'pendiente',
                                                'bg-green-50 text-green-700 border-green-300': message.status === 'completado',
                                                'bg-red-400 text-red-00 border-red-300': message.status === 'spam',
                                            }">
                                        <option value="nuevo">Nuevo</option>
                                        <option value="en_proceso">En Proceso</option>
                                        <option value="pendiente">Pendiente</option>
                                        <option value="completado">Completado</option>
                                        <option value="spam">Spam</option>
                                    </select>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ message.created_at }}
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                                    <div class="flex gap-2 justify-end">
                                        <button v-if="!message.is_read" 
                                                @click="markAsRead(message.id)"
                                                class="text-blue-600 hover:text-blue-900">
                                            Marcar como leído
                                        </button>
                                        <button @click="deleteMessage(message.id)"
                                                class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vista Móvil -->
<div class="md:hidden">
    <div class="space-y-4 px-4">
        <div v-for="message in filteredMessages" 
             :key="message.id" 
             :class="{ 'bg-gray-50': !message.is_read }"
             class="bg-white shadow rounded-lg p-4">
            <!-- Encabezado con nombre y fecha -->
            <div class="flex justify-between items-start mb-3">
                <h3 class="text-sm font-medium text-gray-900">{{ message.name }}</h3>
                <span class="text-xs text-gray-500">{{ message.created_at }}</span>
            </div>
            
            <!-- Email -->
            <div class="text-sm text-gray-500 mb-3">
                {{ message.email }}
            </div>
            
            <!-- Mensaje -->
            <div class="mb-3">
                <button @click="openMessageModal(message)" 
                        class="text-sm text-blue-600 hover:text-blue-900 hover:underline">
                    Ver Mensaje completo
                </button>
            </div>
            
            <!-- Estado -->
            <div class="mb-3">
                <select v-model="message.status" 
                        @change="updateStatus(message.id, $event)"
                        :class="{
                            'rounded-md text-sm block w-full pl-3 pr-10 py-2': true,
                            'bg-yellow-50 text-yellow-700 border-yellow-300': message.status === 'nuevo',
                            'bg-blue-50 text-blue-700 border-blue-300': message.status === 'en_proceso',
                            'bg-orange-100 text-orange-700 border-orange-300': message.status === 'pendiente',
                            'bg-green-50 text-green-700 border-green-300': message.status === 'completado',
                            'bg-red-400 text-red-00 border-red-300': message.status === 'spam',
                        }">
                    <option value="nuevo">Nuevo</option>
                    <option value="en_proceso">En Proceso</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completado">Completado</option>
                    <option value="spam">Spam</option>
                </select>
            </div>
            
            <!-- Acciones -->
            <div class="flex justify-end gap-3">
                <button v-if="!message.is_read" 
                        @click="markAsRead(message.id)"
                        class="text-sm text-blue-600 hover:text-blue-900">
                    Marcar como leído
                </button>
                <button @click="deleteMessage(message.id)"
                        class="text-sm text-red-600 hover:text-red-900">
                    Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

        
        <!-- Modal para ver mensaje completo -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
        >
            <div class="fixed inset-0 z-50 overflow-y-auto">
                <div
                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0"
                >
                    <div
                        class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6"
                    >
                        <div>
                            <div class="mt-3 text-center sm:mt-5">
                                <h3
                                    class="text-lg font-medium leading-6 text-gray-900 mb-4"
                                >
                                    Mensaje de {{ selectedMessage?.name }}
                                </h3>
                                <div class="mt-2">
                                    <div
                                        class="text-sm text-gray-500 text-left mb-4"
                                    >
                                        <p class="font-semibold">Email:</p>
                                        <p class="mb-4">
                                            {{ selectedMessage?.email }}
                                        </p>
                                        <p class="font-semibold">Mensaje:</p>
                                        <p class="whitespace-pre-wrap">
                                            {{ selectedMessage?.message }}
                                        </p>
                                    </div>
                                    <div
                                        class="text-sm text-gray-500 text-left mt-4"
                                    >
                                        <p class="font-semibold">Fecha:</p>
                                        <p>{{ selectedMessage?.created_at }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6">
                            <button
                                type="button"
                                class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                                @click="closeMessageModal"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';  // Agregamos computed aquí
import { router } from '@inertiajs/vue3';

const props = defineProps({
    messages: {
        type: Array,
        required: true
    }
});

// Estado para los filtros
const filters = ref({
    name: '',
    email: '',
    status: ''
});

// Modal states y funciones existentes...
const showModal = ref(false);
const selectedMessage = ref(null);

const openMessageModal = (message) => {
    selectedMessage.value = message;
    showModal.value = true;
};

const closeMessageModal = () => {
    showModal.value = false;
    selectedMessage.value = null;
};

// Computed property para filtrar los mensajes
const filteredMessages = computed(() => {
    return props.messages.filter(message => {
        const matchName = !filters.value.name || 
            message.name.toLowerCase().includes(filters.value.name.toLowerCase());
        
        const matchEmail = !filters.value.email || 
            message.email.toLowerCase().includes(filters.value.email.toLowerCase());
        
        const matchStatus = !filters.value.status || 
            message.status === filters.value.status;
        
        return matchName && matchEmail && matchStatus;
    });
});

// Funciones para los filtros
const applyFilters = () => {
    // El computed se actualizará automáticamente
};

const clearFilters = () => {
    filters.value = {
        name: '',
        email: '',
        status: ''
    };
};

// Funciones existentes
const markAsRead = (id) => {
    if (confirm('¿Marcar este mensaje como leído?')) {
        router.patch(route('admin.contact-messages.read', id));
    }
};

const updateStatus = (id, event) => {
    router.patch(route('admin.contact-messages.status', id), {
        status: event.target.value
    });
};

const deleteMessage = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar este mensaje?')) {
        router.delete(route('admin.contact-messages.destroy', id));
    }
};
</script>

