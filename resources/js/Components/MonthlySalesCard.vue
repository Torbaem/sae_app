<template>
    <div class="p-6 bg-blue-500 rounded-lg shadow-xl h-full">
        <div class="flex flex-col space-y-6">
            <!-- Título y Selector -->
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-white">
                    Ventas Por Mes
                </h3>
                <select
                    v-model="selectedPeriod"
                    @change="handlePeriodChange"
                    class="bg-blue-700 border border-blue-500 text-white text-sm rounded-lg focus:ring-blue-300 focus:border-blue-300 block p-2.5"
                >
                    <template v-if="Object.keys(groupedMonths).length">
                        <template v-for="(months, year) in groupedMonths" :key="year">
                            <optgroup :label="year" class="text-gray-200">
                                <option
                                    v-for="month in months"
                                    :key="`${year}-${month.month}`"
                                    :value="{year: month.year, month: month.month}"
                                    class="text-gray-200 bg-blue-800 hover:bg-blue-700"
                                >
                                    {{ month.label }}
                                </option>
                            </optgroup>
                        </template>
                    </template>
                    <option v-else disabled>No hay ventas registradas</option>
                </select>
            </div>

            <!-- Total de Ventas -->
            <div class="flex flex-col">
                <span class="text-sm text-blue-300">
                    Total de ventas del mes:
                </span>
                <span v-if="totalSales > 0" class="text-4xl font-bold text-yellow-300">
                    ${{ formatPrice(totalSales) }}
                </span>
                <span v-else class="text-xl font-medium text-blue-200">
                    No hay ventas completadas en este período
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
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

const handlePeriodChange = async () => {
    try {
        const response = await axios.get("/api/monthly-sales", {
            params: selectedPeriod.value,
        });
        totalSales.value = response.data.total;
    } catch (error) {
        console.error("Error al cargar las ventas:", error);
        totalSales.value = 0;
    }
};

const formatPrice = (price) => {
    return Number(price).toLocaleString("es-MX", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};
</script>