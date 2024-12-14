<template>

    <Drawer direction="bottom" :open="createDrawerState" @update:open="createDrawerState = $event">

        <DrawerTrigger as-child>

            <Button size="lg" class=" z-20 px-8 py-6 top-8 right-8 rounded-full text-2xl bg-[#230202] shadow-2xl">
                Create Your Own Adventure!
            </Button>

        </DrawerTrigger>

        <DrawerContent>

            <div class="mx-auto w-full px-8 max-w-4xl">

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

            <div class="mx-auto w-full px-8 max-w-4xl" v-if="userBooks.length > 0">

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

                            <div v-if="book.type === 'placeholder'">

                                <img class="rounded-2xl opacity-25 animate-pulse cursor-progress"
                                     src="../assets/placeholder.png"
                                     alt="">

                            </div>

                            <div v-else @click="viewBook(book.id)">

                                <div class="relative">

                                    <div v-if="book.id === loading"
                                         class="before:opacity-50 before:rounded-2xl before:absolute before:bg-black before:w-full before:h-full before:left-0 before:bottom-0 animate-pulse"/>

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

            <div class="mx-auto w-full max-w-4xl pb-8 px-4">

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

                                <Button type="submit" :disabled="formLoading"
                                        class="bg-[#F18533] hover:bg-[#F18533] rounded-full">

                                    <div>Create!</div>

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
    import { LoaderPinwheel } from 'lucide-vue-next'
    import { Drawer, DrawerContent, DrawerDescription, DrawerFooter, DrawerHeader, DrawerTitle, DrawerTrigger } from '../../@/components/ui/drawer'
    import { toTypedSchema } from '@vee-validate/zod'
    import * as z from 'zod'
    import { useForm } from 'vee-validate'
    import { computed, ref, watch } from 'vue'
    import { useLocalStorage } from '@vueuse/core'
    import { Keyboard, Mousewheel, Pagination } from 'swiper/modules'
    import { Swiper, SwiperSlide } from 'swiper/vue'
    import { BookIndexResource, checkBatches, createBook } from '../api.ts'
    import { randomSearchTerm } from '../utilities.ts'

    const props = defineProps<{ viewBook: (bookId: number) => Promise<void>, loading: number | undefined }>()

    const formSchema = toTypedSchema(z.object({ prompt: z.string().max(500).optional() }))
    const form = useForm({ validationSchema: formSchema })
    const formLoading = ref(false)
    const createDrawerState = ref(false)

    const storage = useLocalStorage<Record<string, boolean | BookIndexResource>>('generation', {})
    const randomBookTitle = ref()

    const userBooks = computed<Array<BookIndexResource | { id: string, type: 'placeholder' }>>(function () {

        return Object.keys(storage.value).reverse().map((key: string) => {

            if (typeof storage.value[ key ] === 'boolean') {

                return {
                    id: key,
                    type: 'placeholder',
                }

            } else {

                return storage.value[ key ]

            }

        })

    })

    watch(createDrawerState, () => {
        randomBookTitle.value = randomSearchTerm()
    })

    async function refresh() {

        const currentInStorage = Object.keys(storage.value).filter(key => typeof storage.value[ key ] === 'boolean')

        if (currentInStorage.length) {

            await checkBatches(currentInStorage)
                .then((response: Record<string, BookIndexResource>) => {

                    for (const key in response) {

                        storage.value[ key ] = response[ key ]

                    }

                })
                .finally(() => setTimeout(refresh, 1000 * 10))

        } else {

            setTimeout(refresh, 1000 * 10)

        }

    }

    const onSubmit = form.handleSubmit(async values => {

        formLoading.value = true

        await createBook(values.prompt || randomBookTitle.value)
            .then(response => {

                if (response.id) {
                    storage.value[ response.id ] = false
                }

            })
            .finally(() => {
                formLoading.value = false
                randomBookTitle.value = randomSearchTerm()
                form.resetForm()
            })

    })

    refresh()

</script>