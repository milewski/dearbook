<script setup lang="ts">
    defineProps<{ cover: string, backdrop: string, title: string, synopsis: string }>()
</script>

<template>

    <div class="flex justify-center p-4 h-screen items-center">

        <div class="relative before:rounded-2xl before:opacity-75 before:absolute before:from-black before:bg-gradient-to-t before:w-full before:h-5/6 before:left-0 before:bottom-0 mb-[10vh] sm:mb-0">

            <img class="rounded-2xl max-[80vw] max-h-[80vh]" :src="backdrop" alt="">

            <div class="absolute flex justify-center items-center flex-column text-white p-4 rounded-2xl bottom-5 w-full">

                <img class="rounded-xl h-80 mr-4 shadow-2xl border-4 hidden lg:block" :src="cover" alt="">

                <div class="hidden lg:block px-4 lg:w-7/12 text-center lg:text-left absolute lg:relative">

                    <h1 class="text-2xl sm:text-4xl font-bold">
                        {{ title }}
                    </h1>

                    <p class="sm:text-2xl mt-2 sm:mt-5">
                        {{ synopsis }}
                    </p>

                </div>

            </div>

            <div class="block lg:hidden text-white mt-4 px-4 lg:w-7/12 text-center lg:text-left absolute lg:relative">

                <h1 class="text-3xl sm:text-4xl font-bold">
                    {{ title }}
                </h1>

                <p class="mt-2">
                    {{ synopsis }}
                </p>

            </div>

        </div>

    </div>

</template>
