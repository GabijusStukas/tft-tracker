<script setup lang="ts">
import axios from 'axios';
import { Skeleton } from '@/components/ui/skeleton';
import { ref, watch } from 'vue';

interface Props {
    game: string;
    username: string;
    tagLine: string;
    region: string;
}

const props = defineProps<Props>();

const profileIconUrl = ref<string | null>(null);
const summonerLevel = ref<number | null>(null);
const isProfileLoading = ref(true);
const isImageLoading = ref(false);

async function loadSummonerProfile() {
    isProfileLoading.value = true;

    try {
        const response = await axios.get(route('riot.account.index', {
            game: props.game,
            region: props.region,
            username: props.username,
            tag_line: props.tagLine,
        }));

        profileIconUrl.value = response.data?.profile_icon_url ?? null;
        summonerLevel.value = response.data?.summoner_level ?? null;
        isImageLoading.value = Boolean(profileIconUrl.value);
    } finally {
        isProfileLoading.value = false;
    }
}

watch(
    () => [props.game, props.region, props.username, props.tagLine],
    () => {
        void loadSummonerProfile();
    },
    { immediate: true },
);
</script>

<template>
    <section class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4">
                <div class="relative h-20 w-20 overflow-visible">
                    <div class="relative h-20 w-20 overflow-hidden rounded-full bg-muted">
                        <Skeleton v-if="isProfileLoading || isImageLoading || !profileIconUrl" class="absolute inset-0 h-full w-full rounded-full" />
                        <img
                            v-if="profileIconUrl"
                            :src="profileIconUrl"
                            :alt="`${username} profile icon`"
                            class="h-full w-full object-cover transition-opacity duration-200"
                            :class="isImageLoading ? 'opacity-0' : 'opacity-100'"
                            @load="isImageLoading = false"
                            @error="isImageLoading = false"
                        />
                    </div>
                    <Skeleton v-if="isProfileLoading" class="absolute -bottom-1.5 -left-1.5 h-6 w-10 rounded-md" />
                    <span
                        v-else
                        class="absolute -bottom-1.5 -left-1.5 rounded-md bg-black/70 px-1.5 text-xs font-semibold leading-6 text-white"
                    >
                        {{ summonerLevel ?? '-' }}
                    </span>
                </div>
                <div>
                    <p class="text-xl font-semibold text-sidebar-foreground">
                        {{ username }}<span class="ml-1 text-sm text-muted-foreground">#{{ tagLine }}</span>
                    </p>
                    <Skeleton v-if="isProfileLoading" class="mt-1 h-4 w-32" />
                    <p v-else class="text-sm text-muted-foreground">{{ region.toUpperCase() }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-right sm:grid-cols-4">
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">Tier</p>
                    <p class="font-semibold text-sidebar-foreground">Master</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">LP</p>
                    <p class="font-semibold text-sidebar-foreground">241</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">Top 4</p>
                    <p class="font-semibold text-emerald-500">61%</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-muted-foreground">Avg Place</p>
                    <p class="font-semibold text-sidebar-foreground">4.12</p>
                </div>
            </div>
        </div>
    </section>
</template>

