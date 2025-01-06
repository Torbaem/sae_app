<template>
    <AdminLayout>
        <div>
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4"
            >
                <div
                    class="border-2 border-dashed border-gray-300 rounded-lg dark:border-gray-600 h-32 md:h-64"
                >
                    <!-- Card de Usuarios (Tonos azules) -->
                    <div
                        class="bg-blue-50 dark:bg-blue-900 rounded-lg shadow p-4 h-32 md:h-64"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h3
                                    class="text-sm font-medium text-blue-600 dark:text-blue-300"
                                >
                                    Total Usuarios
                                </h3>
                                <div class="mt-1">
                                    <p
                                        class="text-3xl font-semibold text-blue-700 dark:text-blue-200"
                                    >
                                        {{ usersCount }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="p-3 bg-blue-200 dark:bg-blue-800 rounded-full"
                            >
                                <svg
                                    class="w-6 h-6 text-blue-600 dark:text-blue-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-32 md:h-64"
                >
                    <!-- Card de Mensajes Nuevos (Tonos amarillos) -->
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900 rounded-lg shadow p-4 h-32 md:h-64"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h3
                                    class="text-sm font-medium text-yellow-600 dark:text-yellow-300"
                                >
                                    Mensajes Nuevos
                                </h3>
                                <div class="mt-1">
                                    <p
                                        class="text-3xl font-semibold text-yellow-700 dark:text-yellow-200"
                                    >
                                        {{ newMessagesCount }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="p-3 bg-yellow-200 dark:bg-yellow-800 rounded-full"
                            >
                                <svg
                                    class="w-6 h-6 text-yellow-600 dark:text-yellow-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-32 md:h-64"
                >
                    <!-- Card de Productos (Tonos verdes) -->
                    <div
                        class="bg-green-50 dark:bg-green-900 rounded-lg shadow p-4 h-32 md:h-64"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h3
                                    class="text-sm font-medium text-green-600 dark:text-green-300"
                                >
                                    Total Productos
                                </h3>
                                <div class="mt-1">
                                    <p
                                        class="text-3xl font-semibold text-green-700 dark:text-green-200"
                                    >
                                        {{ productsCount }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="p-3 bg-green-200 dark:bg-green-800 rounded-full"
                            >
                                <svg
                                    class="w-6 h-6 text-green-600 dark:text-green-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-32 md:h-64"
                >
                    <!-- Card de Órdenes Pendientes (Tonos rojos) -->
                    <div
                        class="bg-red-50 dark:bg-red-900 rounded-lg shadow p-4 h-32 md:h-64"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <h3
                                    class="text-sm font-medium text-red-600 dark:text-red-300"
                                >
                                    Órdenes Pendientes
                                </h3>
                                <div class="mt-1">
                                    <p
                                        class="text-3xl font-semibold text-red-700 dark:text-red-200"
                                    >
                                        {{ pendingOrdersCount }}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="p-3 bg-red-200 dark:bg-red-800 rounded-full"
                            >
                                <svg
                                    class="w-6 h-6 text-red-600 dark:text-red-300"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                    ></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="grid grid-cols-2 gap-4 mb-4">
                <div
                    class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
                >
                <WeeklySalesCard
                    :week-sales="weekSales"
                    :total-week-sales="totalWeekSales"
                    :week-range="weekRange"
                />
                
                </div>
                <div
                    class="border-2 border-dashed rounded-lg border-gray-300 dark:border-gray-600 h-48 md:h-72"
                >
                <MonthlySalesCard
                    :initial-sales="monthSales"
                    :initial-total="totalMonthSales"
                    :sales-months="salesMonths"
                    :current-period="currentPeriod"
                />
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { initFlowbite } from "flowbite";
import AdminLayout from "./Components/AdminLayout.vue";
import WeeklySalesCard from "@/Components/WeeklySalesCard.vue";
import MonthlySalesCard from "@/Components/MonthlySalesCard.vue";

defineProps({
    usersCount: Number,
    newMessagesCount: Number,
    productsCount: Number,
    pendingOrdersCount: Number,
    processingOrdersCount: Number,
    weekSales: Array,
    totalWeekSales: Number,
    weekRange: Object,
    monthSales: Array,
    totalMonthSales: Number,
    salesMonths: Array,
    currentPeriod: Object,
});




onMounted(() => {
    initFlowbite();
});
</script>
