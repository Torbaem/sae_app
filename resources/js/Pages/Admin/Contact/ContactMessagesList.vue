<template>
    <div class="p-6">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">Mensajes de Contacto</h1>
                <p class="mt-2 text-sm text-gray-700">Lista de mensajes recibidos a través del formulario de contacto</p>
            </div>
        </div>

        <div class="mt-8 flex flex-col">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Nombre</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Mensaje</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Estado</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Fecha</th>
                                    <th scope="col" class="relative py-3.5 pl-3 pr-4">
                                        <span class="sr-only">Acciones</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="message in messages" :key="message.id" 
                                    :class="{ 'bg-gray-50': !message.is_read }">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                                        {{ message.name }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ message.email }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500 max-w-md truncate">
                                        {{ message.message }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        <select 
                                            v-model="message.status"
                                            @change="updateStatus(message.id, $event)"
                                            class="rounded-md border-gray-300 text-sm">
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
                                            <button 
                                                v-if="!message.is_read"
                                                @click="markAsRead(message.id)"
                                                class="text-blue-600 hover:text-blue-900">
                                                Marcar como leído
                                            </button>
                                            <button 
                                                @click="deleteMessage(message.id)"
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
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    messages: {
        type: Array,
        required: true
    }
});

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