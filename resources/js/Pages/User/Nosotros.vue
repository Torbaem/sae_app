<script setup>
// Importaciones necesarias
import UserLayouts from "./Layouts/UserLayouts.vue";
import { defineProps, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";  // Para acceder a las props de la página
import axios from "axios";

// Definición de props - Recibe un array de contenidos desde el componente padre
const props = defineProps({
    contents: {
        type: Array,
        required: true,
    },
});

// Estados reactivos para manejar la interfaz y datos
const auth = usePage().props.auth;  // Información del usuario autenticado
const selectedContent = ref(null);   // Contenido seleccionado para editar
const loading = ref(false);          // Estado de carga para operaciones asíncronas
const errorMessage = ref("");        // Mensajes de error
const successMessage = ref("");      // Mensajes de éxito
const dialogVisible = ref(false);    // Control de visibilidad del modal
const imagePreview = ref(null);      // Preview de imágenes
const selectedIcon = ref(null);      // Icono seleccionado (si se usa)
const currentImage = ref(null);      // Imagen actual en edición

// Estado inicial del formulario
const initialFormState = {
    title: "",
    content: "",
    type: "",
    images: [], // Array para almacenar las imágenes
};

// Referencia reactiva al formulario
const form = ref({ ...initialFormState });

/**
 * Obtiene la ruta de la imagen para un contenido dado
 * @param {Object} content - Objeto de contenido
 * @returns {string} - URL de la imagen o imagen placeholder
 */
const getImagePath = (content) => {
    if (content?.images && content.images.length > 0) {
        return `/${content.images[0].image_path}`;
    }
    return "https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/330px-No-Image-Placeholder.svg.png";
};

/**
 * Resetea el formulario y todos los estados relacionados
 */
const resetForm = () => {
    form.value = { ...initialFormState };
    imagePreview.value = null;
    selectedContent.value = null;
    selectedIcon.value = null;
    currentImage.value = null;
    dialogVisible.value = false;
};

/**
 * Maneja la carga de archivos de imagen
 * @param {Event} event - Evento del input file
 */
const handleFileUpload = (event) => {
    const files = event.target.files;
    if (!files || files.length === 0) return;

    // Inicialización de arrays si es necesario
    if (!Array.isArray(form.value.images)) {
        form.value.images = [];
    }
    if (!Array.isArray(imagePreview.value)) {
        imagePreview.value = [];
    }

    // Procesa cada archivo
    Array.from(files).forEach((file) => {
        if (file.type.startsWith("image/")) {
            form.value.images.push(file);
            // Crear preview de la imagen
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value.push(e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
};

/**
 * Valida los campos requeridos del formulario
 * @returns {boolean} - true si el formulario es válido
 */
const validateForm = () => {
    if (!form.value.title?.trim()) {
        errorMessage.value = "El título es requerido";
        return false;
    }
    if (!form.value.content?.trim()) {
        errorMessage.value = "El contenido es requerido";
        return false;
    }
    if (!form.value.type) {
        errorMessage.value = "El tipo es requerido";
        return false;
    }
    return true;
};

/**
 * Crea un nuevo contenido
 * Utiliza FormData para manejar la carga de archivos
 */
const createContent = async () => {
    try {
        if (!validateForm()) return;
        loading.value = true;

        const formData = new FormData();
        formData.append("title", form.value.title.trim());
        formData.append("content", form.value.content.trim());
        formData.append("type", form.value.type);

        // Agregar imágenes al FormData
        form.value.images.forEach((image, index) => {
            if (image instanceof File) {
                formData.append(`images[${index}]`, image);
            }
        });

        await axios.post("/admin/nosotros", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
            },
        });

        successMessage.value = "Contenido creado exitosamente";
        resetForm();
        setTimeout(() => location.reload(), 1500);
    } catch (error) {
        console.error(error);
        errorMessage.value =
            error.response?.data?.message || "Error al crear el contenido";
    } finally {
        loading.value = false;
    }
};

/**
 * Guarda o actualiza el contenido
 * Maneja tanto la creación como la actualización
 */
const saveContent = async () => {
    try {
        if (!validateForm()) return;
        loading.value = true;

        const formData = new FormData();
        formData.append("title", form.value.title.trim());
        formData.append("content", form.value.content.trim());
        formData.append("type", form.value.type);

        // Manejo seguro de imágenes
        if (Array.isArray(form.value.images)) {
            form.value.images.forEach((image, index) => {
                if (image instanceof File) {
                    formData.append(`images[${index}]`, image);
                }
            });
        }

        // Determina si es una actualización o creación
        if (selectedContent.value) {
            formData.append("_method", "PUT"); // Laravel method spoofing
            await axios.post(
                `/admin/nosotros/${selectedContent.value.id}`,
                formData,
                {
                    headers: { "Content-Type": "multipart/form-data" },
                }
            );
        } else {
            await axios.post("/admin/nosotros", formData, {
                headers: { "Content-Type": "multipart/form-data" },
            });
        }

        successMessage.value = selectedContent.value
            ? "Contenido actualizado exitosamente"
            : "Contenido creado exitosamente";

        resetForm();
        setTimeout(() => location.reload(), 1500);
    } catch (error) {
        console.error(error);
        errorMessage.value =
            error.response?.data?.message || "Error al procesar la solicitud";
    } finally {
        loading.value = false;
    }
};

/**
 * Elimina un contenido específico
 * @param {Object} content - Contenido a eliminar
 */
const deleteContent = async (content) => {
    try {
        if (confirm("¿Estás seguro de que deseas eliminar este contenido?")) {
            loading.value = true;
            await axios.delete(`/admin/nosotros/${content.id}`);
            successMessage.value = "Contenido eliminado exitosamente";
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        console.error(error);
        errorMessage.value =
            error.response?.data?.message || "Error al eliminar el contenido";
    } finally {
        loading.value = false;
    }
};

/**
 * Prepara el formulario para editar un contenido existente
 * @param {Object} content - Contenido a editar
 */
const editContent = (content) => {
    selectedContent.value = content;
    form.value = {
        title: content.title,
        content: content.content,
        type: content.type,
        images: [], // Inicializar como array vacío
    };

    // Manejo de imágenes existentes
    if (content.images && content.images.length > 0) {
        currentImage.value = content.images[0];
        imagePreview.value = [getImagePath(content)];
    } else {
        currentImage.value = null;
        imagePreview.value = [];
    }

    dialogVisible.value = true;
};

/**
 * Elimina una imagen específica
 * @param {number} imageId - ID de la imagen a eliminar
 */
const deleteImage = async (imageId) => {
    if (!confirm("¿Está seguro de que desea eliminar esta imagen?")) return;

    try {
        loading.value = true;
        await axios.delete(`/admin/nosotros/images/${imageId}`);
        
        // Resetear estados relacionados con la imagen
        currentImage.value = null;
        imagePreview.value = null;
        form.value.images = [];  // Cambiado de null a array vacío
        
        if (selectedContent.value) {
            selectedContent.value.images = [];
        }
        
        successMessage.value = "Imagen eliminada exitosamente";
    } catch (error) {
        console.error(error);
        errorMessage.value = "Error al eliminar la imagen";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <UserLayouts>
        <section class="bg-white dark:bg-gray-900">
            <div class="py-8 px-4 mx-auto max-w-screen-xl sm:py-16 lg:px-6">
                <div class="max-w-screen-md mb-8 lg:mb-16">
                    <h2
                        class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
                    >
                        Acerca de Nosotros
                    </h2>
                    <p class="text-gray-500 sm:text-xl dark:text-gray-400">
                        Cooperativa Café Especial
                    </p>
                </div>
                <div
                    class="space-y-8 md:grid md:grid-cols-2 lg:grid-cols-3 md:gap-12 md:space-y-0"
                >
                    <div
                        v-for="content in contents.filter(
                            (c) => c.type === 'card'
                        )"
                        :key="content.id"
                    >
                        <div
                            class="flex justify-center items-center mb-4 w-10 h-10 rounded-full bg-primary-100 lg:h-12 lg:w-12 dark:bg-primary-900"
                        >
                            <img
                                :src="getImagePath(content)"
                                class="h-auto w-auto object-contain"
                                :alt="content.title"
                            />
                        </div>
                        <h3 class="mb-2 text-xl font-bold dark:text-white">
                            {{ content.title }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ content.content }}
                        </p>
                        <div v-if="auth?.user?.isAdmin" class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                            <button
                                @click="editContent(content)"
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
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-white dark:bg-gray-900">
            <div
                v-for="content in contents.filter(
                    (c) => c.type === 'container'
                )"
                :key="content.id"
                class="gap-16 items-center py-8 px-4 mx-auto max-w-screen-xl lg:grid lg:grid-cols-2 lg:py-16 lg:px-6"
            >
                <div
                    class="font-light text-gray-500 sm:text-lg dark:text-gray-400"
                >
                    <h2
                        class="mb-4 text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white"
                    >
                        {{ content.title }}
                    </h2>
                    <p class="mb-4">
                        {{ content.content }}
                    </p>
                </div>
                <div class="grid grid gap-4 mt-8">
                    <img
                        :src="getImagePath(content)"
                        :alt="content.title"
                        />
                </div>
                <div v-if="auth?.user?.isAdmin" class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                    <button
                        @click="editContent(content)"
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
                </div>
            </div>
        </section>

        <!-- Edit Modal -->
        <el-dialog
            v-model="dialogVisible"
            :title="
                selectedContent ? 'Editar Contenido' : 'Crear Nuevo Contenido'
            "
            width="60%"
        >
            <form @submit.prevent="saveContent" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Título</label
                    >
                    <input
                        v-model="form.title"
                        type="text"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Contenido</label
                    >
                    <textarea
                        v-model="form.content"
                        required
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                    ></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Imagen</label
                    >
                    <div v-if="currentImage" class="mt-2">
                        <img
                            :src="imagePreview"
                            class="h-32 w-32 object-cover rounded"
                        />
                        <button
                            @click="deleteImage(currentImage.id)"
                            class="mt-2 text-red-500"
                        >
                            Eliminar imagen
                        </button>
                    </div>
                    <input
                        v-else
                        type="file"
                        @change="handleFileUpload"
                        accept="image/*"
                        class="mt-1"
                    />
                </div>

                <div v-if="errorMessage" class="text-red-500">
                    {{ errorMessage }}
                </div>
                <div v-if="successMessage" class="text-green-500">
                    {{ successMessage }}
                </div>

                <div class="flex justify-end space-x-2">
                    <button
                        type="button"
                        @click="dialogVisible = false"
                        class="px-4 py-2 border rounded"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-4 py-2 bg-blue-500 text-white rounded"
                    >
                        {{ loading ? "Guardando..." : "Guardar" }}
                    </button>
                </div>
            </form>
        </el-dialog>
    </UserLayouts>
</template>
