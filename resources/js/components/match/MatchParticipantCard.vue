<script setup lang="ts">
import TraitBadge from '@/components/traits/TraitBadge.vue';
import { useTftMatchPresentation } from '@/composables/useTftMatchPresentation';

interface MatchParticipantCardItem {
    summonerName: string;
    placement: number;
    level: number | null;
    goldLeft: number | null;
    lastRound: number | null;
    traits: Array<{
        icon?: string | null;
        name?: string | null;
        style?: number;
        num_units?: number;
    }>;
    units: Array<{
        character_id?: string;
        name?: string | null;
        icon?: string | null;
        rarity: number;
        tier: number;
        items: Array<{
            icon?: string | null;
        }>;
    }>;
}

const props = defineProps<{
    participant: MatchParticipantCardItem;
}>();

const { normalizeTraitName, getUnitName, getUnitColor, getUnitStyle, getVisibleUnitItems, getPlacementClass, getLastRoundLabel } = useTftMatchPresentation();
</script>

<template>
    <article class="min-h-[148px] rounded-xl border border-sidebar-border/70 bg-white p-3 dark:border-sidebar-border dark:bg-sidebar-accent">
        <div class="flex items-center gap-3">
            <div
                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg text-center font-bold"
                :class="getPlacementClass(props.participant.placement)"
            >
                <span class="text-lg leading-none">#{{ props.participant.placement }}</span>
            </div>

            <div class="min-w-0 flex-1 self-center">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="text-sm font-semibold text-foreground">
                        {{ props.participant.summonerName }}
                    </p>
                    <TraitBadge
                        v-for="(trait, traitIndex) in props.participant.traits"
                        :key="`participant-trait-${traitIndex}`"
                        :trait="trait"
                        :label="normalizeTraitName(trait.name ?? '')"
                    />
                </div>
            </div>

            <div class="shrink-0 rounded-md border border-sidebar-border/70 bg-muted/30 px-2 py-1 text-right text-[11px] leading-4 text-foreground dark:border-sidebar-border">
                <p><span class="font-semibold">Lvl</span> {{ props.participant.level ?? '-' }}</p>
                <p><span class="font-semibold">Gold</span> {{ props.participant.goldLeft ?? '-' }}</p>
                <p><span class="font-semibold">Round</span> {{ getLastRoundLabel(props.participant.lastRound) }}</p>
            </div>
        </div>

        <div class="mt-2 flex gap-2 overflow-x-auto pb-1">
            <div
                v-for="(unit, unitIndex) in props.participant.units"
                :key="`${unit.character_id}-${unitIndex}`"
                class="flex w-16 shrink-0 flex-col items-center gap-1"
            >
                <div class="min-h-3 text-[10px] leading-none" :style="{ color: getUnitColor(unit.rarity) }">
                    <span v-for="starIndex in unit.tier" :key="`${unit.character_id}-star-${starIndex}`">★</span>
                </div>
                <img
                    v-if="unit.icon"
                    :src="unit.icon"
                    :alt="unit.name ?? getUnitName(unit.character_id)"
                    class="h-14 w-14 rounded-md border-2 object-cover"
                    :style="getUnitStyle(unit.rarity)"
                />
                <div
                    v-else
                    class="flex h-14 w-14 items-center justify-center rounded-md border-2 bg-muted text-[10px] text-muted-foreground"
                    :style="getUnitStyle(unit.rarity)"
                >
                    {{ getUnitName(unit.character_id).slice(0, 3) }}
                </div>
                <p class="w-full truncate text-center text-[10px] leading-tight text-muted-foreground">
                    {{ unit.name ?? getUnitName(unit.character_id) }}
                </p>
                <div v-if="(unit.items ?? []).length > 0" class="mt-0.5 flex items-center justify-center gap-0.5">
                    <div
                        v-for="(item, itemIndex) in getVisibleUnitItems(unit.items ?? [])"
                        :key="`${unit.character_id}-item-${itemIndex}`"
                        class="h-4 w-4 overflow-hidden rounded-[3px] border border-sidebar-border/60 bg-muted/60"
                    >
                        <img :src="item.icon ?? ''" alt="Item" class="h-full w-full object-cover" />
                    </div>
                </div>
            </div>
        </div>
    </article>
</template>

