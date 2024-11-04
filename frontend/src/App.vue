<template>

    <Toaster/>

    <Drawer
        v-if="activeBook.content"
        @update:open="activeBook.visible = $event"
        :modal="true"
        :open="activeBook.visible"
        direction="bottom"
        :dismissible="false">

        <DrawerContent>
            <Book :book="activeBook.content" @close="activeBook.visible = false"/>
        </DrawerContent>

    </Drawer>

    <CreateStory :view-book="viewBook" :loading="loading"/>

    <div class="flex flex-col justify-center items-center space-y-10 text-black">

        <img src="./assets/logo.png" class="w-44 my-10" alt="">

        <h1 class="text-5xl sm:text-6xl text-center font-serif max-w-[750px] text-[#230202] pb-4">
            Discover Thousands of Magical Stories, or Create a Unique Adventure!
        </h1>

        <div class="w-full max-w-4xl px-8 sm:px-0 p-2 rounded-full flex h-16">

            <div class="relative w-full flex justify-center items-center">

                <Input
                    type="text"
                    :placeholder="randomSearchTermPlaceholder"
                    class="text-[#230202] text-opacity-70 bg-[#F18533] transition-all shadow-xl placeholder:opacity-25 placeholder:text-[#230202] p-8 rounded-full text-2xl border-none h-full w-full focus-visible:ring-[#230202]"
                    :disabled="searching"
                    @keydown.enter="onSearch"
                    v-model="searchTerm"/>

                <LoaderPinwheel
                    v-if="searching"
                    :size="40"
                    class="absolute z-10 right-0 bottom-0 top-0 m-auto opacity-25 mr-3 animate-spin text-[#230202]"/>

                <div v-else
                     class="absolute z-10 text-white right-0 m-auto mr-3 opacity-45 hover:opacity-60 transition-all cursor-pointer">

                    <PackageSearch v-if="!searchTerm" :size="40" class="text-[#230202]" @click="onSearch"/>

                    <CircleX v-else :size="40" class="text-[#230202]" @click="searchTerm = null"/>

                </div>

                <!--                <span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">-->
                <!--                  <Search class="size-10 text-muted-foreground"/>-->
                <!--                </span>-->

                <!--                <div class="flex mt-4 space-x-2">-->

                <!--                    <div class="bg-[#F18533] text-[#230202] hover:bg-[#e77d2b] transition-all text-opacity-60 rounded-full py-1 px-3 text-sm font-bold font-sans" v-for="_ in 5">-->
                <!--                    </div>-->

                <!--                </div>-->

            </div>

        </div>

        <div class="space-y-4 px-8 w-full">

            <div v-if="books.length === 0"
                 class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10 text-center mt-10">

                <div v-for="_ in 4" class="space-y-2 ">

                    <div class="relative rounded-2xl border-8 border-transparent w-full 2xl:w-[360px]">

                        <AspectRatio :ratio="2/3">
                            <Skeleton class="h-full w-full rounded-2xl"/>
                        </AspectRatio>

                    </div>

                    <Skeleton class="h-6 w-10/12 left-0 right-0 mx-auto rounded-2xl"/>

                </div>

            </div>

            <div v-else
                 class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-10 text-center mt-10">

                <div v-for="(book, index) in books"
                     :id="book.id"
                     @keydown.space.prevent="viewBook(book.id)"
                     @keydown.enter.prevent="viewBook(book.id)"
                     :tabindex="0"
                     class="space-y-2 mx-auto relative cursor-pointer w-full ring-offset-background focus-visible:outline-none focus-visible:ring-2 border-none rounded-2xl focus-visible:ring-offset-2 border-none pb-2 focus-visible:ring-[#230202]"
                     @click="viewBook(book.id)">

                    <div
                        class="relative rounded-2xl transition-all hover:scale-[1.08] transform-gpu border border-8 border-transparent hover:border-[white] overflow-hidden hover:z-10 hover:shadow-2xl [&:hover~div]:underline [&:hover~div]:z-50"
                        :class="{
                            'hover:rotate-[-1deg]': index % 4 === 0,
                            'hover:rotate-[-2deg]': index % 4 === 1,
                            'hover:rotate-[3deg]':  index % 4 === 2,
                            'hover:rotate-[4deg]':  index % 4 === 3,
                        }">

                        <AspectRatio
                            :ratio="2/3"
                            class="cover-placeholder bg-[#230202] bg-opacity-25 bg-cover overflow-hidden">

                            <div v-if="book.id === loading"
                                 class="before:opacity-50 before: before:absolute before:bg-black before:w-full before:h-full before:left-0 before:bottom-0 animate-pulse"/>

                            <LoaderPinwheel
                                v-if="book.id === loading"
                                :size="50"
                                class="absolute z-10 text-white top-0 bottom-0 m-auto w-full justify-center items-center mr-2 animate-spin"/>

                            <img :src="book.cover" alt="">

                        </AspectRatio>

                    </div>

                    <div class="relative text-3xl sm:text-2xl hover:underline">
                        {{ book.title }}
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="flex justify-center mt-10 sm:mt-40 select-none">
        <img src="./assets/footer.png" class="sm:w-8/12" alt="">
    </div>

