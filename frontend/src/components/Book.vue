<template>

    <div v-if="book" class="book relative min-h-dvh flex justify-center items-center bg-[#242424] h-dvh">

        <div @click="$emit('close')"
             class="fixed z-50 bg-white w-10 h-10 rounded-full flex justify-center items-center top-10 right-5 cursor-pointer transition-all hover:scale-95 active:scale-75 transform-gpu">

            <X :size="20"/>

        </div>

        <Swiper
            @swiper="onSwiper"
            @key-press="closeOnEsc"
            :modules="[ Pagination, EffectCreative, Keyboard, Mousewheel ]"
            :mousewheel="false"
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
    import { CreativeEffectOptions, type Swiper as SwiperClass, Swiper as SwiperType } from 'swiper/types'
    import { onMounted, onUnmounted, ref } from "vue";    import { X } from 'lucide-vue-next'
    import Slide1 from './Slide1.vue'
    import Intro from './Intro.vue'

    import 'swiper/css'
    import 'swiper/css/pagination'
    import 'swiper/css/keyboard'
    import 'swiper/css/effect-creative'
    import { BookDetailResource } from '../api.ts'
    import LastPage from './LastPage.vue'

    const emit = defineEmits([ 'close' ])
    const swiperInstanceRef = ref<SwiperClass>()

    function onSwiper(swiper: SwiperClass) {
      swiperInstanceRef.value = swiper
    }

    function closeOnEsc(_: SwiperType, keyCode: string): void {
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

    const isScrollingRef = ref<boolean>(false)
    const scrollStopDelayRef = ref<NodeJS.Timeout | null>(null)
    const onMouseWheel = (event: WheelEvent) => {
      if (!swiperInstanceRef.value) return

      if (!isScrollingRef.value) {
        if (event.deltaY > -0 || event.deltaX > -0) {
          swiperInstanceRef.value?.slideNext();
        } else if (event.deltaY < 0 || event.deltaX < 0) {
          swiperInstanceRef.value?.slidePrev();
        }

        isScrollingRef.value = true;
      }

      if (scrollStopDelayRef.value) clearTimeout(scrollStopDelayRef.value);
      scrollStopDelayRef.value = setTimeout(() => {
        isScrollingRef.value = false;
      }, 100);

      event.preventDefault()
    }

    onMounted(() => {
      window.addEventListener('wheel', onMouseWheel, { passive: false })
    })

    onUnmounted(() => {
      window.removeEventListener('wheel', onMouseWheel)
    })

</script>

<style>

    .book .swiper-pagination-horizontal {

        --swiper-pagination-bottom: 35px;

        @apply bg-white p-2 rounded-full;

    }

</style>
