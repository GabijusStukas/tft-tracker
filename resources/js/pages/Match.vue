<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import MatchParticipantCard from '@/components/match/MatchParticipantCard.vue';
import { useRiotApi } from '@/composables/useRiotApi';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface MatchParticipant {
    game_name: string;
    tag_line: string;
    placement: number;
    level?: number;
    gold_left?: number;
    last_round?: number;
    traits: Array<{
        name: string;
        icon?: string | null;
        num_units?: number;
        style: number;
    }>;
    units: Array<{
        character_id?: string;
        name: string;
        icon?: string | null;
        rarity?: number;
        tier: number;
        items?: Array<{ icon?: string | null }>;
    }>;
}

interface ParticipantCardItem {
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

interface Props {
    matchId: string;
}

const props = defineProps<Props>();
const { fetchMatchDetails } = useRiotApi();
const queueName = ref('');
const matchCreatedAt = ref('');
const participantCards = ref<ParticipantCardItem[]>([]);
const isLoading = ref(false);
const errorMessage = ref<string | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Match',
        href: `/tft/match/${props.matchId}`,
    },
];

function formatDate(value?: string): string {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return date.toLocaleString();
}

function asArray<T>(value: unknown): T[] {
    if (Array.isArray(value)) {
        return value as T[];
    }

    if (value && typeof value === 'object') {
        return Object.values(value as Record<string, T>);
    }

    return [];
}

async function loadMatchDetails(): Promise<void> {
    isLoading.value = true;
    errorMessage.value = null;

    try {
        const rawMatch = await fetchMatchDetails(props.matchId);
        const resolvedQueueName = String(rawMatch?.['queue_name'] ?? '');
        const resolvedMatchDate = String(rawMatch?.['match_created_at'] ?? '');
        const sortedParticipants = asArray<MatchParticipant>(rawMatch?.['participants'])
            .slice()
            .sort((a, b) => (a.placement ?? 99) - (b.placement ?? 99));

        queueName.value = resolvedQueueName;
        matchCreatedAt.value = resolvedMatchDate;

        participantCards.value = sortedParticipants.map((participant, participantIndex) => ({
            placement: Number(participant.placement ?? 8),
            summonerName: `${participant.game_name ?? 'Unknown'}#${participant.tag_line ?? '-'}`,
            level: Number.isFinite(Number(participant.level)) ? Number(participant.level) : null,
            goldLeft: Number.isFinite(Number(participant.gold_left)) ? Number(participant.gold_left) : null,
            lastRound: Number.isFinite(Number(participant.last_round)) ? Number(participant.last_round) : null,
            traits: asArray<any>(participant.traits)
                .filter((trait: any) => Number(trait?.style ?? 0) > 0)
                .sort((a: any, b: any) => Number(b?.style ?? 0) - Number(a?.style ?? 0) || Number(b?.num_units ?? 0) - Number(a?.num_units ?? 0))
                .map((trait: any) => ({
                    icon: trait?.icon ?? null,
                    name: trait?.name ?? null,
                    style: Number(trait?.style ?? 0),
                    num_units: Number(trait?.num_units ?? 0),
                })),
            units: asArray<any>(participant.units).map((unit: any, unitIndex: number) => ({
                character_id: String(unit?.character_id ?? unit?.name ?? `unit-${participantIndex}-${unitIndex}`),
                name: unit?.name ?? null,
                icon: unit?.icon ?? null,
                rarity: Number(unit?.rarity ?? 0),
                tier: Number(unit?.tier ?? 0),
                items: asArray<any>(unit?.items).slice(0, 3).map((item: any) => ({
                    icon: item?.icon ?? null,
                })),
            })),
        }));
    } catch (error: any) {
        queueName.value = '';
        matchCreatedAt.value = '';
        participantCards.value = [];
        errorMessage.value = String(error?.response?.data?.message ?? 'Failed to load match details.');
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    void loadMatchDetails();
});
</script>

<template>
    <Head :title="`Match ${props.matchId}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="min-h-full bg-[#f3f5f8] p-4 dark:bg-sidebar">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-4">
                <article class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
                    <h1 class="text-lg font-semibold text-foreground">Match Details</h1>
                    <div class="mt-3 grid gap-3 text-sm sm:grid-cols-3">
                        <p><span class="font-semibold">Queue:</span> {{ queueName || '-' }}</p>
                        <p><span class="font-semibold">Match Date:</span> {{ formatDate(matchCreatedAt) }}</p>
                        <p>
                            <span class="font-semibold">ID:</span>
                            <span class="font-mono text-foreground">{{ props.matchId }}</span>
                        </p>
                    </div>
                </article>

                <section class="flex flex-col gap-4">
                    <p v-if="isLoading" class="rounded-xl border border-dashed border-sidebar-border/70 bg-white p-6 text-sm text-muted-foreground dark:border-sidebar-border dark:bg-sidebar-accent">
                        Loading match details...
                    </p>

                    <p v-else-if="errorMessage" class="rounded-xl border border-rose-300/70 bg-rose-50 px-3 py-2 text-sm text-rose-800 dark:border-rose-500/40 dark:bg-rose-500/10 dark:text-rose-200">
                        {{ errorMessage }}
                    </p>

                    <template v-else-if="participantCards.length > 0">
                        <MatchParticipantCard
                            v-for="(participant, index) in participantCards"
                            :key="`${participant.summonerName}-${index}`"
                            :participant="participant"
                        />
                    </template>

                    <article
                        v-else
                        class="rounded-xl border border-dashed border-sidebar-border/70 bg-white p-6 text-sm text-muted-foreground dark:border-sidebar-border dark:bg-sidebar-accent"
                    >
                        No participants found for this match.
                    </article>
                </section>
            </div>
        </div>
    </AppLayout>
</template>

