<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Clock } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import FormSection from '@/components/admin/FormSection.vue';
import { Button } from '@/components/ui/button';
import { useDateFormatter } from '@/composables/useDates';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { handled, index } from '@/routes/admin/formulaires';

const { t } = useI18n();
const { formatLongDate } = useDateFormatter();

const props = defineProps({
    submission: {
        type: Object,
        required: true,
    },
});

const isCollect = computed(() => props.submission.type === 'collect_request');
const isHandled = computed(() => props.submission.handled_at !== null);

const handledForm = useForm({});

function toggleHandled() {
    handledForm.patch(handled(props.submission.id).url, {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout :title="t('admin.formulaires.show_title')">
        <template #subtitle>
            {{
                isCollect
                    ? t('admin.formulaires.filter_collect')
                    : t('admin.formulaires.filter_contact')
            }}
        </template>

        <template #actions>
            <Button
                as-child
                class="border-2 border-gray-900 bg-white text-gray-900 hover:bg-gray-100"
            >
                <Link :href="index.url()">
                    <ArrowLeft class="size-4" />
                    {{ t('admin.formulaires.back') }}
                </Link>
            </Button>
        </template>

        <div class="mx-auto flex max-w-3xl flex-col gap-6">
            <!-- Bandeau de prise en charge -->
            <div
                class="flex flex-col gap-4 border-2 border-gray-900 p-5 sm:flex-row sm:items-center sm:justify-between"
                :class="isHandled ? 'bg-green-50' : 'bg-amber-50'"
            >
                <div class="flex items-center gap-3">
                    <CheckCircle2
                        v-if="isHandled"
                        class="size-6 shrink-0 text-green-700"
                    />
                    <Clock v-else class="size-6 shrink-0 text-amber-600" />
                    <div>
                        <p class="font-bold text-gray-900">
                            {{
                                isHandled
                                    ? t('admin.formulaires.handled_by', {
                                          name: submission.handler,
                                      })
                                    : t('admin.formulaires.to_handle')
                            }}
                        </p>
                        <p v-if="isHandled" class="text-sm text-gray-600">
                            {{ formatLongDate(submission.handled_at) }}
                        </p>
                    </div>
                </div>
                <Button
                    type="button"
                    :disabled="handledForm.processing"
                    class="border-2 border-gray-900"
                    :class="
                        isHandled
                            ? 'bg-white text-gray-900 hover:bg-gray-100'
                            : 'bg-[var(--brand)] text-white hover:bg-[var(--brand-hover)]'
                    "
                    @click="toggleHandled"
                >
                    {{
                        isHandled
                            ? t('admin.formulaires.unmark_handled')
                            : t('admin.formulaires.mark_handled')
                    }}
                </Button>
            </div>

            <!-- Coordonnées -->
            <FormSection :title="t('admin.formulaires.section_contact')">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <dt
                            class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.formulaires.field_company') }}
                        </dt>
                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ submission.company_name }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.formulaires.field_name') }}
                        </dt>
                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ submission.name }}
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.formulaires.field_email') }}
                        </dt>
                        <dd class="mt-1">
                            <a
                                :href="`mailto:${submission.contact_email}`"
                                class="font-semibold text-[var(--brand)] underline"
                            >
                                {{ submission.contact_email }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt
                            class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                        >
                            {{ t('admin.formulaires.field_received') }}
                        </dt>
                        <dd class="mt-1 font-semibold text-gray-900">
                            {{ formatLongDate(submission.created_at) }}
                        </dd>
                    </div>
                </div>
            </FormSection>

            <!-- Détail de la demande -->
            <FormSection :title="t('admin.formulaires.section_request')">
                <div v-if="isCollect">
                    <dt
                        class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.formulaires.field_dates') }}
                    </dt>
                    <dd class="mt-2 flex flex-wrap gap-2">
                        <span
                            v-for="date in submission.preferred_dates"
                            :key="date"
                            class="border-2 border-gray-900 bg-gray-50 px-2.5 py-1 text-sm font-semibold text-gray-900"
                        >
                            {{ formatLongDate(date) }}
                        </span>
                        <span
                            v-if="!submission.preferred_dates?.length"
                            class="text-sm text-gray-500"
                        >
                            {{ t('admin.formulaires.no_dates') }}
                        </span>
                    </dd>
                </div>

                <div v-if="isCollect">
                    <dt
                        class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.formulaires.field_trophy') }}
                    </dt>
                    <dd class="mt-1 font-semibold text-gray-900">
                        {{
                            submission.trophy_participation
                                ? t('admin.formulaires.yes')
                                : t('admin.formulaires.no')
                        }}
                    </dd>
                </div>

                <div>
                    <dt
                        class="text-xs font-bold tracking-wide text-gray-500 uppercase"
                    >
                        {{ t('admin.formulaires.field_message') }}
                    </dt>
                    <dd
                        class="mt-1 whitespace-pre-line text-gray-900"
                        :class="!submission.message && 'text-gray-500 italic'"
                    >
                        {{
                            submission.message ||
                            t('admin.formulaires.no_message')
                        }}
                    </dd>
                </div>
            </FormSection>
        </div>
    </AdminLayout>
</template>
