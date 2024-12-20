<template>

    <form @submit="onSubmitSimple">

        <Card class="rounded-2xl">

            <CardHeader>

                <CardTitle>Bring Your Story to Life</CardTitle>

                <CardDescription>
                    Describe your story with as much detail as you like, in any language!
                </CardDescription>

            </CardHeader>

            <CardContent class="space-y-2">

                <div class="grid w-full gap-2">

                    <FormField v-slot="{ componentField }" name="prompt">

                        <FormItem>

                            <FormControl>

                                <Textarea
                                    class="auto-expand"
                                    :placeholder="randomBookTitle"
                                    v-bind="componentField"
                                    rows="5"/>

                            </FormControl>

                            <FormMessage/>

                        </FormItem>


                    </FormField>

                </div>

            </CardContent>

            <CardFooter>

                <Button type="submit"
                        :disabled="formLoading || !formSimple.isFieldValid('prompt')"
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
    import { createBook } from '../../api.ts'
    import { randomSearchTerm } from '../../utilities.ts'
    import { toTypedSchema } from '@vee-validate/zod'
    import * as z from 'zod'
    import { useForm } from 'vee-validate'
    import { ref } from 'vue'

    const { fakeWallet } = defineProps<{ fakeWallet: string }>()
    const emit = defineEmits([ 'onResponse' ])

    const formSchemaSimple = toTypedSchema(z.object({ prompt: z.string().max(500).min(10) }))
    const formSimple = useForm({ validationSchema: formSchemaSimple })
    const randomBookTitle = ref(randomSearchTerm())
    const formLoading = ref(false)

    const onSubmitSimple = formSimple.handleSubmit(async values => {

        formLoading.value = true

        await createBook(values.prompt, fakeWallet)
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
                formSimple.resetForm()
            })

    })

</script>

<style>

    .auto-expand {
        field-sizing: content
    }

</style>