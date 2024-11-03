<template>

    <Swiper
        :modules="[ Pagination, EffectCreative ]"
        :effect="'creative'"
        :creativeEffect="creativeEffect"
        :auto-height="true"
        :slides-per-view="1"
        :slidesPerGroup="1"
        :space-between="0"
        :pagination="{ dynamicBullets: true }">

        <SwiperSlide v-if="book">
            <Intro :title="book.title" :subject="book.subject" :cover="book.cover" :backdrop="book.backdrop"/>
        </SwiperSlide>

        <SwiperSlide v-for="({ illustration, text }, index) of book?.paragraphs">
            <Slide1 :key="index" :image="illustration" :text="text" :page="index + 1"/>
        </SwiperSlide>

    </Swiper>

</template>

<script lang="ts" setup>

    import { Swiper, SwiperSlide } from 'swiper/vue'
    import { Pagination, EffectCreative } from 'swiper/modules'

    import 'swiper/css'
    import 'swiper/css/pagination'
    import 'swiper/css/effect-coverflow'
    import 'swiper/css/effect-creative'

    import Slide1 from './Slide1.vue'
    import Intro from './Intro.vue'

    import { CreativeEffectOptions } from 'swiper/types'
    import { ref } from 'vue'

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

    type ViewBookResponse = {
        title: string,
        subject: string,
        cover: string,
        backdrop: string,
        paragraphs: Array<{
            illustration: string,
            text: string
        }>
    }

    const book = ref<ViewBookResponse>()

    fetch('https://api.docker.localhost/book/9')
        .then(response => response.json())
        .then((response: ViewBookResponse) => {
            book.value = response
        })

</script>

<style>
    :root {
        --swiper-pagination-bottom: 10px;
    }

    .swiper-pagination-horizontal {
        @apply bg-white p-2 rounded-full;
    }
</style>
