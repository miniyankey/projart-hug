<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
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
                    {{ t('auth.register.title') }}
                </CardTitle>
            </CardHeader>

            <CardContent>
                <form class="flex flex-col gap-5" @submit.prevent="submit">
                    <div class="flex flex-col gap-1.5">
                        <Label for="name" class="font-semibold text-gray-900">{{
                            t('auth.register.name')
                        }}</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            variant="pixel"
                            class="bg-white dark:bg-white dark:text-gray-900"
                            autocomplete="given-name"
                            :aria-invalid="!!form.errors.name"
                        />
                        <p v-if="form.errors.name" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <Label
                            for="surname"
                            class="font-semibold text-gray-900"
                            >{{ t('auth.register.surname') }}</Label
                        >
                        <Input
                            id="surname"
                            v-model="form.surname"
                            type="text"
                            variant="pixel"
                            class="bg-white dark:bg-white dark:text-gray-900"
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
                        <Label
                            for="email"
                            class="font-semibold text-gray-900"
                            >{{ t('auth.register.email') }}</Label
                        >
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            variant="pixel"
                            class="bg-white dark:bg-white dark:text-gray-900"
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
                            >{{ t('auth.register.password') }}</Label
                        >
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            variant="pixel"
                            class="bg-white dark:bg-white dark:text-gray-900"
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
                        <Label
                            for="password_confirmation"
                            class="font-semibold text-gray-900"
                        >
                            {{ t('auth.register.password_confirmation') }}
                        </Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            variant="pixel"
                            class="bg-white dark:bg-white dark:text-gray-900"
                            autocomplete="new-password"
                        />
                    </div>

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
                        class="text-center text-sm font-medium text-violet-700 underline-offset-4 hover:underline"
                    >
                        {{ t('auth.register.back') }}
                    </Link>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
