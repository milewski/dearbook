<template>

    <AlertDialog :open="noticeState">

        <AlertDialogContent>

            <AlertDialogTitle>
                Welcome to our newly launched AI-Powered Children's Book Generator platform!
            </AlertDialogTitle>

            <AlertDialogHeader>

                <AlertDialogDescription>
                    <div class="prose lg:prose-md font-sans">
                        <p>We are currently revamping our infrastructure to accommodate the growing traffic and traction
                            we’re receiving. Our team is working around the clock to improve the experience!</p>
                        <p>We have many exciting features and ideas in the works, including a future transition to a
                            paid <b>$DEARBOOK</b> token-based system that will power our storytelling experience and
                            provide even more utility for the token. But for now, dive in and enjoy limitless story
                            creation—on us!</p>
                    </div>
                </AlertDialogDescription>

            </AlertDialogHeader>

            <AlertDialogFooter>
                <AlertDialogAction @click="noticeState = false">OK!</AlertDialogAction>
            </AlertDialogFooter>

        </AlertDialogContent>

    </AlertDialog>

</template>

<script setup lang="ts">

    import { AlertDialog, AlertDialogAction, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from '../../@/components/ui/alert-dialog'
    import { useLocalStorage } from '@vueuse/core'
    import { ref, watch } from 'vue'

    const storage = useLocalStorage<boolean>('notice', {})
    const noticeState = ref(storage.value)

    watch(noticeState, function (value) {
        storage.value = value
    })

</script>