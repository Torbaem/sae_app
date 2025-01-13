<template>
    <div class="p-6">
        <!-- Header y botón de agregar -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Gestión de Marcas</h2>
            <button
                @click="showAddModal = true"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
            >
                Agregar Nueva Marca
            </button>
        </div>

        <!-- Vista de escritorio -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="py-2 px-4 text-left">Nombre</th>
                        <th class="py-2 px-4 text-left">Slug</th>
                        <th class="py-2 px-4 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="brand in brands" :key="brand.id" class="border-b">
                        <td class="py-2 px-4">{{ brand.name }}</td>
                        <td class="py-2 px-4">{{ brand.slug }}</td>
                        <td class="py-2 px-4">
                            <button
                                @click="editBrand(brand)"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded mr-2"
                            >
                                Editar
                            </button>
                            <button
                                @click="deleteBrand(brand.id)"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded"
                            >
                                Eliminar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Vista móvil -->
        <div class="md:hidden">
            <div v-for="brand in brands" 
                 :key="brand.id" 
                 class="bg-white shadow rounded-lg mb-4 p-4">
                
                <div class="space-y-3">
                    <!-- Nombre -->
                    <div class="flex flex-col">
                        <span class="text-gray-600 text-sm font-medium">Nombre:</span>
                        <span class="text-gray-900">{{ brand.name }}</span>
                    </div>

                    <!-- Slug -->
                    <div class="flex flex-col">
                        <span class="text-gray-600 text-sm font-medium">Slug:</span>
                        <span class="text-gray-900">{{ brand.slug }}</span>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex space-x-2 pt-2">
                        <button
                            @click="editBrand(brand)"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm"
                        >
                            Editar
                        </button>
                        <button
                            @click="deleteBrand(brand.id)"
                            class="flex-1 bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm"
                        >
                            Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para agregar/editar marca -->
        <Modal :show="showAddModal || showEditModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">
                    {{ showEditModal ? "Editar Marca" : "Agregar Nueva Marca" }}
                </h2>
                <form @submit.prevent="submitForm">
                    <div class="mb-4">
                        <label class="block mb-2">Nombre:</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full px-3 py-2 border rounded"
                            required
                        />
                    </div>
                    <div class="flex justify-end">
                        <button
                            type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        >
                            {{ showEditModal ? "Actualizar" : "Guardar" }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue";

const props = defineProps({
    brands: Array,
});

const showAddModal = ref(false);
const showEditModal = ref(false);
const form = useForm({
    name: "",
});

const editBrand = (brand) => {
    form.name = brand.name;
    form.id = brand.id;
    showEditModal.value = true;
};

const closeModal = () => {
    showAddModal.value = false;
    showEditModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (showEditModal.value) {
        form.put(route("admin.brands.update", form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.brands.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteBrand = (id) => {
    if (confirm("¿Estás seguro de que deseas eliminar esta marca?")) {
        form.delete(route("admin.brands.destroy", id));
    }
};
</script>
