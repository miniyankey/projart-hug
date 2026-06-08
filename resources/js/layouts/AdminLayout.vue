<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import {
    Building2,
    Inbox,
    LayoutDashboard,
    LineChart,
    LogOut,
    Menu,
    Syringe,
    Trophy,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import hugLogo from '@/../images/logos/hug.png';
import { logout } from '@/actions/App/Http/Controllers/AdminAuthController';
import LanguageSwitcher from '@/components/layout/LanguageSwitcher.vue';
import { home } from '@/routes';
import { index as adminIndex } from '@/routes/admin';
import { index as collectesIndex } from '@/routes/admin/collectes';
import { index as entreprisesIndex } from '@/routes/admin/entreprises';
import { index as formulairesIndex } from '@/routes/admin/formulaires';
import { index as kpiIndex } from '@/routes/admin/kpi';
import { index as vainqueursIndex } from '@/routes/admin/vainqueurs';

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

// Drawer de navigation mobile : ouvert/fermé, refermé après chaque navigation.
const mobileMenuOpen = ref(false);

watch(currentComponent, () => {
    mobileMenuOpen.value = false;
});

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
        key: 'formulaires',
        label: t('admin.nav.formulaires'),
        icon: Inbox,
        href: formulairesIndex.url(),
        match: 'Admin/Formulaires',
    },
    {
        key: 'vainqueurs',
        label: t('admin.nav.vainqueurs'),
        icon: Trophy,
        href: vainqueursIndex.url(),
        match: 'Admin/Vainqueurs',
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
        <!-- Topbar mobile -->
        <header
            class="fixed inset-x-0 top-0 z-30 flex h-16 items-center justify-between border-b-2 border-gray-900 bg-white px-4 lg:hidden"
        >
            <img :src="hugLogo" alt="HUG" class="h-8 w-auto" />
            <button
                type="button"
                class="flex size-10 items-center justify-center border-2 border-gray-900 bg-white text-gray-900 transition-colors hover:bg-gray-100"
                :aria-label="t('admin.nav.open_menu')"
                aria-haspopup="true"
                :aria-expanded="mobileMenuOpen"
                @click="mobileMenuOpen = true"
            >
                <Menu class="size-5" />
            </button>
        </header>

        <!-- Overlay mobile -->
        <div
            v-if="mobileMenuOpen"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            @click="mobileMenuOpen = false"
        />

        <!-- Barre latérale -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex w-64 max-w-[85vw] flex-col border-r-2 border-gray-900 bg-white transition-transform duration-200 ease-out lg:translate-x-0"
            :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div
                class="flex items-center justify-between border-b-2 border-gray-900 px-6 py-5"
            >
                <img :src="hugLogo" alt="HUG" class="h-9 w-auto" />
                <button
                    type="button"
                    class="flex size-9 items-center justify-center text-gray-500 transition-colors hover:text-gray-900 lg:hidden"
                    :aria-label="t('admin.nav.close_menu')"
                    @click="mobileMenuOpen = false"
                >
                    <X class="size-5" />
                </button>
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
        <div
            class="flex min-h-screen min-w-0 flex-1 flex-col pt-16 lg:ml-64 lg:pt-0"
        >
            <header
                class="flex flex-col gap-4 border-b-2 border-gray-900 bg-white px-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 sm:py-6 lg:px-8"
            >
                <div class="min-w-0">
                    <h1 class="text-xl font-bold tracking-tight sm:text-2xl">
                        <slot name="title">{{ props.title }}</slot>
                    </h1>
                    <p
                        v-if="$slots.subtitle"
                        class="mt-1 text-sm text-gray-500"
                    >
                        <slot name="subtitle" />
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <slot name="actions" />
                    <LanguageSwitcher />
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
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
