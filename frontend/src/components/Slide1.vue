<script setup lang="ts">
    defineProps<{ text: string, image: string, page: number }>()
</script>

<template>

    <div class="flex flex-col justify-center text-white p-4 h-screen items-center">

        <div class="relative before:opacity-75 before:rounded-2xl sm:before:absolute before:from-black before:bg-gradient-to-t before:w-full before:h-96 before:left-0 before:bottom-0 sm:mb-0">

            <img src="../assets/logo-outline.png" class="w-14 absolute top-10 right-10 mx-auto opacity-30" alt="">

            <img class="rounded-2xl max-h-[90vh] max-w-[90vw]" :src="image" alt="">

            <div class="absolute opacity-45 rounded-full flex justify-center items-center text-sm font-bold bottom-4 right-4 shadow-2xl">
                {{ page }}
            </div>

            <div class="absolute hidden sm:block bottom-4 font-bold p-4 rounded-2xl text-xl sm:text-2xl text-center px-12 mx-auto left-0 right-0">
                {{ text }}
            </div>

        </div>

        <div class="relative sm:hidden font-bold p-4 rounded-2xl text-xl sm:text-2xl  sm:bottom-20 text-center w-[85vw] sm:w-8/12 xl:w-4/12">
            {{ text }}
        </div>

    </div>

</template>
