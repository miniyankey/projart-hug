<script setup>
import { Head } from '@inertiajs/vue3';
import hugLogo from '@/../images/logos/hug.png';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

defineProps({
    // Titre affiché dans la Card et l'onglet du navigateur.
    title: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head :title="title" />

    <div
        class="flex min-h-screen items-center justify-center px-4 py-12"
        style="
            background-color: #6b21a8;
            background-image: radial-gradient(
                circle,
                rgba(255, 255, 255, 0.07) 1px,
                transparent 1px
            );
            background-size: 28px 28px;
        "
    >
        <Card variant="pixel" class="w-full max-w-sm">
            <CardHeader class="justify-items-center text-center">
                <img :src="hugLogo" alt="HUG" class="mb-4 h-12 w-auto" />
                <CardTitle
                    class="font-pixel text-[1rem] leading-loose text-gray-900"
                >
                    {{ title }}
                </CardTitle>
            </CardHeader>

            <CardContent>
                <slot />
            </CardContent>
        </Card>
    </div>
</template>
