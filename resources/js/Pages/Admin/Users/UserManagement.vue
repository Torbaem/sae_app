<template>
    <div class="p-6">
        <!-- Botón para crear usuario -->
        <div class="mb-6">
            <button 
                @click="openModal('create')"
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                Crear Usuario
            </button>
        </div>

                <!-- Filtros -->
                <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="searchId" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar por ID
                    </label>
                    <input
                        id="searchId"
                        v-model="filters.id"
                        type="text"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="ID del usuario"
                    >
                </div>
                <div>
                    <label for="searchName" class="block text-sm font-medium text-gray-700 mb-1">
                        Buscar por Nombre
                    </label>
                    <input
                        id="searchName"
                        v-model="filters.name"
                        type="text"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="Nombre del usuario"
                    >
                </div>
                <div>
                    <label for="searchAdmin" class="block text-sm font-medium text-gray-700 mb-1">
                        Filtrar por Privilegios
                    </label>
                    <select
                        id="searchAdmin"
                        v-model="filters.isAdmin"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Todos</option>
                        <option value="1">Administradores</option>
                        <option value="0">No Administradores</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button
                        @click="clearFilters"
                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de usuarios -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Usuarios</h2>
                <span class="text-sm text-gray-600">
                    Total: {{ filteredUsers.length }} usuario(s)
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr v-if="filteredUsers.length === 0">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="user in filteredUsers" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ user.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span 
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                    :class="user.isAdmin ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                >
                                    {{ user.isAdmin ? 'Sí' : 'No' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button 
                                    @click="openModal('edit', user)"
                                    class="text-indigo-600 hover:text-indigo-900 mr-3"
                                >
                                    Editar
                                </button>
                                <button 
                                    @click="deleteUser(user.id)"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end sm:items-center justify-center min-h-full p-4 text-center sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <div class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                    {{ modalMode === 'create' ? 'Crear Usuario' : 'Editar Usuario' }}
                                </h3>
                                <form @submit.prevent="saveUser" class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                                        <input 
                                            id="name"
                                            v-model="form.name" 
                                            type="text" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input 
                                            id="email"
                                            v-model="form.email" 
                                            type="email" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                    </div>
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ modalMode === 'create' ? 'Contraseña' : 'Nueva Contraseña (dejar en blanco para mantener la actual)' }}
                                        </label>
                                        <input 
                                            id="password"
                                            v-model="form.password" 
                                            type="password" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            :required="modalMode === 'create'"
                                        >
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
                                        <input 
                                            id="password_confirmation"
                                            v-model="form.password_confirmation" 
                                            type="password" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            :required="modalMode === 'create' || form.password !== ''"
                                        >
                                    </div>
                                    <div class="flex items-center" v-if="canEditAdminStatus">
                                        <input 
                                            id="isAdmin"
                                            v-model="form.isAdmin" 
                                            type="checkbox" 
                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                        >
                                        <label for="isAdmin" class="ml-2 block text-sm text-gray-700">Es Administrador</label>
                                    </div>
                                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                                        <button 
                                            type="submit" 
                                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
                                        >
                                            {{ modalMode === 'create' ? 'Crear' : 'Actualizar' }}
                                        </button>
                                        <button 
                                            type="button" 
                                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
                                            @click="closeModal"
                                        >
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
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
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    users: {
        type: Array,
        required: true
    }
})

const currentUser = computed(() => usePage().props.auth.user)

const showModal = ref(false)
const modalMode = ref('create')
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    isAdmin: false
})
const editingId = ref(null)

const canEditAdminStatus = computed(() => {
    // Solo permite editar el estado de administrador si:
    // 1. Estamos creando un nuevo usuario
    // 2. Estamos editando un usuario que NO es el usuario actual
    return modalMode.value === 'create' || editingId.value !== currentUser.value.id
})

function openModal(mode, user = null) {
    modalMode.value = mode
    showModal.value = true
    
    if (mode === 'edit' && user) {
        editingId.value = user.id
        form.value = {
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
            isAdmin: user.isAdmin
        }
    } else {
        resetForm()
    }
}

function closeModal() {
    showModal.value = false
    resetForm()
}

async function saveUser() {
    try {
        if (modalMode.value === 'edit') {
            await axios.put(`/admin/users/${editingId.value}`, form.value)
        } else {
            await axios.post('/admin/users', form.value)
        }
        closeModal()
        window.location.reload()
    } catch (error) {
        if (error.response?.data?.errors) {
            alert(Object.values(error.response.data.errors).flat().join('\n'))
        } else {
            alert('Error al guardar usuario')
        }
    }
}

async function deleteUser(id) {
    // Prevenir eliminar el propio usuario
    if (id === currentUser.value.id) {
        alert('No puedes eliminar tu propio usuario')
        return
    }

    if (!confirm('¿Está seguro de eliminar este usuario?')) return

    try {
        await axios.delete(`/admin/users/${id}`)
        window.location.reload()
    } catch (error) {
        alert('Error al eliminar usuario')
    }
}

function resetForm() {
    form.value = {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        isAdmin: false
    }
    editingId.value = null
}

const filters = ref({
    id: '',
    name: '',
    isAdmin: ''
})

const filteredUsers = computed(() => {
    return props.users.filter(user => {
        const matchId = !filters.value.id || 
            user.id.toString().includes(filters.value.id.toString())
        
        const matchName = !filters.value.name || 
            user.name.toLowerCase().includes(filters.value.name.toLowerCase())
        
        const matchAdmin = filters.value.isAdmin === '' || 
            user.isAdmin.toString() === filters.value.isAdmin

        return matchId && matchName && matchAdmin
    })
})

function clearFilters() {
    filters.value = {
        id: '',
        name: '',
        isAdmin: ''
    }
}
</script>