<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
import { login } from '@/actions/App/Http/Controllers/AdminAuthController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

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
                    {{ t('auth.login.title') }}
                </CardTitle>
            </CardHeader>

            <CardContent>
                <form class="flex flex-col gap-5" @submit.prevent="submit">
                    <div class="flex flex-col gap-1.5">
                        <Label
                            for="email"
                            class="font-semibold text-gray-900"
                            >{{ t('auth.login.email') }}</Label
                        >
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            variant="pixel"
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
                        <Label
                            for="password"
                            class="font-semibold text-gray-900"
                            >{{ t('auth.login.password') }}</Label
                        >
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            variant="pixel"
                            autocomplete="current-password"
                            :aria-invalid="!!form.errors.password"
                        />
                        <p
                            v-if="form.errors.password"
                            class="text-sm text-red-600"
                        >
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <Button
                        type="submit"
                        variant="pixel_violet"
                        class="mt-2 w-full"
                        :disabled="form.processing"
                    >
                        {{ t('auth.login.submit') }}
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
