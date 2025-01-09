<script setup>
import { ref, computed, watch, onMounted } from "vue";
import axios from "axios";

const props = defineProps({
    initialTotal: {
        type: Number,
        required: true,
    },
    salesMonths: {
        type: Array,
        required: true,
    },
    currentPeriod: {
        type: Object,
        required: true,
    },
});

const totalSales = ref(props.initialTotal);
const selectedPeriod = ref(props.currentPeriod);
const isLoading = ref(false);
const error = ref(null);

// Agrupar meses por año
const groupedMonths = computed(() => {
    return props.salesMonths.reduce((groups, month) => {
        const year = month.year.toString();
        if (!groups[year]) {
            groups[year] = [];
        }
        groups[year].push(month);
        return groups;
    }, {});
});

// Obtener el nombre del mes actual seleccionado
const currentMonthLabel = computed(() => {
    if (!selectedPeriod.value) return "";

    const month = props.salesMonths.find(
        (m) =>
            m.year === selectedPeriod.value.year &&
            m.month === selectedPeriod.value.month
    );

    return month ? month.label : "";
});

// Observador para el currentPeriod
// Simplificar los watchers combinándolos en uno solo
watch(
    () => selectedPeriod.value,
    async (newPeriod) => {
        if (newPeriod) {
            await loadSalesData(newPeriod);
        }
    },
    { deep: true, immediate: true }
);

const loadSalesData = async (period) => {
    if (!period) return;

    isLoading.value = true;
    error.value = null;

    try {
        const response = await axios.get("/admin/monthly-sales", {
            params: {
                year: period.year,
                month: period.month,
            },
        });

        if (response.data.success) {
            totalSales.value = response.data.total;
        } else {
            throw new Error(response.data.error || "Error desconocido");
        }
    } catch (err) {
        console.error("Error al cargar las ventas:", err);
        error.value = "Error al cargar las ventas";
        totalSales.value = 0;
    } finally {
        isLoading.value = false;
    }
};

// Manejar el cambio de período directamente
const handlePeriodChange = (event) => {
    // Asegurarse de que el valor seleccionado sea válido
    if (event.target.value) {
        loadSalesData(selectedPeriod.value);
    }
};

const formatPrice = (price) => {
    return Number(price).toLocaleString("es-MX", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

// Cargar datos iniciales
onMounted(() => {
    if (props.currentPeriod) {
        loadSalesData(props.currentPeriod);
    }
});
</script>

<template>
    <div class="bg-blue-500 rounded-lg shadow-xl h-full">
        <div class="p-4 md:p-6 flex flex-col space-y-4 md:space-y-6">
            <!-- Título y Selector -->
            <div
                class="flex flex-col md:flex-row md:justify-between md:items-center space-y-3 md:space-y-0"
            >
                <h3 class="text-base md:text-lg font-bold text-white">
                    Ventas Por Mes
                </h3>
                <div class="relative">
                    <select
                        v-model="selectedPeriod"
                        @change="handlePeriodChange"
                        class="w-full md:w-auto bg-blue-700 border border-blue-500 text-white text-sm rounded-lg focus:ring-blue-300 focus:border-blue-300 block p-2 pr-8"
                        :disabled="isLoading"
                    >
                        <template v-if="Object.keys(groupedMonths).length">
                            <template
                                v-for="(months, year) in groupedMonths"
                                :key="year"
                            >
                                <optgroup :label="year" class="text-gray-200">
                                    <option
                                        v-for="month in months"
                                        :key="`${year}-${month.month}`"
                                        :value="{
                                            year: parseInt(year),
                                            month: month.month,
                                        }"
                                        class="text-gray-200 bg-blue-800 hover:bg-blue-700"
                                    >
                                        {{ month.label }}
                                    </option>
                                </optgroup>
                            </template>
                        </template>
                    </select>
                    <!-- Indicador de carga -->
                    <div
                        v-if="isLoading"
                        class="absolute right-10 top-1/2 -translate-y-1/2"
                    >
                        <div
                            class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Total de Ventas -->
            <div class="flex flex-col">
                <span class="text-xs md:text-sm text-blue-300">
                    Total de ventas de {{ currentMonthLabel }}:
                </span>
                <div class="mt-2">
                    <template v-if="isLoading">
                        <div
                            class="animate-pulse h-8 bg-blue-400/50 rounded w-48"
                        ></div>
                    </template>
                    <template v-else-if="error">
                        <span class="text-red-300 text-sm">{{ error }}</span>
                    </template>
                    <template v-else>
                        <span
                            v-if="totalSales > 0"
                            class="text-2xl md:text-4xl font-bold text-yellow-300"
                        >
                            ${{ formatPrice(totalSales) }}
                        </span>
                        <span
                            v-else
                            class="text-base md:text-xl font-medium text-blue-200"
                        >
                            No hay ventas completadas en este período
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>
