<script setup>
import { Head } from '@inertiajs/vue3'

defineProps({
    brandName: String,
    token: String,
})
</script>

<template>
    <Head title="Informations collecte" />
    <div>
        <h1>Informations collecte</h1>
    </div>
</template>
