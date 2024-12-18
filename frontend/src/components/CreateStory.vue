<template>

    <Drawer direction="bottom" :open="createDrawerState" @update:open="createDrawerState = $event">

        <DrawerTrigger as-child>

            <Button size="lg" class=" z-20 px-8 py-6 top-8 right-8 rounded-full text-2xl bg-[#230202] shadow-2xl">
                Generate your AI story now!
            </Button>

        </DrawerTrigger>

        <DrawerContent>

            <div v-if="!isPhantomInstalled" class="flex flex-col justify-center items-center mx-auto w-full px-8 max-w-4xl">

                <DrawerHeader class="my-8">

                    <DrawerTitle class="text-4xl text-center  flex flex-col justify-center items-center space-y-4">

                        <Phantom class="w-80 h-20"/>
                        <div>Have you installed Phantom?</div>

                    </DrawerTitle>

                    <DrawerDescription class="text-center">
                        Select from your preferred options below:
                    </DrawerDescription>

                </DrawerHeader>

                <div class="flex flex-col space-y-2 mb-20">

                    <a href="https://chromewebstore.google.com/detail/phantom/bfnaelmomeimhlpmgjnjophhpkkoljpa"
                       target="_blank"
                       class="bg-gray-200 hover:bg-gray-300 transition rounded-full py-2 px-4 flex space-x-2">
                        <Chrome class="size-6"/>
                        <div>Install Chrome extension</div>
                    </a>

                    <a href="https://chromewebstore.google.com/detail/phantom/bfnaelmomeimhlpmgjnjophhpkkoljpa"
                       target="_blank"
                       class="bg-gray-200 hover:bg-gray-300 transition rounded-full py-2 px-4 flex space-x-2">
                        <Brave class="size-6"/>
                        <div>Install Brave extension</div>
                    </a>

                    <a href="https://addons.mozilla.org/en-US/firefox/addon/phantom-app"
                       target="_blank"
                       class="bg-gray-200 hover:bg-gray-300 transition rounded-full py-2 px-4 flex space-x-2">
                        <Firefox class="size-6"/>
                        <div>Install Firefox extension</div>
                    </a>

                </div>

            </div>

            <div v-else class="flex flex-col justify-center items-center mx-auto w-full px-8 max-w-4xl">

                <DrawerHeader class="my-8">

                    <DrawerTitle class="text-4xl text-center">
                        Create Your Own Adventure!
                    </DrawerTitle>

                    <DrawerDescription class="text-center">
                        Imagine a story and watch it come to life with beautiful <br>
                        illustrations and fun characters.
                    </DrawerDescription>

                </DrawerHeader>

                <Button
                    v-if="!wallet"
                    @click="login"
                    class="bg-[#F18533] hover:bg-[#F98533] transition py-2 px-4 flex space-x-2 rounded-full mb-20">
                    Connect your phantom wallet to start creating!
                </Button>

            </div>

            <div class="mx-auto w-full px-8 max-w-4xl" v-if="wallet && userBooks.length > 0">

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

                                <div class="absolute top-0 bottom-0 m-auto w-full flex flex-col justify-center items-center">

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

                                <div class="absolute w-full flex top-0 bottom-0 m-auto justify-center items-center ">

                                    <HoverCard :open-delay="0">

                                        <HoverCardTrigger as-child>

                                            <ClockAlertIcon :size="50" class="text-white"/>

                                        </HoverCardTrigger>

                                        <HoverCardContent class="text-center prose-sm space-y-4">

                                            <div>{{ book.reason }}</div>

                                            <Button class="w-full" variant="destructive" @click="deleteMyBook(book.id)">
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

            <div class="mx-auto w-full max-w-4xl pb-8 px-4" v-if="wallet">

                <DrawerFooter>

                    <form @submit="onSubmit">

                        <div class="grid w-full gap-2">

                            <FormField v-slot="{ componentField }" name="prompt">

                                <FormItem>

                                    <FormControl>

                                        <Textarea
                                            :placeholder="randomBookTitle"
                                            v-bind="componentField"
                                            rows="5"
                                            class="rounded-2xl"/>

                                    </FormControl>

                                    <FormMessage/>

                                </FormItem>

                                <Button type="submit"
                                        :disabled="formLoading || !(form.isFieldValid('prompt') && form.values.prompt)"
                                        class="bg-[#F18533] hover:bg-[#F18533] rounded-full">

                                    <div v-if="wallet">Create!</div>
                                    <div v-else>Connect Wallet and Create!</div>

                                    <LoaderPinwheel v-if="formLoading" class="animate-spin"/>

                                </Button>

                            </FormField>

                        </div>

                    </form>

                </DrawerFooter>

            </div>

        </DrawerContent>

    </Drawer>

