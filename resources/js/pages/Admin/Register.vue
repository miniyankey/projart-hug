<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { register } from '@/actions/App/Http/Controllers/AdminAuthController';
import FormField from '@/components/forms/FormField.vue';
import { Button } from '@/components/ui/button';
import AuthLayout from '@/layouts/AuthLayout.vue';

const { t } = useI18n();

const form = useForm({
    name: '',
    surname: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(register.url(), {
        onSuccess: () => form.reset('password', 'password_confirmation'),
    });
}
</script>

<template>
    <AuthLayout :title="t('auth.register.title')">
        <form class="flex flex-col gap-5" @submit.prevent="submit">
            <FormField
                id="name"
                v-model="form.name"
                type="text"
                :label="t('auth.register.name')"
                autocomplete="given-name"
                :error="form.errors.name"
            />

            <FormField
                id="surname"
                v-model="form.surname"
                type="text"
                :label="t('auth.register.surname')"
                autocomplete="family-name"
                :error="form.errors.surname"
            />

            <FormField
                id="email"
                v-model="form.email"
                type="email"
                :label="t('auth.register.email')"
                autocomplete="email"
                :error="form.errors.email"
            />

            <FormField
                id="password"
                v-model="form.password"
                type="password"
                :label="t('auth.register.password')"
                autocomplete="new-password"
                :error="form.errors.password"
            />

            <FormField
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                :label="t('auth.register.password_confirmation')"
                autocomplete="new-password"
            />

            <Button
                type="submit"
                variant="pixel_violet"
                class="mt-2 w-full"
                :disabled="form.processing"
            >
                {{ t('auth.register.submit') }}
            </Button>

            <Link
                href="/admin"
                class="text-center text-sm font-medium text-[var(--brand)] underline-offset-4 hover:underline"
            >
                {{ t('auth.register.back') }}
            </Link>
        </form>
    </AuthLayout>
</template>
