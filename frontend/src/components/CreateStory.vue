<template>

    <Drawer direction="bottom" :open="createDrawerState" @update:open="createDrawerState = $event">

        <DrawerTrigger as-child>

            <Button size="lg" class=" z-20 px-8 py-6 top-8 right-8 rounded-full text-2xl bg-[#230202] shadow-2xl">
                Generate your AI story now!
            </Button>

        </DrawerTrigger>

        <DrawerContent>

            <ScrollArea class="rounded-md border border-none w-full mx-auto h-[80vh]">

                <div class="flex flex-col justify-center items-center mx-auto w-full px-8 max-w-4xl">

                    <DrawerHeader class="my-8">

                        <DrawerTitle class="text-4xl text-center">
                            Create Your Own Adventure!
                        </DrawerTitle>

                        <DrawerDescription class="text-center">
                            Imagine a story and watch it come to life with beautiful <br>
                            illustrations and fun characters.
                        </DrawerDescription>

                    </DrawerHeader>

                </div>

                <div class="mx-auto w-full px-8 max-w-4xl" v-if="fakeWallet && userBooks.length > 0">

                    <div class="rounded-2xl bg-gray-100 p-1">

                        <Swiper
                            class="rounded-2xl"
                            :modules="[ Pagination, Keyboard, Mousewheel ]"
                            :mousewheel="true"
                            :keyboard="{ enabled: true, onlyInViewport: false }"
                            :centered-slides="true"
                            :slides-per-view="3"
                            :space-between="10">

                            <SwiperSlide v-for="book in userBooks" class="space-y-2 cursor-pointer">

                                <div v-if="book.type === 'placeholder'" class="animate-pulse">

                                    <img class="rounded-2xl opacity-50 cursor-progress"
                                         src="../assets/placeholder.png"
                                         alt="">

                                    <div
                                        class="absolute top-0 bottom-0 m-auto w-full flex flex-col justify-center items-center">

                                        <LoaderPinwheel :size="50" class="text-white animate-spin"/>

                                        <div>
                                            Generating...
                                        </div>

                                    </div>

                                </div>

                                <div v-if="book.type === 'failed'" class="bg-red-500/90 rounded-2xl">

                                    <img class="rounded-2xl opacity-25 cursor-progress"
                                         src="../assets/placeholder.png"
                                         alt="">

                                    <div
                                        class="absolute w-full flex top-0 bottom-0 m-auto justify-center items-center ">

                                        <HoverCard :open-delay="0">

                                            <HoverCardTrigger as-child>

                                                <ClockAlertIcon :size="50" class="text-white"/>

                                            </HoverCardTrigger>

                                            <HoverCardContent class="text-center prose-sm space-y-4">

                                                <div>{{ book.reason }}</div>

                                                <Button class="w-full" variant="destructive"
                                                        @click="deleteMyBook(book.id)">
                                                    Ok
                                                </Button>

                                            </HoverCardContent>

                                        </HoverCard>

                                    </div>

                                </div>

                                <div v-if="!!book.cover" @click="viewBook(book.id)">

                                    <div class="relative group">

                                        <div :class="{ 'before:opacity-25': book.id === loading }"
                                             class="group-hover:before:opacity-25 before:opacity-0 before:rounded-2xl before:transition-all before:absolute before:bg-black before:w-full before:h-full before:left-0 before:bottom-0"/>

                                        <EyeIcon
                                            v-if="book.id !== loading"
                                            :size="50"
                                            class="scale-75 opacity-0 group-hover:opacity-100 group-hover:scale-100 transition-all absolute text-white top-0 bottom-0 m-auto w-full justify-center items-center"/>

                                        <LoaderPinwheel
                                            v-if="book.id === loading"
                                            :size="50"
                                            class="absolute text-white top-0 bottom-0 m-auto w-full justify-center items-center mr-2 animate-spin"/>

                                        <img class="rounded-2xl object-cover" :src="book.cover" alt="">

                                    </div>

                                </div>


                            </SwiperSlide>

                        </Swiper>

                    </div>

                </div>

                <div class="mx-auto w-full max-w-4xl pb-8 px-4" v-if="fakeWallet">

                    <DrawerFooter>

                        <Tabs default-value="simple" class="w-full">

                            <TabsList class="grid w-full grid-cols-2 rounded-full">

                                <TabsTrigger value="simple" class="rounded-full">
                                    Simple
                                </TabsTrigger>

                                <TabsTrigger value="advanced" class="rounded-full">
                                    Advanced
                                </TabsTrigger>

                            </TabsList>

                            <TabsContent value="simple">
                                <Simple @on-response="onResponse" :fakeWallet="fakeWallet"/>
                            </TabsContent>

                            <TabsContent value="advanced">
                                <Advanced @on-response="onResponse" :fakeWallet="fakeWallet"/>
                            </TabsContent>

                        </Tabs>


                    </DrawerFooter>

                </div>

            </ScrollArea>

        </DrawerContent>

    </Drawer>

