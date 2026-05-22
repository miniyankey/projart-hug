<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    brandName: String,
    token: String,
});
</script>

<template>
    <Head title="Êtes-vous éligible ?" />
    <div>
        <h1>Êtes-vous éligible ?</h1>
    </div>
</template>
