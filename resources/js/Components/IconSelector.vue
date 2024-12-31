<!-- IconSelector.vue -->
<template>
    <div class="icon-selector">
        <!-- Campo de búsqueda -->
        <div class="mb-4">
            <input
                type="text"
                v-model="searchQuery"
                class="w-full px-4 py-2 border rounded-lg"
                placeholder="Buscar icono..."
            />
        </div>

        <!-- Grid de iconos -->
        <div class="grid grid-cols-6 gap-4 max-h-60 overflow-y-auto">
            <button
                v-for="(icon, name) in filteredIcons"
                :key="name"
                @click="selectIcon(name)"
                class="p-2 border rounded-lg hover:bg-gray-100 flex flex-col items-center"
                :class="{ 'bg-blue-100': selectedIcon === name }"
            >
                <component :is="icon" class="h-6 w-6" />
                <span class="text-xs mt-1 text-gray-600">{{
                    formatIconName(name)
                }}</span>
            </button>
        </div>

        <!-- Icono seleccionado -->
        <div v-if="selectedIcon" class="mt-4 p-4 border rounded-lg">
            <p class="text-sm text-gray-600 mb-2">Icono seleccionado:</p>
            <div class="flex items-center gap-2">
                <component :is="icons[selectedIcon]" class="h-6 w-6" />
                <span>{{ formatIconName(selectedIcon) }}</span>
            </div>
        </div>
    </div>
</template>

<script>
import * as icons from "@heroicons/vue/24/solid";

export default {
    name: "IconSelector",
    data() {
        return {
            icons,
            searchQuery: "",
            selectedIcon: null,
        };
    },
    computed: {
        filteredIcons() {
            const query = this.searchQuery.toLowerCase();
            return Object.entries(this.icons).reduce(
                (filtered, [name, icon]) => {
                    if (name.toLowerCase().includes(query)) {
                        filtered[name] = icon;
                    }
                    return filtered;
                },
                {}
            );
        },
    },
    methods: {
        selectIcon(iconName) {
            this.selectedIcon = iconName;
            this.$emit("icon-selected", {
                name: iconName,
                component: this.icons[iconName],
            });
        },
        formatIconName(name) {
            return name
                .replace(/([A-Z])/g, " $1")
                .trim()
                .replace(/Icon$/, "");
        },
    },
};
</script>
