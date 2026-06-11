<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';
import { useRiotApi } from '@/composables/useRiotApi';
import { ref, watch } from 'vue';

interface Props {
    game: string;
    username: string;
    tagLine: string;
    region: string;
    refreshToken: number;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    (event: 'refresh-all'): void;
}>();

const profileIconUrl = ref<string | null>(null);
const summonerLevel = ref<number | null>(null);
const isProfileLoading = ref(true);
const isImageLoading = ref(false);

const { fetchSummonerProfile } = useRiotApi();

async function loadSummonerProfile(refresh = false) {
    isProfileLoading.value = true;

    try {
        const previousIconUrl = profileIconUrl.value;
        const response = await fetchSummonerProfile({
            game: props.game,
            region: props.region,
            username: props.username,
            tagLine: props.tagLine,
        }, { refresh });

        const nextIconUrl = response?.profile_icon_url ?? null;
        profileIconUrl.value = nextIconUrl;
        summonerLevel.value = response?.summoner_level ?? null;
        // If URL did not change, keep current image visible instead of waiting for a load event that may not fire again.
        isImageLoading.value = Boolean(nextIconUrl && nextIconUrl !== previousIconUrl);
    } finally {
        isProfileLoading.value = false;
    }
}

watch(
    () => [props.game, props.region, props.username, props.tagLine],
    () => {
        void loadSummonerProfile(false);
    },
    { immediate: true },
);

watch(
    () => props.refreshToken,
    (value) => {
        if (value > 0) {
            void loadSummonerProfile(true);
        }
    },
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
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-md border border-sidebar-border/70 px-3 py-1.5 text-xs font-medium text-sidebar-foreground transition-colors hover:bg-sidebar-accent disabled:cursor-not-allowed disabled:opacity-50 dark:border-sidebar-border dark:hover:bg-sidebar"
                :disabled="isProfileLoading"
                @click="emit('refresh-all')"
            >
                {{ isProfileLoading ? 'Refreshing...' : 'Refresh Data' }}
            </button>
        </div>
    </section>
</template>

