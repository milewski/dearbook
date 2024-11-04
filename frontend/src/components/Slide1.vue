<script setup lang="ts">
    defineProps<{ text: string, image: string, page: number }>()
</script>

<template>

    <div class="flex justify-center text-white p-4">

        <div class="relative before:opacity-75 before:rounded-2xl before:absolute before:from-black before:bg-gradient-to-t before:w-full before:h-96 before:left-0 before:bottom-0">

            <img class="rounded-2xl" :src="image" alt="">

            <div class="absolute opacity-45 rounded-full flex justify-center items-center text-sm font-bold bottom-4 right-4 shadow-2xl">
                {{ page }}
            </div>

        </div>

        <div class="absolute font-bold p-4 rounded-2xl text-2xl bottom-16 text-center w-11/12 lg:w-4/12">
            {{ text }}
        </div>

    </div>

</template>
