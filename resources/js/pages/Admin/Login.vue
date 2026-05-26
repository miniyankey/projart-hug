<script setup>
import { login } from '@/actions/App/Http/Controllers/AdminAuthController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

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
    <Head :title="t('auth.login.title')" />

    <div class="flex min-h-screen items-center justify-center bg-gray-50">
        <Card class="w-full max-w-sm">
            <CardHeader>
                <CardTitle class="text-xl">{{ t('auth.login.title') }}</CardTitle>
            </CardHeader>

            <CardContent>
                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <div class="flex flex-col gap-1.5">
                        <Label for="email">{{ t('auth.login.email') }}</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            :aria-invalid="!!form.errors.email"
                        />
                        <p v-if="form.errors.email" class="text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label for="password">{{ t('auth.login.password') }}</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            :aria-invalid="!!form.errors.password"
                        />
                        <p v-if="form.errors.password" class="text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <Button type="submit" class="w-full" :disabled="form.processing">
                        {{ t('auth.login.submit') }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
