<template>

    <div v-if="book" class="book relative min-h-dvh flex justify-center items-center bg-[#242424] h-dvh">

        <div class="fixed flex space-x-4 z-50 top-10 right-5">

            <Popover @update:open="onPopoverToggle">

                <PopoverTrigger>

                    <div @click="$emit('share')"
                         class="bg-white w-10 h-10 rounded-full flex justify-center items-center cursor-pointer transition-all hover:scale-95 active:scale-75 transform-gpu">
                        <Share :size="20"/>
                    </div>

                </PopoverTrigger>

                <PopoverContent align="end" class="sm:w-96">

                    <div class="grid gap-4" v-if="copied === false">

                        <div class="flex justify-between">
                            <Label>Sharable link</Label>
                        </div>

                        <Input type="text" @focus="$event.target.select()" readonly :default-value="sharableBookUrl"/>

                        <Button @click="copyToClipboard">
                            Copy to clipboard
                        </Button>

                    </div>

                    <div v-else class="flex flex-col justify-center items-center space-y-4">
                        <div>Link copied to clipboard!</div>
                        <ClipboardCheck :size="40" class="text-green-600"/>
                    </div>

                </PopoverContent>

            </Popover>

            <div @click="$emit('close')"
                 class="bg-white w-10 h-10 rounded-full flex justify-center items-center cursor-pointer transition-all hover:scale-95 active:scale-75 transform-gpu">

                <X :size="20"/>

            </div>

        </div>

        <Swiper
            @swiper="onSwiper"
            @key-press="closeOnEsc"
            :modules="[ Pagination, EffectCreative, Keyboard, Mousewheel, Navigation ]"
            :mousewheel="true"
            :navigation="true"
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
    import { EffectCreative, Keyboard, Mousewheel, Navigation, Pagination } from 'swiper/modules'
    import { CreativeEffectOptions, Swiper as SwiperType } from 'swiper/types'
    import { ClipboardCheck, Share, X } from 'lucide-vue-next'
    import Slide1 from './Slide1.vue'
    import Intro from './Intro.vue'
    import { Popover, PopoverContent, PopoverTrigger } from '../../@/components/ui/popover'
    import { Input } from '../../@/components/ui/input'
    import { Label } from '../../@/components/ui/label'

    import 'swiper/css'
    import 'swiper/css/pagination'
    import 'swiper/css/keyboard'
    import 'swiper/css/effect-creative'
    import 'swiper/css/navigation'

    import { BookDetailResource } from '../api.ts'
    import LastPage from './LastPage.vue'
    import { Button } from '../../@/components/ui/button'
    import { ref } from 'vue'

    const props = defineProps<{ book: BookDetailResource }>()
    const sharableBookUrl = `https://${ window.location.host }?book=${ props.book.id }`
    const emit = defineEmits([ 'close' ])
    const copied = ref(false)

    function closeOnEsc(swiper: SwiperType, keyCode: string): void {
        if (parseInt(keyCode) === 27) {
            emit('close')
        }
    }

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
            translate: [ '100%', 0, 200 ],
            rotate: [ -60, 0, -20 ],
        },
    }

    function copyToClipboard() {

        navigator.clipboard.writeText(window.location.href).then(function () {
            copied.value = true
        })

    }

    function onPopoverToggle(state) {

        if (state === false) {

            setTimeout(() => {
                copied.value = false
            }, 150)

        }

    }

</script>

<style>

    :root {
        --swiper-navigation-size: 0px;
        --swiper-navigation-top-offset: 50%;
        --swiper-navigation-sides-offset: 50px;
        --swiper-navigation-color: white;

        --swiper-pagination-bottom: 35px;
    }

    @screen sm {

        :root {
            --swiper-navigation-size: 44px;
        }

    }

    .book .swiper-pagination-horizontal {

        @apply bg-white p-2 rounded-full;

    }

</style>
