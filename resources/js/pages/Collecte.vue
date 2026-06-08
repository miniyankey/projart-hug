<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import CollectForm from '@/components/forms/CollectForm.vue';
import MascottePopup from '@/components/MascottePopup.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { eligibilite } from '@/routes';

const { t } = useI18n();
</script>

<template>
    <PublicLayout>
        <Head :title="t('collecte.title')" />
        <div class="mx-auto mt-10 max-w-3xl px-4 py-10 sm:px-6 sm:py-16">
            <h1 class="font-pixel text-[1.2rem] leading-loose text-gray-900">
                {{ t('collecte.title') }}
            </h1>
            <p class="mt-4 text-base leading-relaxed text-gray-600">
                {{ t('collecte.intro') }}
            </p>

            <div class="mt-10">
                <CollectForm />
            </div>
        </div>
        <MascottePopup :href="eligibilite.url()" />
    </PublicLayout>
</template>
