<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import NewsletterForm from '@/components/forms/NewsletterForm.vue';
import PublicLayout from '@/layouts/PublicLayout.vue';

const { t } = useI18n();
</script>

<template>
    <PublicLayout>
        <Head :title="t('newsletter.unsubscribe_title')" />

        <section class="py-24">
            <div class="mx-auto max-w-xl px-6">
                <NewsletterForm mode="unsubscribe" />
            </div>
        </section>
    </PublicLayout>
</template>
