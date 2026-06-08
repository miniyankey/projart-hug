<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Clock, MapPin } from 'lucide-vue-next';
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import MascottePopup from '@/components/MascottePopup.vue';
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from '@/components/ui/accordion';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useDateFormatter } from '@/composables/useDates';
import { useTracking } from '@/composables/useTracking';
import PublicLayout from '@/layouts/PublicLayout.vue';
import { jeu as cobrandJeu } from '@/routes/cobrand';

const { t } = useI18n();
const { formatLongDate } = useDateFormatter();
const { trackAppointmentClick, trackCollecteView } = useTracking();

const props = defineProps({
    company: Object,
    collectSlug: String,
    collect: Object,
});

// Toute arrivée sur la page collecte alimente le haut du funnel RDV
// (visiteur sans inscription = ligne avec appointment_click à false)
onMounted(() => {
    if (props.collect?.id) {
        trackCollecteView(props.collect.id);
    }
});

const routeParams = computed(() => ({
    brandName: props.company?.slug,
    collect: props.collectSlug,
}));

const horaires = computed(() => {
    const { start_time: start, end_time: end } = props.collect ?? {};

    if (start && end) {
        return `${start} - ${end}`;
    }

    return start || '-';
});

// Le jour J est passé (mais dans la semaine de grâce où le lien reste ouvert) :
// la page affiche « collecte terminée » et masque l'inscription.
const isPast = computed(() => Boolean(props.collect?.is_past));

const place = computed(() => props.collect?.place ?? null);

// Carte Google Maps géocodée sur l'adresse de la collecte
const mapUrl = computed(() => {
    const p = place.value;

    if (!p) {
        return null;
    }

    const query = [p.address, `${p.locality} ${p.city}`]
        .filter(Boolean)
        .join(', ');

    return `https://maps.google.com/maps?q=${encodeURIComponent(query)}&z=15&output=embed`;
});
</script>