</template>

<script lang="ts" setup>

    import Book from './components/Book.vue'
    import { LoaderPinwheel, PackageSearch, CircleX } from 'lucide-vue-next'
    import CreateStory from './components/CreateStory.vue'
    import { Drawer, DrawerContent } from '../@/components/ui/drawer'
    import { nextTick, ref, watch } from 'vue'
    import { Input } from '../@/components/ui/input'
    import Toaster from '../@/components/ui/sonner/Sonner.vue'
    import { BookIndexResource, bookSearch, booksList, fetchBookById } from './api.ts'
    import { debounce, randomSearchTerm } from './utilities.ts'
    import { AspectRatio } from '../@/components/ui/aspect-ratio'
    import { Skeleton } from '../@/components/ui/skeleton'

    const activeBook = ref<{ visible: boolean, content: BookIndexResource | null }>({
        visible: false,
        content: null,
    })

    const loading = ref<number>()
    const searching = ref(false)
    const searchTerm = ref()
    const tokens: AbortController[] = []
    const books = ref<Array<BookIndexResource>>([])
    const queryString = new URLSearchParams(window.location.search)
    const randomSearchTermPlaceholder = ref<string>(randomSearchTerm())

    async function viewBook(bookId: number) {

        loading.value = bookId

        while (tokens.length) {
            tokens.pop()!.abort()
        }

        const preloadImage = (source: string): Promise<string> => new Promise((resolve, reject) => {

            const controller = new AbortController

            tokens.push(controller)

            return fetch(source, { signal: controller.signal, cache: 'force-cache' })
                .then(response => response.blob())
                .then(blob => URL.createObjectURL(blob))
                .then(resolve)
                .catch(reject)

        })

        const controller = new AbortController

        tokens.push(controller)

        const book = await fetchBookById(bookId, controller)

        /**
         * Preload all assets
         */
        await Promise.all([
            preloadImage(book.backdrop).then(blob => book.backdrop = blob),
            preloadImage(book.cover).then(blob => book.cover = blob),
            ...book.paragraphs.map(paragraph => preloadImage(paragraph.illustration).then(blob => paragraph.illustration = blob)),
        ])

        loading.value = undefined
        activeBook.value.content = book

        await nextTick(() => {
            activeBook.value.visible = true
        })

    }

    const onSearch = debounce(200, async function (event: KeyboardEvent | MouseEvent) {

        searchTerm.value = searchTerm.value || randomSearchTermPlaceholder.value.substring(
            0, randomSearchTermPlaceholder.value.length - 3,
        )

        books.value = []
        searching.value = true

        while (tokens.length) {
            tokens.pop()!.abort()
        }

        const controller = new AbortController()

        tokens.push(controller)

        books.value = await bookSearch(searchTerm.value, controller).catch(() => {
            return books.value
        })

        searching.value = false
        randomSearchTermPlaceholder.value = randomSearchTerm()

    })

    /**
     * If there is a ?book=xx on the url launch it straightaway
     */
    if (queryString.has('book')) {
        viewBook(+queryString.get('book')!)
    }

    watch(activeBook.value, function ({ visible, content }) {

        if (history.pushState) {

            const currentUrl = `${ window.location.protocol }//${ window.location.host }${ window.location.pathname }`

            if (visible) {

                const path = currentUrl + `?book=${ content.id }`

                window.history.pushState({ path }, '', path)

            } else {

                window.history.pushState({ path: currentUrl }, '', currentUrl)

            }

        }

    })

    /**
     * Load initial books list
     */
    booksList().then(response => {
        books.value = response
    })

</script>

<style>

    .cover-placeholder {
        background-image: url("./assets/cover-placeholder.jpg");
    }

    .drawer .swiper-pagination-horizontal {

        --swiper-pagination-bottom: 5px;

        @apply bg-white p-2 rounded-full;

    }

</style>
