<!-- Historias.vue -->
<script setup>
import UserLayouts from "./Layouts/UserLayouts.vue";
import { ref, watch } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const auth = usePage().props.auth;
const { producers } = usePage().props;
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");
const dialogVisible = ref(false);

const initialFormState = {
    name: "",
    content: "",
    youtube_url: "",
    image: null,
};

const form = ref({ ...initialFormState });
const editingProducer = ref(null);
const imagePreview = ref(null);
const currentImage = ref(null);

function getYoutubeEmbedUrl(youtubeUrl) {
    const videoIdMatch = youtubeUrl.match(
        /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^/]+\/.+|(?:v|embed)\/|.*[?&]v=)|youtu\.be\/)([^"&?/ ]{11})/
    );
    const videoId = videoIdMatch ? videoIdMatch[1] : null;
    return videoId ? `https://www.youtube.com/embed/${videoId}` : null;
}

// Computed para manejar la imagen
const getImagePath = (producer) => {
    // Verifica si el productor tiene imágenes
    if (producer?.images && producer.images.length > 0) {
        // Retorna la primera imagen del productor
        return `/${producer.images[0].image_path}`;
    }
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png";
};

// Reset form
const resetForm = () => {
    form.value = { ...initialFormState };
    imagePreview.value = null;
    editingProducer.value = null;
    dialogVisible.value = false;
};

// Validación básica
const validateForm = () => {
    if (!form.value.name?.trim()) {
        errorMessage.value = "El nombre es requerido";
        return false;
    }
    if (!form.value.content?.trim()) {
        errorMessage.value = "El contenido es requerido";
        return false;
    }
    if (form.value.youtube_url && !isValidYouTubeUrl(form.value.youtube_url)) {
        errorMessage.value = "La URL de YouTube no es válida";
        return false;
    }
    return true;
};

const isValidYouTubeUrl = (url) => {
    const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+/;
    return youtubeRegex.test(url);
};

const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.type.startsWith("image/")) {
            // Si ya existe una imagen y estamos editando, mostrar error
            if (
                editingProducer.value?.images?.length > 0 &&
                !form.value.image
            ) {
                errorMessage.value =
                    "Este productor ya tiene una imagen. Elimine la actual antes de agregar una nueva.";
                event.target.value = ""; // Limpiar input
                return;
            }

            form.value.image = file;
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            errorMessage.value =
                "Por favor, seleccione un archivo de imagen válido";
            event.target.value = "";
        }
    }
};

// Función para eliminar imagen
const deleteImage = async (imageId) => {
    if (!confirm("¿Está seguro de que desea eliminar esta imagen?")) return;

    try {
        loading.value = true;
        await axios.delete(`/historias/image/${imageId}`);
        currentImage.value = null;
        imagePreview.value = null;
        if (editingProducer.value) {
            editingProducer.value.images = [];
        }
        successMessage.value = "Imagen eliminada exitosamente";
    } catch (error) {
        errorMessage.value = "Error al eliminar la imagen";
    } finally {
        loading.value = false;
    }
};

const saveProducer = async () => {
    try {
        errorMessage.value = "";
        if (!validateForm()) return;

        loading.value = true;
        const formData = new FormData();
        formData.append("name", form.value.name.trim());
        formData.append("content", form.value.content.trim());
        formData.append("youtube_url", form.value.youtube_url.trim());
        if (form.value.image) {
            formData.append("image", form.value.image);
        }

        // Si estamos editando, incluir el método PUT
        if (editingProducer.value) {
            formData.append("_method", "PUT");
            await axios.post(
                `/historias/${editingProducer.value.id}`,
                formData
            );
        } else {
            await axios.post("/historias", formData);
        }

        successMessage.value = editingProducer.value
            ? "Productor actualizado exitosamente"
            : "Productor agregado exitosamente";

        resetForm();
        setTimeout(() => {
            location.reload();
        }, 1500);
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Ocurrió un error al guardar";
    } finally {
        loading.value = false;
    }
};

const editProducer = (producer) => {
    editingProducer.value = producer;
    form.value = {
        name: producer.name,
        content: producer.content,
        youtube_url: producer.youtube_url,
        image: null,
    };

    // Establecer la imagen actual si existe
    if (producer.images && producer.images.length > 0) {
        currentImage.value = producer.images[0];
        imagePreview.value = `/${producer.images[0].image_path}`;
    } else {
        currentImage.value = null;
        imagePreview.value = null;
    }

    dialogVisible.value = true;
};

const openAddModal = () => {
    editingProducer.value = null;
    resetForm();
    dialogVisible.value = true;
};

const deleteProducer = async (id) => {
    if (!confirm("¿Está seguro de que desea eliminar este productor?")) return;

    try {
        loading.value = true;
        await axios.delete(`/historias/${id}`);
        successMessage.value = "Productor eliminado exitosamente";
        setTimeout(() => {
            location.reload();
        }, 1500);
    } catch (error) {
        errorMessage.value = "Error al eliminar el productor";
    } finally {
        loading.value = false;
    }
};

