<script setup>
import UserLayouts from "./Layouts/UserLayouts.vue";
import { useForm } from "@inertiajs/vue3";
import { ref, computed, watch } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    contents: Array,
});

// Estados para mensajes y control
const auth = usePage().props.auth;
const showSuccessMessage = ref(false);
const loading = ref(false);
const errorMessage = ref("");
const showModal = ref(false);
const imagePreview = ref(null);
const currentImage = ref(null);

// Formulario de contacto
const contactForm = useForm({
    name: "",
    email: "",
    message: "",
});


// Formulario de edición con estado inicial
const editForm = useForm({
    id: "",
    title: "",
    content: "",
    map_url: "",
    type: "",
    image: null,
});



// Computed properties para filtrar contenidos
const cardContents = computed(() =>
    props.contents.filter((content) => content.type === "card")
);

const containerContents = computed(() =>
    props.contents.filter((content) => content.type === "container")
);

// Helper para obtener la ruta de la imagen
const getImagePath = (content) => {
    if (content?.images && content.images.length > 0) {
        return `/${content.images[0].image_path}`;
    }
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png";
};

// Manejo de carga de archivos
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (!file) return;

    if (file.type.startsWith("image/")) {
        if (currentImage.value && !editForm.image) {
            errorMessage.value = "Este contenido ya tiene una imagen. Elimine la actual antes de agregar una nueva.";
            event.target.value = "";
            return;
        }

        editForm.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        errorMessage.value = "Por favor, seleccione un archivo de imagen válido";
        event.target.value = "";
    }
};

// Eliminar imagen
const deleteImage = async (imageId) => {
    if (!confirm("¿Está seguro de que desea eliminar esta imagen?")) return;

    try {
        loading.value = true;
        await axios.delete(`/admin/contactanos/images/${imageId}`);
        currentImage.value = null;
        imagePreview.value = null;
        editForm.data.images = [];
        showSuccessMessage.value = true;
    } catch (error) {
        errorMessage.value = "Error al eliminar la imagen";
    } finally {
        loading.value = false;
    }
};


// Google Maps URL parser
const getGoogleMapsEmbedUrl = (url) => {
    if (!url) return null;

    // Handle already formatted embed URLs
    if (url.includes("google.com/maps/embed")) {
        return url;
    }

    // Extract place ID or coordinates
    const placeMatch = url.match(/place\/([^\/]+)/);
    const coordMatch = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);

    if (placeMatch) {
        return `https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q=${placeMatch[1]}`;
    } else if (coordMatch) {
        return `https://www.google.com/maps/embed/v1/view?key=YOUR_API_KEY&center=${coordMatch[1]},${coordMatch[2]}&zoom=15`;
    }

    return null;
};

// Enviar formulario de contacto
const submitContact = () => {
    contactForm.post(route("contactanos.store"), {
        onSuccess: () => {
            showSuccessMessage.value = true;
            contactForm.reset();
            setTimeout(() => {
                showSuccessMessage.value = false;
            }, 5000);
        },
    });
};
// Abrir modal de edición
const openEditModal = (item) => {
    editForm.id = item.id;
    editForm.title = item.title;
    editForm.content = item.content;
    editForm.map_url = item.map_url || "";
    editForm.type = item.type;

    if (item.images && item.images.length > 0) {
        currentImage.value = item.images[0];
        imagePreview.value = `/${item.images[0].image_path}`;
    } else {
        currentImage.value = null;
        imagePreview.value = null;
    }

    showModal.value = true;
};

// Guardar cambios
const saveChanges = async () => {
    if (!editForm.title || !editForm.content) {
        errorMessage.value = "Por favor complete todos los campos requeridos";
        return;
    }

    const formData = new FormData();
    formData.append("_method", "PUT");
    formData.append("title", editForm.title);
    formData.append("content", editForm.content);
    formData.append("map_url", editForm.map_url || "");
    formData.append("type", editForm.type);

    if (editForm.image instanceof File) {
        formData.append("images[]", editForm.image);
    }

    try {
        loading.value = true;
        const response = await axios.post(`/admin/contactanos/${editForm.id}`, formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        showModal.value = false;
        showSuccessMessage.value = true;
        setTimeout(() => window.location.reload(), 1500);
    } catch (error) {
        errorMessage.value = error.response?.data?.message || "Error al guardar los cambios";
    } finally {
        loading.value = false;
    }
};

const getSafeMapUrl = (url) => {
    if (!url) return "";
    if (url.includes("iframe")) {
        const srcMatch = url.match(/src="([^"]+)"/);
        return srcMatch ? srcMatch[1] : "";
    }
    return url;
};


// Limpiar mensajes después de 5 segundos
watch([showSuccessMessage, errorMessage], () => {
    if (showSuccessMessage.value || errorMessage.value) {
        setTimeout(() => {
            showSuccessMessage.value = false;
            errorMessage.value = "";
        }, 5000);
    }
});
</script>

