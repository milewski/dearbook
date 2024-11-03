<script setup lang="ts">
    defineProps<{ cover: string, backdrop: string, title: string, subject: string }>()
</script>

<template>

    <div class="flex justify-center before:rounded-2xl before:opacity-75 before:absolute before:from-black before:bg-gradient-to-t before:w-full before:h-5/6 before:left-0 before:bottom-0">

        <img class="rounded-2xl" :src="backdrop" alt="">

        <div class="absolute flex justify-center items-center flex-column text-white p-4 rounded-2xl bottom-28 w-6/12">

            <img class="rounded-xl h-60 mr-8 shadow-2xl border border-2" :src="cover" alt="">

            <div>

                <h1 class="text-4xl font-bold">
                    {{ title }}
                </h1>

                <p class="text-2xl mt-5">
                    {{ subject }}
                </p>

            </div>

        </div>

    </div>

</template>