// Limpiar mensajes después de 5 segundos
watch([successMessage, errorMessage], () => {
    if (successMessage.value || errorMessage.value) {
        setTimeout(() => {
            successMessage.value = "";
            errorMessage.value = "";
        }, 5000);
    }
});
</script>

<template>
    <UserLayouts>
        <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
            <h1 class="text-3xl font-bold mb-6">Historias de Productores</h1>

            <!-- Mensajes de éxito y error -->
            <div
                v-if="successMessage"
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4"
            >
                {{ successMessage }}
            </div>
            <div
                v-if="errorMessage"
                class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"
            >
                {{ errorMessage }}
            </div>

            <!-- Loading overlay -->
            <div
                v-if="loading"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            >
                <div class="bg-white p-4 rounded-lg">Procesando...</div>
            </div>

            <!-- Cards Productores Vista normal  -->
            <div
                v-if="!auth.user || (auth.user && !auth.user.isAdmin)"
                class="overflow-x-auto"
            >
                <div class="grid gap-6 p-4">
                    <!-- Card de Productor -->
                    <div
                        v-for="producer in producers"
                        :key="producer.id"
                        class="bg-white rounded-lg shadow-md p-6 flex flex-col md:flex-row gap-6"
                    >
                        <!-- Columna Izquierda: Imagen -->
                        <div class="w-full md:w-1/4">
                            <div
                                class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-lg bg-gray-100"
                            >
                                <img
                                    :src="getImagePath(producer)"
                                    :alt="producer.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                        </div>

                        <!-- Columna Derecha: Contenido -->
                        <div class="flex-1 flex flex-col">
                            <!-- Nombre del Productor -->
                            <h3
                                class="text-xl font-bold border-b border-gray-200 pb-2 mb-4"
                            >
                                {{ producer.name }}
                            </h3>

                            <!-- Contenido/Texto -->
                            <div class="flex-grow mb-4">
                                <h3
                                    class="text-gray-700 leading-relaxed whitespace-pre-line"
                                >
                                    {{ producer.content }}
                                </h3>
                            </div>

                            <!-- Video de YouTube -->
                            <div
                                v-if="producer.youtube_url"
                                class="w-full h-full aspect-w-16 aspect-h-16 mt-4"
                            >
                                <iframe
                                    :src="
                                        getYoutubeEmbedUrl(producer.youtube_url)
                                    "
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                    width="900"
                                    height="500"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards de prodcutores  Editables-->
            <div v-if="auth.user && auth.user.isAdmin">
                <h1 class="text-2xl mb-6">Vista de Editor</h1>
                <div class="overflow-x-auto">
                    <div class="grid gap-6 p-4">
                        <!-- Card de Productor -->
                        <div
                            v-for="producer in producers"
                            :key="producer.id"
                            class="bg-white rounded-lg shadow-md p-6 flex flex-col md:flex-row gap-6"
                        >
                            <!-- Columna Izquierda: Imagen -->
                            <div class="w-full md:w-1/4">
                                <div
                                    class="aspect-w-3 aspect-h-4 w-full overflow-hidden rounded-lg bg-gray-100"
                                >
                                    <img
                                        :src="getImagePath(producer)"
                                        :alt="producer.name"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                            </div>

                            <!-- Columna Derecha: Contenido -->
                            <div class="flex-1 flex flex-col">
                                <!-- Botones de Acción -->
                                <div
                                    class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200"
                                >
                                    <button
                                        @click="editProducer(producer)"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2"
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
                                    <button
                                        @click="deleteProducer(producer.id)"
                                        class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg transition duration-200"
                                    >
                                        Eliminar
                                    </button>
                                </div>
                                <!-- Nombre del Productor -->
                                <h3
                                    class="text-xl font-bold border-b border-gray-200 pb-2 mb-4"
                                >
                                    {{ producer.name }}
                                </h3>

                                <!-- Contenido/Texto -->
                                <div class="flex-grow mb-4">
                                    <h3
                                        class="text-gray-700 leading-relaxed whitespace-pre-line"
                                    >
                                        {{ producer.content }}
                                    </h3>
                                </div>

                                <!-- Video de YouTube -->
                                <div
                                    v-if="producer.youtube_url"
                                    class="w-full h-full aspect-w-16 aspect-h-16 mt-4"
                                >
                                    <iframe
                                        :src="
                                            getYoutubeEmbedUrl(
                                                producer.youtube_url
                                            )
                                        "
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                        width="500"
                                        height="300"
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button
                    @click="openAddModal"
                    class="flex items-center justify-center text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800"
                >
                    <svg
                        width="30px"
                        height="30px"
                        viewBox="0 0 32 32"
                        version="1.1"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                        xmlns:sketch="http://www.bohemiancoding.com/sketch/ns"
                        fill="#ffffff"
                        stroke="#ffffff"
                        class="mr-2"
                    >
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g
                            id="SVGRepo_tracerCarrier"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        ></g>
                        <g id="SVGRepo_iconCarrier">
                            <title>plus-circle</title>
                            <desc>Created with Sketch Beta.</desc>
                            <defs></defs>
                            <g
                                id="Page-1"
                                stroke="none"
                                stroke-width="1"
                                fill="none"
                                fill-rule="evenodd"
                                sketch:type="MSPage"
                            >
                                <g
                                    id="Icon-Set"
                                    sketch:type="MSLayerGroup"
                                    transform="translate(-464.000000, -1087.000000)"
                                    fill="#ffffff"
                                >
                                    <path
                                        d="M480,1117 C472.268,1117 466,1110.73 466,1103 C466,1095.27 472.268,1089 480,1089 C487.732,1089 494,1095.27 494,1103 C494,1110.73 487.732,1117 480,1117 L480,1117 Z M480,1087 C471.163,1087 464,1094.16 464,1103 C464,1111.84 471.163,1119 480,1119 C488.837,1119 496,1111.84 496,1103 C496,1094.16 488.837,1087 480,1087 L480,1087 Z M486,1102 L481,1102 L481,1097 C481,1096.45 480.553,1096 480,1096 C479.447,1096 479,1096.45 479,1097 L479,1102 L474,1102 C473.447,1102 473,1102.45 473,1103 C473,1103.55 473.447,1104 474,1104 L479,1104 L479,1109 C479,1109.55 479.447,1110 480,1110 C480.553,1110 481,1109.55 481,1109 L481,1104 L486,1104 C486.553,1104 487,1103.55 487,1103 C487,1102.45 486.553,1102 486,1102 L486,1102 Z"
                                        id="plus-circle"
                                        sketch:type="MSShapeGroup"
                                    ></path>
                                </g>
                            </g>
                        </g>
                    </svg>
                    Añadir Productor
                </button>
            </div>

            <!-- Formulario -->

            <el-dialog
                v-model="dialogVisible"
                :title="
                    editingProducer ? 'Editar Productor' : 'Agregar Productor'
                "
                width="60%"
                :before-close="resetForm"
            >
                <form
                    @submit.prevent="saveProducer"
                    enctype="multipart/form-data"
                    class="space-y-6"
                >
                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Nombre del Productor*
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{
                                    'border-red-500':
                                        errorMessage && !form.name,
                                }"
                                placeholder="Ingrese el nombre"
                            />
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Contenido*
                            </label>
                            <textarea
                                v-model="form.content"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :class="{
                                    'border-red-500':
                                        errorMessage && !form.content,
                                }"
                                rows="4"
                                placeholder="Ingrese el contenido"
                            ></textarea>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                URL de YouTube
                            </label>
                            <input
                                v-model="form.youtube_url"
                                type="url"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="https://youtube.com/..."
                            />
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Imagen
                            </label>

                            <!-- Mostrar la imagen actual si existe -->
                            <div
                                v-if="editingProducer?.images?.length > 0"
                                class="mb-4"
                            >
                                <div
                                    class="relative aspect-w-1 aspect-h-1 w-32 h-32 overflow-hidden rounded-lg bg-gray-100"
                                >
                                    <img
                                        :src="getImagePath(editingProducer)"
                                        class="w-full h-full object-cover"
                                        alt="Current Image"
                                    />
                                    <button
                                        @click.prevent="
                                            deleteImage(
                                                editingProducer.images[0].id
                                            )
                                        "
                                        class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-bl-lg hover:bg-red-600 transition-colors"
                                        title="Eliminar imagen"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Input de archivo solo visible si no hay imagen actual -->
                            <input
                                v-if="!editingProducer?.images?.length"
                                type="file"
                                @change="handleFileUpload"
                                accept="image/*"
                                class="w-full"
                                :class="{
                                    'opacity-50 cursor-not-allowed':
                                        form.image !== null,
                                }"
                                :disabled="form.image !== null"
                            />

                            <!-- Preview de la nueva imagen -->
                            <div
                                v-if="
                                    imagePreview &&
                                    !editingProducer?.images?.length
                                "
                                class="mt-2 relative aspect-w-1 aspect-h-1 w-32 h-32 overflow-hidden rounded-lg bg-gray-100"
                            >
                                <img
                                    :src="imagePreview"
                                    class="w-full h-full object-cover"
                                    alt="Preview"
                                />
                                <button
                                    @click.prevent="
                                        () => {
                                            form.image = null;
                                            imagePreview = null;
                                        }
                                    "
                                    class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-bl-lg hover:bg-red-600 transition-colors"
                                    title="Remover imagen"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mt-6">
                            <button
                                type="button"
                                @click="resetForm"
                                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition duration-200"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition duration-200 disabled:opacity-50"
                            >
                                {{ loading ? "Guardando..." : "Guardar" }}
                            </button>
                        </div>
                    </div>
                </form>
            </el-dialog>
        </div>
    </UserLayouts>
</template>
