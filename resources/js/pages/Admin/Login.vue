<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { login } from '@/actions/App/Http/Controllers/AdminAuthController';
import FormField from '@/components/forms/FormField.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';

const { t } = useI18n();

const form = useForm({
    email: '',
    password: '',
});

function submit() {
    form.post(login.url());
}
</script>

<template>
    <AuthLayout :title="t('auth.login.title')">
        <form class="flex flex-col gap-5" @submit.prevent="submit">
            <FormField
                id="email"
                v-model="form.email"
                type="email"
                :label="t('auth.login.email')"
                autocomplete="email"
                :error="form.errors.email"
            />

            <FormField
                id="password"
                v-model="form.password"
                type="password"
                :label="t('auth.login.password')"
                autocomplete="current-password"
                :error="form.errors.password"
            />

            <Button
                type="submit"
                variant="pixel_violet"
                class="mt-2 w-full"
                :disabled="form.processing"
            >
                {{ t('auth.login.submit') }}
            </Button>
        </form>
    </AuthLayout>
</template>
