<script setup lang="ts">
interface MatchItem {
    placement: number;
    comp: string;
    lp: string;
    date: string;
}

interface Props {
    matches: MatchItem[];
}

defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-4">
        <article
            v-for="(match, index) in matches"
            :key="`${match.comp}-${index}`"
            class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent"
        >
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="h-12 w-12 rounded-lg text-center text-lg font-bold leading-[48px]"
                        :class="match.placement <= 4 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                    >
                        #{{ match.placement }}
                    </div>
                    <div>
                        <p class="font-semibold text-sidebar-foreground">{{ match.comp }}</p>
                        <p class="text-xs text-muted-foreground">{{ match.date }}</p>
                    </div>
                </div>
                <p class="text-sm font-semibold" :class="match.lp.startsWith('+') ? 'text-emerald-500' : 'text-rose-500'">{{ match.lp }}</p>
            </div>
        </article>
    </div>
</template>

