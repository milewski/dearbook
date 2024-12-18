<template>

    <div class="flex justify-center p-4 h-dvh items-center">

        <div class="w-full h-full flex justify-center items-center flex-colum rounded-2xl text-black">

            <div class="flex bg-white sm:w-full sm:h-full sm:max-w-[768px] sm:max-h-[768px]  rounded-2xl mx-4 flex-col justify-center items-center p-10 text-center sm:text-left absolute sm:relative">

                <img src="../assets/logo.png" class="w-40 mb-10 sm:mb-16" alt="dearbook">

                <h1 class="text-3xl sm:text-4xl text-center font-bold *:hidden *:sm:block">
                    This book was created by DearBook, <br> an AI-powered children's book <br> generating platform
                </h1>

                <p class="sm:text-2xl mt-2 sm:mt-5 text-center">

                    Turn your ideas into books at <br>

                    <a href="https://dearbook.fun" target="_blank" class="text-[#F18533] font-sans">
                        https://dearbook.fun
                    </a>

                </p>


            </div>

        </div>

    </div>

</template>
<script setup lang="ts">
    import { AspectRatio } from '../../@/components/ui/aspect-ratio'
</script>