<template>
    <PublicLayout
        :company="company"
        :collect-slug="collectSlug"
        cta-scroll-target="#rdv"
    >
        <Head :title="t('nav.collecte_info')" />

        <!-- Hero co-brandé -->
        <section class="bg-[var(--brand-tint)] py-16 lg:py-24">
            <div class="mx-auto max-w-7xl px-6">
                <div class="grid items-center gap-10 lg:grid-cols-2">
                    <!-- Colonne texte -->
                    <div>
                        <Badge v-if="!isPast" variant="pixel">
                            {{ t('cobrand.collecte.eyebrow') }}
                        </Badge>
                        <h1
                            class="mt-6 font-pixel text-[1.35rem] leading-loose text-gray-900"
                        >
                            {{ t('cobrand.collecte.title') }}
                        </h1>
                        <p class="mt-4 max-w-lg leading-relaxed text-gray-700">
                            {{ t('cobrand.intro', { company: company.name }) }}
                        </p>

                        <!-- Bandeau « collecte terminée » (semaine de grâce après le jour J) -->
                        <div
                            v-if="isPast"
                            class="mt-8 border-[3px] border-black bg-[var(--brand)] p-6 text-white shadow-[8px_8px_0_0_var(--brand-shadow)]"
                        >
                            <p
                                class="font-pixel text-base leading-snug sm:text-lg"
                            >
                                {{ t('cobrand.collecte.ended_title') }}
                            </p>
                            <p
                                class="mt-4 text-sm leading-relaxed text-white/80"
                            >
                                {{
                                    t('cobrand.collecte.ended_text', {
                                        date: formatLongDate(collect?.day),
                                    })
                                }}
                            </p>
                        </div>

                        <!-- Encart date / horaires / lieu -->
                        <dl
                            class="mt-8 flex flex-col divide-y divide-gray-200 border-[3px] border-black bg-white p-6 shadow-[8px_8px_0_0_rgba(0,0,0,0.45)]"
                        >
                            <div class="flex gap-3 py-4 first:pt-0 last:pb-0">
                                <Calendar
                                    class="mt-0.5 size-5 shrink-0 text-[var(--brand)]"
                                />
                                <div>
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                                    >
                                        {{ t('cobrand.collecte.info.date') }}
                                    </dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        {{ formatLongDate(collect?.day) }}
                                    </dd>
                                </div>
                            </div>
                            <div class="flex gap-3 py-4 first:pt-0 last:pb-0">
                                <Clock
                                    class="mt-0.5 size-5 shrink-0 text-[var(--brand)]"
                                />
                                <div>
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                                    >
                                        {{
                                            t('cobrand.collecte.info.horaires')
                                        }}
                                    </dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        {{ horaires }}
                                    </dd>
                                </div>
                            </div>
                            <div class="flex gap-3 py-4 first:pt-0 last:pb-0">
                                <MapPin
                                    class="mt-0.5 size-5 shrink-0 text-[var(--brand)]"
                                />
                                <div>
                                    <dt
                                        class="text-xs font-semibold tracking-wide text-gray-500 uppercase"
                                    >
                                        {{ t('cobrand.collecte.info.lieu') }}
                                    </dt>
                                    <dd class="mt-1 font-medium text-gray-900">
                                        <template v-if="place">
                                            {{ place.name }}
                                            <span
                                                class="mt-0.5 block text-sm leading-snug font-normal text-gray-600"
                                            >
                                                {{ place.address }},
                                                {{ place.locality }}
                                                {{ place.city }}
                                                <template v-if="place.room">
                                                    · {{ place.room }}
                                                </template>
                                            </span>
                                        </template>
                                        <template v-else>-</template>
                                    </dd>
                                </div>
                            </div>
                        </dl>

                        <!-- CTA -->
                        <div
                            id="rdv"
                            class="mt-8 flex scroll-mt-24 flex-wrap gap-4"
                        >
                            <Button as-child variant="pixel_violet" size="cta">
                                <Link :href="cobrandJeu.url(routeParams)">
                                    {{ t('cobrand.collecte.cta_eligible') }}
                                </Link>
                            </Button>
                            <Button
                                v-if="collect?.link_appointment && !isPast"
                                as-child
                                variant="pixel_white"
                                size="cta"
                            >
                                <a
                                    :href="collect.link_appointment"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    @click="
                                        trackAppointmentClick(
                                            collect.id,
                                            'collecte',
                                        )
                                    "
                                >
                                    {{ t('cobrand.collecte.cta_rdv') }}
                                </a>
                            </Button>
                        </div>
                    </div>

                    <!-- Colonne carte du lieu -->
                    <div
                        class="aspect-square overflow-hidden border-[3px] border-black bg-white shadow-[8px_8px_0_0_rgba(0,0,0,0.45)]"
                    >
                        <iframe
                            v-if="mapUrl"
                            :src="mapUrl"
                            :title="t('cobrand.collecte.map_alt')"
                            class="h-full w-full"
                            style="border: 0"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Suis-je éligible ? + Informations utiles -->
        <section class="py-20">
            <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-2">
                <!-- Carte feature ludique -->
                <div>
                    <h2
                        class="mb-6 font-pixel text-[1.1rem] leading-loose text-gray-900"
                    >
                        {{ t('cobrand.collecte.eligible_section_title') }}
                    </h2>
                    <div
                        class="border-[3px] border-black bg-[var(--brand-tint)] p-8 shadow-[8px_8px_0_0_rgba(0,0,0,0.45)]"
                    >
                        <span
                            class="font-pixel text-[0.7rem] leading-loose text-[var(--brand)]"
                        >
                            &gt;
                            {{ t('cobrand.collecte.eligible_card_eyebrow') }}
                        </span>
                        <h3 class="mt-5 text-xl font-semibold text-gray-900">
                            {{ t('cobrand.collecte.eligible_card_title') }}
                        </h3>
                        <p
                            class="mt-3 max-w-md text-sm leading-relaxed text-gray-600"
                        >
                            {{ t('cobrand.collecte.eligible_card_text') }}
                        </p>
                        <Button
                            as-child
                            variant="pixel_violet"
                            size="cta"
                            class="mt-6"
                        >
                            <Link :href="cobrandJeu.url(routeParams)">
                                &gt;
                                {{ t('cobrand.collecte.eligible_card_cta') }}
                            </Link>
                        </Button>
                    </div>
                </div>

                <!-- Accordéon infos utiles -->
                <div>
                    <h2
                        class="mb-6 font-pixel text-[1.1rem] leading-loose text-gray-900"
                    >
                        {{ t('cobrand.collecte.infos_title') }}
                    </h2>
                    <Accordion type="single" collapsible>
                        <AccordionItem
                            v-for="i in 8"
                            :key="i"
                            :value="`info-${i}`"
                        >
                            <AccordionTrigger
                                class="text-left font-medium text-gray-900"
                            >
                                {{ t(`cobrand.collecte.faq.q${i}`) }}
                            </AccordionTrigger>
                            <AccordionContent
                                class="leading-relaxed text-gray-600"
                            >
                                {{ t(`cobrand.collecte.faq.a${i}`) }}
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </div>
            </div>
        </section>
        <MascottePopup :href="cobrandJeu.url(routeParams)" />
    </PublicLayout>
</template>
