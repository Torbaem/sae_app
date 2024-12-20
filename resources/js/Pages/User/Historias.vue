<template>
    <user-layouts>
        <section class="py-12 bg-[#f3e7d3]">
            <div class="max-w-screen-xl mx-auto px-4">
                <h2 class="text-3xl font-bold text-center text-[#5a5a5a] mb-8">
                    Historias de nuestros Productores
                </h2>

                <!-- Contenido para usuarios sin productores -->
                <template v-if="productores.length === 0">
                    <!-- Vista para usuarios normales -->
                    <div 
                        v-if="!isAdmin" 
                        class="text-center p-8 bg-white rounded-lg shadow-md"
                    >
                        <p class="text-xl text-gray-600 mb-4">
                            Próximamente: Historias de nuestros productores
                        </p>
                    </div>

                    <!-- Vista para administradores -->
                    <div 
                        v-else 
                        class="bg-white p-8 rounded-lg shadow-md"
                    >
                        <div class="text-center mb-6">
                            <h3 class="text-2xl font-semibold text-gray-700 mb-4">
                                Agregar Productor
                            </h3>
                            <p class="text-gray-600 mb-6">
                                No hay productores registrados. ¿Desea agregar contenido?
                            </p>
                        </div>

                        <form 
                            @submit.prevent="addNewProducer" 
                            class="max-w-xl mx-auto space-y-6"
                        >
                            <div>
                                <label class="block text-gray-700 font-bold mb-2">
                                    Nombre del Productor
                                </label>
                                <input 
                                    v-model="newProducer.title" 
                                    type="text" 
                                    required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ingrese el nombre del productor"
                                />
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2">
                                    Descripción
                                </label>
                                <textarea 
                                    v-model="newProducer.description" 
                                    required
                                    rows="4"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Escriba la historia del productor"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2">
                                    Imagen del Productor
                                </label>
                                <input 
                                    type="file" 
                                    @change="handleImageUpload"
                                    accept="image/*"
                                    class="w-full px-4 py-2 border rounded-lg file:mr-4 file:rounded-full file:border-0 file:bg-blue-50 file:px-4 file:py-2 file:text-blue-700 hover:file:bg-blue-100"
                                />
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2">
                                    URL de Video
                                </label>
                                <input 
                                    v-model="newProducer.video_url" 
                                    type="text" 
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="URL de YouTube o video local"
                                />
                            </div>

                            <div>
                                <label class="block text-gray-700 font-bold mb-2">
                                    Tipo de Video
                                </label>
                                <select 
                                    v-model="newProducer.video_type"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="youtube">YouTube</option>
                                    <option value="local">Video Local</option>
                                </select>
                            </div>

                            <div class="flex justify-center">
                                <button 
                                    type="submit" 
                                    class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105"
                                >
                                    Crear Productor
                                </button>
                            </div>
                        </form>
                    </div>
                </template>

                <!-- Listado de Productores -->
                <div v-else class="space-y-8">
                    <div 
                        v-for="(productor, index) in productores" 
                        :key="index" 
                        class="bg-white p-6 rounded-lg shadow-lg flex flex-col md:flex-row transform transition hover:scale-105 hover:shadow-xl"
                    >
                        <!-- Contenido similar al ejemplo anterior -->
                        <div class="md:w-1/2 pr-4">
                            <h3 class="text-2xl font-semibold text-[#5a5a5a] mb-4">
                                {{ productor.title }}
                            </h3>
                            
                            <img
                                :src="productor.image_path ? `/storage/${productor.image_path}` : '/path/to/default-image.jpg'"
                                :alt="productor.title"
                                class="w-60 h-60 object-cover rounded-lg mx-auto mb-4 shadow-md"
                            />
                            
                            <p class="text-center italic text-[#5a5a5a] mb-4">
                                {{ productor.title }}
                            </p>
                            
                            <p class="text-[#5a5a5a] text-justify">
                                {{ productor.description }}
                            </p>
                        </div>
                        
                        <div class="md:w-1/2 mt-4 md:mt-0">
                            <div class="w-full rounded-lg overflow-hidden shadow-md">
                                <template v-if="productor.video_type === 'local'">
                                    <video 
                                        class="w-full" 
                                        controls
                                    >
                                        <source 
                                            :src="productor.video_url" 
                                            type="video/mp4" 
                                        />
                                    </video>
                                </template>
                                
                                <template v-else-if="productor.video_type === 'youtube'">
                                    <iframe 
                                        :src="productor.video_url" 
                                        class="w-full aspect-video"
                                        frameborder="0"
                                        allowfullscreen
                                    ></iframe>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </user-layouts>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import UserLayouts from './Layouts/UserLayouts.vue';

const productores = ref([])
const isAdmin = ref(false)
const newProducer = ref({
    title: '',
    description: '',
    video_url: '',
    video_type: 'youtube'
})
const imageFile = ref(null)

onMounted(async () => {
    try {
        // Verificar si es admin
        const adminResponse = await axios.get('/check-admin')
        isAdmin.value = adminResponse.data.isAdmin

        // Cargar contenido de productores
        const response = await axios.get('/api/editable-content', {
            params: {
                page: 'historias',
                section_key: 'productores'
            }
        })
        productores.value = response.data.contents
    } catch (error) {
        console.error('Error al cargar contenido:', error)
    }
})

const handleImageUpload = (event) => {
    imageFile.value = event.target.files[0]
}

const addNewProducer = async () => {
    try {
        const formData = new FormData()
        formData.append('page', 'historias')
        formData.append('section_key', 'productores')
        formData.append('title', newProducer.value.title)
        formData.append('description', newProducer.value.description)
        formData.append('video_url', newProducer.value.video_url)
        formData.append('video_type', newProducer.value.video_type)
        
        if (imageFile.value) {
            formData.append('image', imageFile.value)
        }

        const response = await axios.post('/api/create-content', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })

        // Agregar nuevo productor a la lista
        productores.value.push(response.data)
        
        // Resetear formulario
        newProducer.value = {
            title: '',
            description: '',
            video_url: '',
            video_type: 'youtube'
        }
        imageFile.value = null
    } catch (error) {
        console.error('Error al crear productor:', error)
        // Aquí podrías agregar una notificación de error
    }
}
</script>