<template>
    <user-layouts>
        <!-- Formulario de Contacto -->
        <section class="bg-white dark:bg-gray-900">
            <div class="py-8 lg:py-16 px-4 mx-auto max-w-screen-md">
                <h2
                    class="mb-4 text-4xl tracking-tight font-extrabold text-center text-gray-900 dark:text-white"
                >
                    Contáctanos
                </h2>
                <p
                    class="mb-8 lg:mb-16 font-light text-center text-gray-500 dark:text-gray-400 sm:text-xl"
                >
                    ¿Tienes algún Comentario? ¿Quieres enviarnos comentarios
                    sobre nuestros Productos? ¿Necesitas más detalles sobre
                    nuestro Cafe? Háznoslo saber.
                </p>

                <div
                    v-if="showSuccessMessage"
                    class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400"
                >
                    ¡Mensaje enviado correctamente! Nos pondremos en contacto
                    contigo pronto.
                </div>

                <form @submit.prevent="submitContact" class="space-y-8">
                    <div>
                        <label
                            for="email"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                        >
                            Correo Electrónico
                        </label>
                        <input
                            type="email"
                            id="email"
                            v-model="contactForm.email"
                            class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5"
                            placeholder="ejemplo@ejemplo.com"
                            required
                        />
                        <p
                            v-if="contactForm.errors.email"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ contactForm.errors.email }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300"
                        >
                            Nombre
                        </label>
                        <input
                            type="text"
                            id="name"
                            v-model="contactForm.name"
                            class="block p-3 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 shadow-sm focus:ring-primary-500 focus:border-primary-500"
                            placeholder="John Doe"
                            required
                        />
                        <p
                            v-if="contactForm.errors.name"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ contactForm.errors.name }}
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label
                            for="message"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400"
                        >
                            Mensaje
                        </label>
                        <textarea
                            id="message"
                            v-model="contactForm.message"
                            rows="6"
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg shadow-sm border border-gray-300 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Dejanos saber tu comentario..."
                            required
                        ></textarea>
                        <p
                            v-if="contactForm.errors.message"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ contactForm.errors.message }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="contactForm.processing"
                        class="py-3 px-5 text-sm font-medium text-center text-white rounded-lg bg-blue-500 sm:w-fit hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300"
                    >
                        {{ contactForm.processing ? "Enviando..." : "Enviar" }}
                    </button>
                </form>
            </div>

            <!-- Contenedor principal -->
            <div class="bg-white dark:bg-gray-900">
                <!-- Sección de información de contacto -->
                <div class="max-w-screen-xl mx-auto py-8 px-4">
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center justify-center"
                    >
                        <div
                            v-for="content in cardContents"
                            :key="content.id"
                            class="flex flex-col items-center p-6 bg-white rounded-lg border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700"
                        >
                            <div class="p-2 bg-blue-50 rounded-full">
                                <img
                                    :src="getImagePath(content)"
                                    :alt="content.title"
                                    class="w-full h-20 object-cover rounded-lg"
                                />
                            </div>
                            <h3
                                class="mt-4 text-lg font-medium text-gray-900 dark:text-white"
                            >
                                {{ content.title }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 text-center mx-auto max-w-3xl leading-relaxed my-4">
                                {{ content.content }}
                            </p>
                            <div  v-if="auth?.user?.isAdmin" class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                                <button
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                                    @click="openEditModal(content)"
                                >
                                <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                            />
                                        </svg>
                                    Editar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- Sección del mapa y descripción -->
            <div
                v-for="content in containerContents"
                :key="content.id"
                class="max-w-screen-xl mx-auto px-4 py-8"
            >
                <div v-if="auth?.user?.isAdmin" class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
                        @click="openEditModal(content)"
                    >
                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                                            />
                                        </svg>
                        Editar
                    </button>
                </div>

                <H1 class="text-4xl font-bold text-center mb-6 text-gray-900 dark:text-white tracking-tight">{{ content.title }}</H1>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Texto descriptivo -->
                    <div class="flex items-center">
                        <p
                            class="text-gray-600 dark:text-gray-400 text-center md:text-left"
                        >
                            {{ content.content }}
                        </p>
                    </div>

                    <!-- Iframe del mapa -->
                    <div
                        v-if="content.map_url"
                        class="w-full h-96 rounded-lg overflow-hidden"
                    >
                        <iframe
                            :src="getSafeMapUrl(content.map_url)"
                            width="100%"
                            height="100%"
                            style="border: 0"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-lg"
                        ></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal de Edición -->
        <dialog
    v-if="showModal"
    open
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-0"
    width="60%"
>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full p-6 relative">
        <h2 class="text-lg font-bold mb-4">Editar contenido</h2>
        <form @submit.prevent="saveChanges">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium">Título</label>
                <input
                    v-model="editForm.title"
                    type="text"
                    id="title"
                    class="mt-1 block w-full border rounded-md shadow-sm p-2"
                />
            </div>
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium">Descripción</label>
                <textarea
                    v-model="editForm.content"
                    id="content"
                    rows="3"
                    class="mt-1 block w-full border rounded-md shadow-sm p-2"
                ></textarea>
            </div>
            <div v-if="editForm.type === 'container'" class="mb-4">
                <label for="map_url" class="block text-sm font-medium">URL del Mapa</label>
                <input
                    v-model="editForm.map_url"
                    type="text"
                    id="map_url"
                    class="mt-1 block w-full border rounded-md shadow-sm p-2"
                    placeholder="https://www.google.com/maps/embed?..."
                />
            </div>

            <!-- Sección de imagen -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Imagen</label>
                <div v-if="currentImage" class="relative w-32 h-32 mb-4">
                    <img :src="imagePreview" class="w-full h-full object-cover rounded" alt="Imagen actual" />
                    <button
                        @click.prevent="deleteImage(currentImage.id)"
                        class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-bl hover:bg-red-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>

                <input
                    v-if="!currentImage"
                    type="file"
                    @change="handleFileUpload"
                    accept="image/*"
                    class="w-full"
                />

                <div v-if="imagePreview && !currentImage" class="relative w-32 h-32 mt-2">
                    <img :src="imagePreview" class="w-full h-full object-cover rounded" alt="Previsualización" />
                    <button
                        @click.prevent="() => { editForm.image = null; imagePreview = null; }"
                        class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-bl hover:bg-red-600"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 111.414 1.414L11.414 10l4.293 4.293a1 1 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded" @click="showModal = false">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</dialog>

    </user-layouts>
</template>
