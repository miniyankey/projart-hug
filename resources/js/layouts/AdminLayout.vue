<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    Building2,
    LayoutDashboard,
    LineChart,
    LogOut,
    Syringe,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
import { logout } from '@/actions/App/Http/Controllers/AdminAuthController';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { home } from '@/routes';
import { index as adminIndex } from '@/routes/admin';
import { index as collectesIndex } from '@/routes/admin/collectes';
import { index as entreprisesIndex } from '@/routes/admin/entreprises';
import { index as kpiIndex } from '@/routes/admin/kpi';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
});

const { t } = useI18n();
const page = usePage();
const flash = computed(() => page.props.flash ?? {});

// Le composant Inertia courant (ex: "Admin/Entreprises/Index") sert à
// déterminer l'onglet actif de la barre latérale.
const currentComponent = computed(() => page.component);

const navItems = computed(() => [
    {
        key: 'overview',
        label: t('admin.nav.overview'),
        icon: LayoutDashboard,
        href: adminIndex.url(),
        match: 'Admin/Index',
    },
    {
        key: 'entreprises',
        label: t('admin.nav.entreprises'),
        icon: Building2,
        href: entreprisesIndex.url(),
        match: 'Admin/Entreprises',
    },
    {
        key: 'collectes',
        label: t('admin.nav.collectes'),
        icon: Syringe,
        href: collectesIndex.url(),
        match: 'Admin/Collectes',
    },
    {
        key: 'kpi',
        label: t('admin.nav.kpi'),
        icon: LineChart,
        href: kpiIndex.url(),
        match: 'Admin/Kpi',
    },
]);

function isActive(match) {
    return currentComponent.value.startsWith(match);
}

const logoutForm = useForm({});

function submitLogout() {
    logoutForm.post(logout.url());
}
</script>

<template>
    <Head :title="props.title" />

    <div class="flex min-h-screen bg-gray-50 text-gray-900">
        <!-- Barre latérale -->
        <aside
            class="fixed inset-y-0 left-0 flex w-64 flex-col border-r-2 border-gray-900 bg-white"
        >
            <div class="flex items-center border-b-2 border-gray-900 px-6 py-5">
                <img :src="hugLogo" alt="HUG" class="h-9 w-auto" />
            </div>

            <nav class="flex flex-1 flex-col gap-1 p-3">
                <Link
                    v-for="item in navItems"
                    :key="item.key"
                    :href="item.href"
                    class="flex items-center gap-3 border-2 px-3 py-2.5 text-sm font-semibold transition-colors"
                    :class="
                        isActive(item.match)
                            ? 'border-gray-900 bg-[var(--brand)] text-white'
                            : 'border-transparent text-gray-600 hover:border-gray-900 hover:bg-gray-100 hover:text-gray-900'
                    "
                >
                    <component :is="item.icon" class="size-5 shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>

            <div class="flex flex-col gap-2 border-t-2 border-gray-900 p-3">
                <Link
                    :href="home.url()"
                    class="flex items-center justify-center gap-2 border-2 border-gray-900 bg-white px-3 py-2.5 text-sm font-semibold text-gray-900 transition-colors hover:bg-gray-100"
                >
                    {{ t('admin.nav.back_to_site') }}
                </Link>
                <form @submit.prevent="submitLogout">
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-2 px-3 py-2 text-sm font-medium text-gray-500 transition-colors hover:text-gray-900"
                    >
                        <LogOut class="size-4" />
                        {{ t('admin.nav.logout') }}
                    </button>
                </form>
            </div>
        </aside>

        <!-- Contenu -->
        <div class="ml-64 flex min-h-screen flex-1 flex-col">
            <header
                class="flex items-center justify-between gap-4 border-b-2 border-gray-900 bg-white px-8 py-6"
            >
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">
                        <slot name="title">{{ props.title }}</slot>
                    </h1>
                    <p
                        v-if="$slots.subtitle"
                        class="mt-1 text-sm text-gray-500"
                    >
                        <slot name="subtitle" />
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <slot name="actions" />
                    <LanguageSwitcher />
                </div>
            </header>

            <main class="flex-1 px-8 py-8">
                <div
                    v-if="flash.success"
                    class="mb-6 border-2 border-gray-900 bg-green-50 px-4 py-3 text-sm font-medium text-green-900"
                >
                    {{ t(flash.success) }}
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>
