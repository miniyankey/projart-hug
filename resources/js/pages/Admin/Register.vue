<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { register } from '@/actions/App/Http/Controllers/AdminAuthController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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
    <Head :title="t('auth.register.title')" />

    <div class="flex min-h-screen items-center justify-center bg-gray-50">
        <Card class="w-full max-w-sm">
            <CardHeader>
                <CardTitle class="text-xl">{{
                    t('auth.register.title')
                }}</CardTitle>
            </CardHeader>

            <CardContent>
                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="flex flex-col gap-1.5">
                        <Label for="name">{{ t('auth.register.name') }}</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            autocomplete="given-name"
                            :aria-invalid="!!form.errors.name"
                        />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="surname">{{
                            t('auth.register.surname')
                        }}</Label>
                        <Input
                            id="surname"
                            v-model="form.surname"
                            type="text"
                            autocomplete="family-name"
                            :aria-invalid="!!form.errors.surname"
                        />
                        <p
                            v-if="form.errors.surname"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.surname }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="email">{{
                            t('auth.register.email')
                        }}</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            :aria-invalid="!!form.errors.email"
                        />
                        <p
                            v-if="form.errors.email"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="password">{{
                            t('auth.register.password')
                        }}</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="new-password"
                            :aria-invalid="!!form.errors.password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="password_confirmation">
                            {{ t('auth.register.password_confirmation') }}
                        </Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                        />
                    </div>

                    <Button
                        type="submit"
                        class="w-full"
                        :disabled="form.processing"
                    >
                        {{ t('auth.register.submit') }}
                    </Button>

                    <Link
                        href="/admin"
                        class="text-center text-sm text-gray-500 hover:text-gray-700"
                    >
                        {{ t('auth.register.back') }}
                    </Link>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
