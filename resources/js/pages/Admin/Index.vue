<script setup>
import {
    logout,
    showRegister,
} from '@/actions/App/Http/Controllers/AdminAuthController';
import { Button } from '@/components/ui/button';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const flash = computed(() => page.props.flash);

const logoutForm = useForm({});

function submitLogout() {
    logoutForm.post(logout.url());
}
</script>

<template>
    <Head title="Administration" />

    <div class="min-h-screen bg-white">
        <header class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h1 class="text-lg font-semibold">Tableau de bord</h1>

            <div class="flex items-center gap-3">
                <Link
                    :href="showRegister.url()"
                    class="text-sm text-gray-600 hover:text-gray-900"
                >
                    Créer un compte admin
                </Link>

                <form @submit.prevent="submitLogout">
                    <Button type="submit" variant="outline" size="sm">
                        Se déconnecter
                    </Button>
                </form>
            </div>
        </header>

        <main class="px-6 py-8">
            <div
                v-if="flash.success"
                class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            >
                {{ flash.success }}
            </div>

            <p class="text-gray-500">Contenu du tableau de bord à venir.</p>
        </main>
    </div>
</template>
