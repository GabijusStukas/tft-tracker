<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { useRiotApi } from '@/composables/useRiotApi';
import type { LeagueEntry } from '@/types/riot';
import { computed, ref, watch } from 'vue';

interface Props {
    game: string;
    region: string;
    username: string;
    tagLine: string;
    refreshToken: number;
}

const props = defineProps<Props>();

const leagues = ref<LeagueEntry[]>([]);
const isLoading = ref(false);
const selectedIndex = ref(0);
const { fetchSummonerLeague } = useRiotApi();

const selected = computed<LeagueEntry | null>(() => leagues.value[selectedIndex.value] ?? null);

function queueLabel(queueType: string): string {
    if (queueType === 'RANKED_TFT') return 'Ranked TFT';
    if (queueType === 'RANKED_TFT_DOUBLE_UP') return 'Double Up';
    return queueType.replace(/^RANKED_TFT_?/, '').replace(/_/g, ' ');
}

function winRate(wins: number, losses: number): string {
    const total = wins + losses;
    if (total === 0) return '0%';
    return `${Math.round((wins / total) * 100)}%`;
}

async function loadLeague(refresh = false) {
    isLoading.value = true;
    try {
        leagues.value = await fetchSummonerLeague({
            game: props.game,
            region: props.region,
            username: props.username,
            tagLine: props.tagLine,
        }, { refresh });
        selectedIndex.value = 0;
    } finally {
        isLoading.value = false;
    }
}

watch(
    () => [props.game, props.region, props.username, props.tagLine],
    () => { void loadLeague(false); },
    { immediate: true },
);

watch(
    () => props.refreshToken,
    (value) => {
        if (value > 0) {
            void loadLeague(true);
        }
    },
);
</script>

<template>
    <div class="flex flex-col gap-4">
        <!-- Ranked Summary -->
        <aside class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
            <div class="mb-3 flex items-center justify-between gap-2">
                <h2 class="text-sm font-semibold text-sidebar-foreground">Ranked Summary</h2>

                <!-- Queue selector -->
                <div v-if="!isLoading && leagues.length > 1" class="flex gap-1">
                    <button
                        v-for="(league, i) in leagues"
                        :key="league.queue_type"
                        class="rounded px-2 py-0.5 text-xs font-medium transition-colors"
                        :class="selectedIndex === i
                            ? 'bg-sidebar-accent text-sidebar-foreground dark:bg-sidebar'
                            : 'text-muted-foreground hover:text-sidebar-foreground'"
                        @click="selectedIndex = i"
                    >
                        {{ queueLabel(league.queue_type) }}
                    </button>
                </div>
            </div>

            <!-- Loading skeleton -->
            <template v-if="isLoading">
                <div class="flex items-center gap-3">
                    <Skeleton class="h-14 w-14 rounded-md" />
                    <div class="flex-1 space-y-2">
                        <Skeleton class="h-4 w-24" />
                        <Skeleton class="h-3 w-16" />
                        <Skeleton class="h-3 w-12" />
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-2 gap-3">
                    <Skeleton v-for="n in 4" :key="n" class="h-10 rounded-md" />
                </div>
            </template>

            <!-- No data -->
            <p v-else-if="!selected" class="text-xs text-muted-foreground">No ranked data available.</p>

            <!-- League data -->
            <template v-else>
                <div class="flex items-center gap-3">
                    <img
                        :src="selected.icon"
                        :alt="selected.tier"
                        class="h-14 w-14 rounded-md object-contain"
                    />
                    <div>
                        <p class="text-base font-bold capitalize text-sidebar-foreground leading-tight">
                            {{ selected.tier.charAt(0) + selected.tier.slice(1).toLowerCase() }}
                            {{ selected.rank }}
                            <span class="text-sm font-medium text-muted-foreground">· {{ selected.league_points }} LP</span>
                        </p>
                        <p class="text-xs text-muted-foreground">{{ queueLabel(selected.queue_type) }}</p>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-muted-foreground">Total Matches</p>
                        <p class="font-semibold text-sidebar-foreground">{{ selected.wins + selected.losses }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Win Rate</p>
                        <p class="font-semibold text-sidebar-foreground">{{ winRate(selected.wins, selected.losses) }}</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Wins</p>
                        <p class="font-semibold text-emerald-500">{{ selected.wins }}W</p>
                    </div>
                    <div>
                        <p class="text-muted-foreground">Losses</p>
                        <p class="font-semibold text-red-500">{{ selected.losses }}L</p>
                    </div>
                </div>
            </template>
        </aside>
    </div>
</template>
