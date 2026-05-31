<script setup>
import { Building2, Mail, MapPin, Phone, User } from 'lucide-vue-next';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

// Aperçu live du co-branding d'une entreprise pendant la saisie du
// formulaire : reproduit l'en-tête teinté + logo de la page co-brandée.
const props = defineProps({
    name: { type: String, default: '' },
    color: { type: String, default: '#8b2cf1' },
    colorSecondary: { type: String, default: '' },
    logoUrl: { type: String, default: null },
    contactName: { type: String, default: '' },
    contactPhone: { type: String, default: '' },
    email: { type: String, default: '' },
    city: { type: String, default: '' },
});

const { t } = useI18n();

const brandColor = computed(() => props.color || '#8b2cf1');
const displayName = computed(
    () => props.name || t('admin.entreprises.create.preview_name'),
);
</script>

<template>
    <div>
        <p class="mb-3 text-xs font-bold uppercase tracking-wide text-gray-500">
            {{ t('admin.entreprises.create.preview_title') }}
        </p>

        <div class="overflow-hidden border-2 border-gray-900 bg-white">
            <!-- En-tête teinté façon page co-brandée -->
            <div
                class="flex items-center gap-3 p-4 text-white"
                :style="{ backgroundColor: brandColor }"
            >
                <span
                    class="flex size-12 shrink-0 items-center justify-center overflow-hidden border-2 border-white/40 bg-white"
                >
                    <img
                        v-if="logoUrl"
                        :src="logoUrl"
                        alt=""
                        class="size-full object-contain p-0.5"
                    />
                    <Building2 v-else class="size-6 text-gray-400" />
                </span>
                <div class="min-w-0">
                    <p class="truncate text-base font-bold leading-tight">
                        {{ displayName }}
                    </p>
                    <p v-if="city" class="truncate text-xs text-white/80">
                        {{ city }}
                    </p>
                </div>
            </div>

            <!-- Détails -->
            <div class="space-y-2 p-4 text-sm text-gray-700">
                <p v-if="contactName" class="flex items-center gap-2">
                    <User class="size-4 shrink-0 text-gray-400" />
                    <span class="truncate">{{ contactName }}</span>
                </p>
                <p v-if="email" class="flex items-center gap-2">
                    <Mail class="size-4 shrink-0 text-gray-400" />
                    <span class="truncate">{{ email }}</span>
                </p>
                <p v-if="contactPhone" class="flex items-center gap-2">
                    <Phone class="size-4 shrink-0 text-gray-400" />
                    <span class="truncate">{{ contactPhone }}</span>
                </p>
                <p v-if="city" class="flex items-center gap-2">
                    <MapPin class="size-4 shrink-0 text-gray-400" />
                    <span class="truncate">{{ city }}</span>
                </p>

                <!-- Pastilles de couleurs -->
                <div class="flex items-center gap-2 pt-2">
                    <span
                        class="size-6 border-2 border-gray-900"
                        :style="{ backgroundColor: brandColor }"
                        :title="color"
                    />
                    <span
                        v-if="colorSecondary"
                        class="size-6 border-2 border-gray-900"
                        :style="{ backgroundColor: colorSecondary }"
                        :title="colorSecondary"
                    />
                </div>
            </div>
        </div>

        <p class="mt-3 text-xs leading-relaxed text-gray-500">
            {{ t('admin.entreprises.create.preview_hint') }}
        </p>
    </div>
</template>
