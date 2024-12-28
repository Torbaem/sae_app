<template>
    <div>
        <!-- Sección Hero no editable (estática) -->
        <section
            v-if="!isEditing"
            class="relative bg-white dark:bg-gray-900"
            :style="{
                backgroundImage: `url(${getBackgroundImage})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                backgroundRepeat: 'no-repeat',
            }"
        >
            <div class="absolute inset-0 bg-black opacity-50 z-0"></div>

            <div
                class="relative z-10 grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12"
            >
                <div class="mr-auto place-self-center lg:col-span-7">
                    <h1
                        class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl text-white"
                    >
                        {{
                            heroData.title || "Venta de Café especial Xilitla!"
                        }}
                    </h1>
                    <p
                        class="max-w-2xl mb-6 font-light text-gray-200 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400"
                    >
                        {{
                            heroData.description ||
                            "Resaltar la calidad única y distintiva del café cultivado, especialmente si ha sido reconocido como «café especial»."
                        }}
                    </p>
                    <a
                        :href="heroData.url || '#'"
                        class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                    >
                        Compra!
                    </a>
                </div>

                <div
                    class="lg:col-span-5 flex justify-center items-center lg:items-end"
                >
                    <img
                        :src="getProductImage"
                        alt="Producto Hero"
                        class="w-full h-auto max-w-sm lg:max-w-full object-contain"
                    />
                </div>
            </div>

            <!-- Botón de edición (solo visible para administradores) -->
            <div v-if="auth.user?.isAdmin" class="absolute top-4 right-4 z-20">
                <button
                    @click="startEditing"
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
                    Editar Hero
                </button>
            </div>
        </section>

        <!-- Modal de edición -->
        <el-dialog
            v-model="isEditing"
            title="Editar Hero"
            width="60%"
            :before-close="cancelEdit"
        >
            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-6">
                <!-- Mensajes de error/éxito -->
                <div
                    v-if="errorMessage"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded"
                >
                    {{ errorMessage }}
                </div>
                <div
                    v-if="successMessage"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded"
                >
                    {{ successMessage }}
                </div>

                <!-- Campos del formulario -->
                <div class="space-y-4">
                    <!-- Título -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Título del Hero*
                        </label>
                        <input
                            v-model="form.title"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Descripción*
                        </label>
                        <textarea
                            v-model="form.description"
                            required
                            rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                    </div>

                    <!-- URL -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            URL del botón*
                        </label>
                        <input
                            v-model="form.url"
                            type="url"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <!-- Imagen de fondo -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Imagen de fondo
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Preview de imagen actual -->
                            <div v-if="getBackgroundImage" class="relative">
                                <img
                                    :src="getBackgroundImage"
                                    class="w-full h-40 object-cover rounded-lg"
                                />
                                <button
                                    @click="deleteImage('background')"
                                    type="button"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <!-- Input de archivo -->
                            <input
                                type="file"
                                @change="
                                    handleFileUpload($event, 'background_image')
                                "
                                accept="image/*"
                                class="w-full"
                            />
                        </div>
                    </div>

                    <!-- Imagen del producto -->
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Imagen del producto
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Preview de imagen actual -->
                            <div v-if="getProductImage" class="relative">
                                <img
                                    :src="getProductImage"
                                    class="w-full h-40 object-cover rounded-lg"
                                />
                                <button
                                    @click="deleteImage('product')"
                                    type="button"
                                    class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full hover:bg-red-600"
                                >
                                    <svg
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <!-- Input de archivo -->
                            <input
                                type="file"
                                @change="
                                    handleFileUpload($event, 'product_image')
                                "
                                accept="image/*"
                                class="w-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-end gap-4">
                    <button
                        type="button"
                        @click="cancelEdit"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ loading ? "Guardando..." : "Guardar cambios" }}
                    </button>
                </div>
            </form>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";

// Props y estado inicial
const props = defineProps({
    heroContent: {
        type: Object,
        default: () => ({}),
    },
});

const auth = usePage().props.auth;
const isEditing = ref(false);
const loading = ref(false);
const errorMessage = ref("");
const successMessage = ref("");

// Estado del formulario
const initialFormState = {
    title: props.heroContent?.title || "",
    description: props.heroContent?.description || "",
    url: props.heroContent?.url || "",
    background_image: null,
    product_image: null,
};

const form = ref({ ...initialFormState });
const heroData = ref(props.heroContent || {});

// Computed properties para las imágenes
const getBackgroundImage = computed(() => {
    if (!heroData.value?.images) return "/img/Cafe-SierraAltaEspecial-Cultivo.jpg";
    const backgroundImage = heroData.value.images.find(
        img => img.type === 'background_image'
    );
    return backgroundImage ? `/${backgroundImage.image_path}` : "/img/Cafe-SierraAltaEspecial-Cultivo.jpg";
});

const getProductImage = computed(() => {
    if (!heroData.value?.images) return "/img/Cafe_xilitla.png";
    const productImage = heroData.value.images.find(
        img => img.type === 'product_image'
    );
    return productImage ? `/${productImage.image_path}` : "/img/Cafe_xilitla.png";
});
// Métodos
const startEditing = () => {
    form.value = {
        title: heroData.value.title || "",
        description: heroData.value.description || "",
        url: heroData.value.url || "",
        background_image: null,
        product_image: null,
    };
    isEditing.value = true;
};

const cancelEdit = () => {
    isEditing.value = false;
    form.value = { ...initialFormState };
    errorMessage.value = "";
    successMessage.value = "";
};

const handleFileUpload = (event, type) => {
    const file = event.target.files[0];
    if (file) {
        if (file.type.startsWith("image/")) {
            form.value[type] = file;
        } else {
            errorMessage.value =
                "Por favor, seleccione un archivo de imagen válido";
            event.target.value = "";
        }
    }
};

const deleteImage = async (type) => {
    const imageToDelete = heroData.value?.images?.find(
        (img) => img.type === `${type}_image`
    );
    if (
        !imageToDelete ||
        !confirm("¿Está seguro de que desea eliminar esta imagen?")
    )
        return;

    try {
        loading.value = true;
        await axios.delete(`/hero/image/${imageToDelete.id}`);
        heroData.value.images = heroData.value.images.filter(
            (img) => img.id !== imageToDelete.id
        );
        successMessage.value = "Imagen eliminada exitosamente";
    } catch (error) {
        errorMessage.value = "Error al eliminar la imagen";
    } finally {
        loading.value = false;
    }
};

const submitForm = async () => {
    try {
        loading.value = true;
        errorMessage.value = "";

        const formData = new FormData();
        formData.append("title", form.value.title);
        formData.append("description", form.value.description);
        formData.append("url", form.value.url);

        if (form.value.background_image) {
            formData.append("background_image", form.value.background_image);
        }
        if (form.value.product_image) {
            formData.append("product_image", form.value.product_image);
        }

        const response = await axios.post("/hero/update", formData);

        heroData.value = response.data.heroContent;
        successMessage.value = "Hero actualizado exitosamente";
        setTimeout(() => {
            isEditing.value = false;
            location.reload();
        }, 1500);
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Error al actualizar el hero";
    } finally {
        loading.value = false;
    }
};
</script>