</template>

<script setup lang="ts">

    import { Button } from '../../@/components/ui/button'
    import { FormControl, FormField, FormItem, FormMessage } from '../../@/components/ui/form'
    import { Textarea } from '../../@/components/ui/textarea'
    import { LoaderPinwheel, EyeIcon, ClockAlertIcon } from 'lucide-vue-next'
    import { Drawer, DrawerContent, DrawerDescription, DrawerFooter, DrawerHeader, DrawerTitle, DrawerTrigger } from '../../@/components/ui/drawer'
    import { toTypedSchema } from '@vee-validate/zod'
    import * as z from 'zod'
    import { useForm } from 'vee-validate'
    import { computed, ref, watch } from 'vue'
    import { useLocalStorage } from '@vueuse/core'
    import { Keyboard, Mousewheel, Pagination } from 'swiper/modules'
    import { Swiper, SwiperSlide } from 'swiper/vue'
    import { BookIndexResource, checkBatches, createBook, deleteBook, myBooks } from '../api.ts'
    import { randomSearchTerm } from '../utilities.ts'
    import { HoverCard, HoverCardContent, HoverCardTrigger } from '../../@/components/ui/hover-card'
    import Phantom from '../icons/Phantom.vue'
    import Chrome from '../icons/Chrome.vue'
    import Brave from '../icons/Brave.vue'
    import Firefox from '../icons/Firefox.vue'

    const props = defineProps<{ viewBook: (bookId: number) => Promise<void>, loading: number | undefined }>()

    const formSchema = toTypedSchema(z.object({ prompt: z.string().max(500).min(10).optional() }))
    const form = useForm({ validationSchema: formSchema })
    const formLoading = ref(false)
    const createDrawerState = ref(false)
    const wallet = ref(null)

    const storage = useLocalStorage<Record<string, boolean | BookIndexResource>>('generation', {})
    const randomBookTitle = ref()

    const getProvider = () => {

        if ('phantom' in window) {

            const provider = window.phantom?.solana

            if (provider?.isPhantom) {
                return provider
            }

        }

    }

    const isPhantomInstalled = window.phantom?.solana?.isPhantom

    async function deleteMyBook(id: string) {

        userBooks.value = userBooks.value.filter(book => book.id !== id)

        await deleteBook(id, wallet.value!).catch(console.error)

    }

    async function login() {

        const provider = getProvider()

        if (provider) {

            provider.on('connect', publicKey => {
                wallet.value = publicKey
            })

            provider.on('disconnect', () => {
                wallet.value = null
            })

            provider.on('accountChanged', async publicKey => {

                wallet.value = publicKey

                if (!publicKey) {
                    await provider.connect()
                }

            })

        }

        try {

            await provider.connect()

            await myBooks(wallet.value!).then(response => {

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

            createDrawerState.value = true

        } catch (error) {
            // { code: 4001, message: 'User rejected the request.' }
        }

    }

    const userBooks = ref<Array<BookIndexResource | { id: string, type: 'placeholder' } | {
        id: string,
        reason: string,
        type: 'failed'
    }>>([])

    watch(createDrawerState, () => {
        randomBookTitle.value = randomSearchTerm()
    })

    const onSubmit = form.handleSubmit(async values => {

        formLoading.value = true

        await createBook(values.prompt || randomBookTitle.value, wallet.value!)
            .then(response => {

                if (response.id) {

                    userBooks.value.unshift({
                        id: response.id,
                        type: 'placeholder',
                    })

                }

            })
            .finally(() => {
                formLoading.value = false
                randomBookTitle.value = randomSearchTerm()
                form.resetForm()
            })

    })

</script>