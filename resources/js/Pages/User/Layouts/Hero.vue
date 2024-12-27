<template>
    <div>
        <section
            class="relative bg-white dark:bg-gray-900"
            :style="{
                backgroundImage: 'url(/img/Cafe-SierraAltaEspecial-Cultivo.jpg)',
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
                        Venta de Café especial Xilitla!
                    </h1>
                    <p
                        class="max-w-2xl mb-6 font-light text-gray-200 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400"
                    >
                        Resaltar la calidad única y distintiva del café
                        cultivado, especialmente si ha sido reconocido como
                        «café especial».
                    </p>
                    <a
                        href="#"
                        class="inline-flex items-center justify-center px-5 py-3 text-base font-medium text-center text-white bg-red-600 border border-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800"
                    >
                        Compra!
                    </a>
                </div>

                <!-- Contenedor de la imagen  responsivo -->
                <div class="lg:col-span-5 flex justify-center items-center lg:items-end">
                    <img
                        src="img/Cafe_xilitla.png"
                        alt="mockup"
                        class="w-full h-auto max-w-sm lg:max-w-full object-contain"
                    />
                </div>
            </div>
        </section>
    </div>
</template>
