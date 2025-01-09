<script>
export default {
    name: "WeeklySalesCard",

    props: {
        weekSales: {
            type: Array,
            required: true,
        },
        totalWeekSales: {
            type: Number,
            required: true,
        },
        weekRange: {
            type: Object,
            required: true,
        },
    },

    methods: {
        formatDate(dateString) {
            const date = new Date(dateString + "T12:00:00");
            const options = {
                weekday: "long",
                day: "numeric",
                month: "long",
                timeZone: "America/Mexico_City",
            };
            return date.toLocaleDateString("es-ES", options);
        },

        formatPrice(price) {
            return Number(price).toLocaleString("es-MX", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            });
        },

        // Nuevo método para formato de fecha corto en móvil
        formatShortDate(dateString) {
            const date = new Date(dateString + "T12:00:00");
            const options = {
                weekday: "short",
                day: "numeric",
                timeZone: "America/Mexico_City",
            };
            return date.toLocaleDateString("es-ES", options);
        },
    },
};
</script>

<template>
    <div class="bg-purple-100 dark:bg-gray-800 rounded-lg shadow-lg p-4 md:p-6 h-full overflow-hidden">
        <!-- Header con título e icono -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex-1 min-w-0">
                <h3 class="text-base md:text-lg font-bold text-yellow-700 dark:text-yellow-300 truncate">
                    Ventas de la Semana
                </h3>
                <p class="text-xs md:text-sm text-yellow-600 dark:text-yellow-400 mt-1">
                    <span class="hidden md:inline">
                        {{ formatDate(weekRange.start) }} - {{ formatDate(weekRange.end) }}
                    </span>
                    <span class="md:hidden">
                        {{ formatShortDate(weekRange.start) }} - {{ formatShortDate(weekRange.end) }}
                    </span>
                </p>
            </div>
            <div class="p-2 md:p-3 bg-purple-400 dark:bg-yellow-800 rounded-full ml-2 flex-shrink-0">
                <svg 
                    class="w-6 h-6 md:w-8 md:h-8"
                    fill="#5D3FD3" 
                    xmlns="http://www.w3.org/2000/svg" 
                    viewBox="0 0 52 52"
                    enable-background="new 0 0 52 52"
                >
                    <path d="M2,45.5c0,2.2,1.8,4,4,4h42.4c0.9,0,1.6-0.7,1.6-1.6v-2.8c0-0.9-0.7-1.6-1.6-1.6l-38.9,0 C8.7,43.5,8,42.8,8,42L8,4.1c0-0.9-0.7-1.6-1.6-1.6H3.6C2.7,2.5,2,3.2,2,4.1V45.5z"></path>
                    <path d="M49.7,14.1c0-1.7-1.3-3-3-3c-0.9,0-1.6,0.4-2.2,0.9l-8.6,8.6L30,15c0,0,0,0,0,0l-0.1-0.1 c-0.1-0.1-0.1-0.1-0.2-0.2c-0.1,0-0.1-0.1-0.2-0.1c-0.1-0.1-0.2-0.1-0.3-0.2c-0.1,0-0.1-0.1-0.2-0.1c-0.1,0-0.2-0.1-0.4-0.1 c-0.1,0-0.1,0-0.2-0.1c-0.2,0-0.4-0.1-0.6-0.1c-0.2,0-0.4,0-0.6,0.1c-0.1,0-0.1,0-0.2,0.1c-0.1,0-0.3,0.1-0.4,0.1 c-0.1,0-0.1,0.1-0.2,0.1c-0.1,0.1-0.2,0.1-0.3,0.2c-0.1,0-0.1,0.1-0.2,0.2c-0.1,0.1-0.2,0.1-0.3,0.2c0,0,0,0,0,0L14.2,26.5 c-0.6,0.6-1,1.3-1,2.2c0,1.7,1.3,3,3,3c0.7,0,1.4-0.3,1.9-0.7l9.8-9.7l5.7,5.6c0,0,0,0,0,0c0.1,0.1,0.2,0.1,0.3,0.2 c0.1,0.1,0.1,0.1,0.2,0.2c0.1,0.1,0.2,0.1,0.3,0.2c0.1,0,0.1,0.1,0.2,0.1c0.1,0.1,0.3,0.1,0.4,0.1c0.1,0,0.1,0,0.2,0.1 c0.2,0,0.4,0.1,0.6,0.1c0.2,0,0.4,0,0.6-0.1c0.1,0,0.1,0,0.2,0c0.1,0,0.2-0.1,0.4-0.1c0.1,0,0.1-0.1,0.2-0.1c0.1,0,0.2-0.1,0.3-0.2 c0.1,0,0.1-0.1,0.2-0.1c0,0,0.1-0.1,0.1-0.1l0.1-0.1C37.9,27.1,38,27,38,27l10.8-10.7C49.3,15.7,49.7,15,49.7,14.1z"></path>
                </svg>
            </div>
        </div>

        <!-- Total de Ventas -->
        <div class="mb-4 md:mb-6">
            <p class="text-xs md:text-sm font-medium text-yellow-600 dark:text-yellow-400">
                Total de Ventas:
            </p>
            <p class="text-2xl md:text-4xl font-bold text-yellow-700 dark:text-yellow-200 mt-1">
                ${{ formatPrice(totalWeekSales) }}
            </p>
        </div>

        <!-- Desglose Diario -->
        <div>
            <h4 class="text-xs md:text-sm font-medium text-yellow-700 dark:text-yellow-300 mb-2">
                Desglose Diario:
            </h4>
            <div class="space-y-2 max-h-[calc(100vh-300px)] overflow-y-auto">
                <div
                    v-for="sale in weekSales"
                    :key="sale.date"
                    class="flex justify-between items-center text-xs md:text-sm py-1"
                >
                    <span class="text-yellow-600 dark:text-yellow-400">
                        <span class="hidden md:inline">{{ formatDate(sale.date) }}</span>
                        <span class="md:hidden">{{ formatShortDate(sale.date) }}</span>
                    </span>
                    <span class="font-semibold text-yellow-700 dark:text-yellow-200 ml-2">
                        ${{ formatPrice(sale.total) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>