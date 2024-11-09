import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./node_modules/flowbite/**/*.js"
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'dark-brown': '#5e3929',  // Marrón Café Oscuro
                'ochre': '#cd8c52',       // Ocre
                'sage-green': '#b7d1a3',  // Verde Salvia Claro (ya incluido)
                'beige': '#f3e7d3',       // Beige Suave
                'moss-green': '#8b9b71',  // Verde Musgo
                'terracotta': '#a35c39',  // Terracota
                'cream': '#f5f0e6',       // Crema
                'slate-gray': '#5a5a5a',  // Gris Pizarra
            },

        },
    },

    plugins: [forms, require("flowbite/plugin")],
};
