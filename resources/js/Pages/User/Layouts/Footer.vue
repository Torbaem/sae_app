<template>
<footer class="bg-dark-brown text-beige-soft">
    <div class="max-w-screen-xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Sección Sobre Nosotros -->
            <div>
                <h3 class="text-lg font-semibold text-ocre mb-4">Sobre Nosotros</h3>
                <p class="text-gray-300">
                    Somos una cooperativa dedicada a la venta de café artesanal de la más alta calidad, comprometidos con prácticas sostenibles y el apoyo a los productores locales.
                </p>
            </div>
            
            <!-- Sección Enlaces -->
            <div>
                <h3 class="text-lg font-semibold text-ocre mb-4">Enlaces Útiles</h3>
                <ul class="space-y-2">
                    <li><a href="/" class="hover:text-ocre">Inicio</a></li>
                    <li><a href="/nosotros" class="hover:text-ocre">Nosotros</a></li>
                    <li><a href="/products" class="hover:text-ocre">Tienda</a></li>
                    <li><a href="/contactanos" class="hover:text-ocre">Contacto</a></li>
                </ul>
            </div>
            
            <!-- Sección Contacto -->
            <div>
                <h3 class="text-lg font-semibold text-ocre mb-4">Contacto</h3>
                <ul class="space-y-2">
                    <li class="text-gray-300">Teléfono: +52 4891074086 </li>
                    <li class="text-gray-300">Email: altasierracafexilitla@outlook.com
                        <br>cmaltasierra@gmail.com</li>
                    <li class="text-gray-300">Dirección: Xilitla, San Luis Potosí</li>
                </ul>
            </div>
            
            <!-- Sección Redes Sociales -->
            <div>
                <h3 class="text-lg font-semibold text-ocre mb-4">Síguenos</h3>
                <div class="flex space-x-6">
                    <a href="#" class="text-beige-soft hover:text-ocre">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.04C6.478 2.04 2 6.518 2 12.041c0 4.987 3.657 9.12 8.437 9.88v-7H7.898v-2.88h2.539V9.41c0-2.507 1.492-3.89 3.77-3.89 1.093 0 2.236.195 2.236.195v2.461h-1.257c-1.237 0-1.621.766-1.621 1.548v1.854h2.765l-.442 2.88h-2.323v7.04c4.78-.76 8.437-4.893 8.437-9.88C22 6.518 17.522 2.04 12 2.04z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-beige-soft hover:text-ocre">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22.23 5.924a8.12 8.12 0 01-2.357.646 4.107 4.107 0 001.804-2.27 8.194 8.194 0 01-2.605.996 4.086 4.086 0 00-6.963 3.724A11.6 11.6 0 013.156 4.6a4.086 4.086 0 001.264 5.457 4.07 4.07 0 01-1.852-.512v.05a4.086 4.086 0 003.279 4.005 4.08 4.08 0 01-1.847.07 4.087 4.087 0 003.82 2.837A8.192 8.192 0 012 19.55a11.58 11.58 0 006.29 1.844c7.547 0 11.675-6.251 11.675-11.674 0-.178-.004-.355-.012-.532A8.348 8.348 0 0022.23 5.924z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-beige-soft hover:text-ocre">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2.163c3.204 0 3.584.012 4.849.07 1.366.062 2.633.332 3.608 1.308.975.975 1.246 2.242 1.308 3.608.059 1.265.07 1.645.07 4.849 0 3.204-.012 3.584-.07 4.849-.062 1.366-.332 2.633-1.308 3.608-.975.975-2.242 1.246-3.608 1.308-1.265.059-1.645.07-4.849.07-3.204 0-3.584-.012-4.849-.07-1.366-.062-2.633-.332-3.608-1.308-.975-.975-1.246-2.242-1.308-3.608-.059-1.265-.07-1.645-.07-4.849 0-3.204.012-3.584.07-4.849.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.246 3.608-1.308 1.265-.059 1.645-.07 4.849-.07M12 0C8.741 0 8.332.012 7.052.07 5.77.129 4.548.384 3.468 1.463c-1.08 1.08-1.335 2.302-1.394 3.584C2.012 6.948 2 7.358 2 10.5s.012 3.552.07 4.849c.059 1.282.314 2.504 1.394 3.584 1.08 1.08 2.302 1.335 3.584 1.394 1.282.059 1.692.07 4.849.07 3.204 0 3.584-.012 4.849-.07 1.366-.062 2.633-.332 3.608-1.308.975-.975 1.246-2.242 1.308-3.608.059-1.265.07-1.645.07-4.849s-.012-3.584-.07-4.849c-.062-1.366-.332-2.633-1.308-3.608-.975-.975-2.242-1.246-3.608-1.308C15.584.012 15.204 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zm0 10.162a3.999 3.999 0 110-7.998 3.999 3.999 0 010 7.998zm6.406-11.345a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-gray-700 pt-8">
            <p class="text-center text-sm text-gray-400">&copy; 2024 Cooperativa Café. Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

</template>