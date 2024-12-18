<template>

    <div v-if="book" class="book relative min-h-dvh flex justify-center items-center bg-[#242424] h-dvh">

        <div @click="$emit('close')"
             class="fixed z-50 bg-white w-10 h-10 rounded-full flex justify-center items-center top-10 right-5 cursor-pointer transition-all hover:scale-95 active:scale-75 transform-gpu">

            <X :size="20"/>

        </div>

        <Swiper
            @key-press="closeOnEsc"
            :modules="[ Pagination, EffectCreative, Keyboard, Mousewheel ]"
            :mousewheel="true"
            :effect="'creative'"
            :keyboard="{ enabled: true, onlyInViewport: false }"
            :creative-effect="creativeEffect"
            :auto-height="true"
            :slides-per-view="1"
            :slides-per-group="1"
            :space-between="0"
            :pagination="{ dynamicBullets: true }">

            <SwiperSlide>
                <Intro v-bind="book"/>
            </SwiperSlide>

            <SwiperSlide v-for="({ illustration, text }, index) of book?.paragraphs">
                <Slide1 :key="index" :image="illustration" :text="text" :page="index + 1"/>
            </SwiperSlide>

            <SwiperSlide>
                <LastPage/>
            </SwiperSlide>

        </Swiper>

    </div>

</template>

<script lang="ts" setup>

    import { Swiper, SwiperSlide } from 'swiper/vue'
    import { Pagination, EffectCreative, Keyboard, Mousewheel } from 'swiper/modules'
    import { CreativeEffectOptions, Swiper as SwiperType } from 'swiper/types'
    import { X } from 'lucide-vue-next'
    import Slide1 from './Slide1.vue'
    import Intro from './Intro.vue'

    import 'swiper/css'
    import 'swiper/css/pagination'
    import 'swiper/css/keyboard'
    import 'swiper/css/effect-creative'
    import { BookDetailResource } from '../api.ts'
    import LastPage from './LastPage.vue'

    const emit = defineEmits([ 'close' ])

    function closeOnEsc(swiper: SwiperType, keyCode: string): void {
        if (parseInt(keyCode) === 27) {
            emit('close')
        }
    }

    defineProps<{ book: BookDetailResource }>()

    const creativeEffect: Partial<CreativeEffectOptions> = {
        perspective: true,
        prev: {
            opacity: 0,
            translate: [ '-20%', '-25%', 0 ],
            rotate: [ 0, 0, 20 ],
            scale: -0.001,
        },
        next: {
            opacity: 1,
            translate: [ '100%', 0, 100 ],
            rotate: [ -60, 0, -20 ],
        },
    }

</script>

<style>

    .book .swiper-pagination-horizontal {

        --swiper-pagination-bottom: 35px;

        @apply bg-white p-2 rounded-full;

    }

</style>