</template>

<script setup lang="ts">

    import { Button } from '../../@/components/ui/button'
    import { ClockAlertIcon, EyeIcon, LoaderPinwheel } from 'lucide-vue-next'
    import { Drawer, DrawerContent, DrawerDescription, DrawerFooter, DrawerHeader, DrawerTitle, DrawerTrigger } from '../../@/components/ui/drawer'
    import { ref } from 'vue'
    import { useLocalStorage } from '@vueuse/core'
    import { Keyboard, Mousewheel, Pagination } from 'swiper/modules'
    import { Swiper, SwiperSlide } from 'swiper/vue'
    import { BookIndexResource, deleteBook, myBooks } from '../api.ts'
    import { HoverCard, HoverCardContent, HoverCardTrigger } from '../../@/components/ui/hover-card'
    import { Tabs, TabsContent, TabsList, TabsTrigger } from '../../@/components/ui/tabs'
    import { ScrollArea } from '../../@/components/ui/scroll-area'
    import Advanced from './forms/Advanced.vue'
    import Simple from './forms/Simple.vue'

    export type UserBookType = Array<BookIndexResource | { id: string, type: 'placeholder' } | {
        id: string,
        reason: string,
        type: 'failed'
    }>

    const props = defineProps<{ viewBook: (bookId: number) => Promise<void>, loading: number | undefined }>()

    const createDrawerState = ref(false)

    const fakeWallet = useLocalStorage<Record<string, boolean | BookIndexResource>>('fake_wallet', makeFakeWallet(12))

    function makeFakeWallet(length: number): string {

        let result = ''

        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
        const charactersLength = characters.length

        let counter = 0

        while (counter < length) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength))
            counter += 1
        }

        return result
    }

    async function deleteMyBook(id: string) {

        userBooks.value = userBooks.value.filter(book => book.id !== id)

        await deleteBook(id, fakeWallet.value!).catch(console.error)

    }

    async function refresh() {

        const placeholders = userBooks.value.filter(book => book.type === 'placeholder')

        if (placeholders.length || userBooks.value.length === 0) {

            await myBooks(fakeWallet.value)
                .then((response: Record<string, BookIndexResource>) => {

                    userBooks.value = []

                    for (const key in response) {

                        if (typeof response[ key ] === 'object') {

                            userBooks.value.push(response[ key ])

                        }

                        if (response[ key ] === true) {

                            userBooks.value.push({
                                id: key,
                                type: 'placeholder',
                            })

                        }

                        if (typeof response[ key ] === 'string') {

                            userBooks.value.push({
                                id: key,
                                reason: response[ key ],
                                type: 'failed',
                            })

                        }

                    }

                })
                .catch(console.log)
                .finally(() => setTimeout(refresh, 1000 * 10))

        } else {

            setTimeout(refresh, 1000 * 10)

        }

    }

    const userBooks = ref<UserBookType>([])

    function onResponse(placeholder) {
        userBooks.value.unshift(placeholder)
    }

    refresh()

</script>
