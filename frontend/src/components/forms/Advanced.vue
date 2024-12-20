<template>

    <form @submit="onSubmitAdvanced">

        <Card class="rounded-2xl">

            <CardHeader>

                <CardTitle>Bring Your Story to Life</CardTitle>

                <CardDescription>
                    Take full control over your story’s title and description with more
                    detailed customization options.
                </CardDescription>

            </CardHeader>

            <CardContent class="space-y-2">

                <div class="grid w-full gap-2">

                    <FormField v-slot="{ componentField }" name="title">

                        <FormItem>

                            <FormControl>

                                <div class="grid w-full  items-center gap-1.5">

                                    <Label for="title" class="sm:hidden">Title</Label>
                                    <Input v-bind="componentField" type="text"
                                           class="w-full"
                                           :placeholder="`Title, e.g. '${ randomBookTitle }'`"/>

                                </div>

                            </FormControl>

                            <FormMessage/>

                        </FormItem>

                    </FormField>

                    <div class="sm:flex space-y-2 sm:space-y-0 sm:space-x-4">

                        <FormField as="div" class="flex-1" v-slot="{ componentField }" name="prompt">

                            <FormItem>

                                <FormControl>

                                    <Label for="title" class="sm:hidden">Story</Label>

                                    <Textarea
                                        class="auto-expand"
                                        placeholder="Main storyline: What exciting adventure will your story unfold?"
                                        v-bind="componentField"
                                        rows="5"/>

                                </FormControl>

                                <FormMessage/>

                            </FormItem>

                        </FormField>

                        <FormField as="div" class="flex-1" v-slot="{ componentField }" name="negative">

                            <FormItem>

                                <FormControl>

                                    <Label for="negative"
                                           class="sm:hidden">Exclusion</Label>

                                    <Textarea
                                        class="auto-expand"
                                        placeholder="Describe elements you don't want in the illustrations (e.g., humans, forests, trees)"
                                        v-bind="componentField"
                                        rows="5"/>

                                </FormControl>

                                <FormMessage/>

                            </FormItem>

                        </FormField>

                    </div>

                </div>

            </CardContent>

            <CardFooter>

                <Button
                    type="submit"
                    :disabled="formLoading || !(formAdvanced.isFieldValid('prompt') && formAdvanced.isFieldValid('title'))"
                    class="bg-[#F18533] hover:bg-[#F18533] w-full rounded-full">

                    <div>Create!</div>

                    <LoaderPinwheel v-if="formLoading" class="animate-spin"/>

                </Button>

            </CardFooter>

        </Card>

    </form>

</template>

<script setup lang="ts">

    import { Textarea } from '../../../@/components/ui/textarea'
    import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '../../../@/components/ui/card'
    import { FormControl, FormField, FormItem, FormMessage } from '../../../@/components/ui/form'
    import { Button } from '../../../@/components/ui/button'
    import { LoaderPinwheel } from 'lucide-vue-next'
    import { Label } from '../../../@/components/ui/label'
    import { Input } from '../../../@/components/ui/input'
    import { createBookAdvanced } from '../../api.ts'
    import { randomSearchTerm } from '../../utilities.ts'
    import { toTypedSchema } from '@vee-validate/zod'
    import * as z from 'zod'
    import { useForm } from 'vee-validate'
    import { ref } from 'vue'

    const { fakeWallet } = defineProps<{ fakeWallet: string }>()
    const emit = defineEmits([ 'onResponse' ])

    const formSchemaAdvanced = toTypedSchema(z.object({
        title: z.string().max(255).min(10),
        prompt: z.string().max(500).min(10),
        negative: z.string().max(500).optional(),
    }))

    const formAdvanced = useForm({ validationSchema: formSchemaAdvanced })
    const randomBookTitle = ref(randomSearchTerm())
    const formLoading = ref(false)

    const onSubmitAdvanced = formAdvanced.handleSubmit(async values => {

        formLoading.value = true

        await createBookAdvanced(values.title, values.prompt, values.negative, fakeWallet.value!)
            .then(response => {

                if (response.id) {

                    emit('onResponse', {
                        id: response.id,
                        type: 'placeholder',
                    })

                }

            })
            .finally(() => {
                formLoading.value = false
                randomBookTitle.value = randomSearchTerm()
                formAdvanced.resetForm()
            })

    })

</script>

<style>

    .auto-expand {
        field-sizing: content
    }

</style>