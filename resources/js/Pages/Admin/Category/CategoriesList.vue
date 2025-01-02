<template>
    <div class="p-6">
        <!-- Header y botón de agregar -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Gestión de Categorías</h2>
            <button 
                @click="showAddModal = true"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Agregar Nueva Categoría
            </button>
        </div>

        <!-- Tabla de categorías -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="category in categories" :key="category.id">
                        <td class="px-6 py-4 whitespace-nowrap">{{ category.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ category.slug }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button 
                                @click="editCategory(category)"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2"
                            >
                                Editar
                            </button>
                            <button 
                                @click="deleteCategory(category.id)"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal para agregar/editar categoría -->
        <Modal :show="showAddModal || showEditModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">
                    {{ showEditModal ? 'Editar Categoría' : 'Agregar Nueva Categoría' }}
                </h2>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Categoría</label>
                        <input 
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            required
                        >
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button 
                            type="button"
                            @click="closeModal"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                        >
                            Cancelar
                        </button>
                        <button 
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                            :disabled="form.processing"
                        >
                            {{ showEditModal ? 'Actualizar' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    categories: Array
});

const showAddModal = ref(false);
const showEditModal = ref(false);
const form = useForm({
    id: '',
    name: ''
});

const editCategory = (category) => {
    form.id = category.id;
    form.name = category.name;
    showEditModal.value = true;
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (showEditModal.value) {
        form.put(route('admin.categories.update', form.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                // Opcional: Mostrar mensaje de éxito
            },
        });
    } else {
        form.post(route('admin.categories.store'), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
                // Opcional: Mostrar mensaje de éxito
            },
        });
    }
};

const deleteCategory = (id) => {
    if (confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        form.delete(route('admin.categories.destroy', id), {
            preserveScroll: true,
            onSuccess: () => {
                // Opcional: Mostrar mensaje de éxito
            },
        });
    }
};
</script>