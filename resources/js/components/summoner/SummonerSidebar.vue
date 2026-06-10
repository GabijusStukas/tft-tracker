<script setup lang="ts">
interface TopCompItem {
    name: string;
    games: number;
    avg: number;
}

interface Props {
    topComps: TopCompItem[];
}

defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-4">
        <aside class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
            <h2 class="text-sm font-semibold text-sidebar-foreground">Ranked Summary</h2>
            <div class="mt-3 grid grid-cols-2 gap-3 text-sm">
                <div>
                    <p class="text-muted-foreground">Games</p>
                    <p class="font-semibold text-sidebar-foreground">143</p>
                </div>
                <div>
                    <p class="text-muted-foreground">Win Rate</p>
                    <p class="font-semibold text-sidebar-foreground">13%</p>
                </div>
                <div>
                    <p class="text-muted-foreground">Top 4 Rate</p>
                    <p class="font-semibold text-sidebar-foreground">61%</p>
                </div>
                <div>
                    <p class="text-muted-foreground">Bottom 4</p>
                    <p class="font-semibold text-sidebar-foreground">39%</p>
                </div>
            </div>
        </aside>

        <aside class="rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent">
            <h2 class="text-sm font-semibold text-sidebar-foreground">Most Played Comps</h2>
            <div class="mt-3 space-y-3">
                <div v-for="comp in topComps" :key="comp.name" class="rounded-lg bg-[#f6f8fb] p-3 dark:bg-sidebar">
                    <p class="text-sm font-medium text-sidebar-foreground">{{ comp.name }}</p>
                    <p class="text-xs text-muted-foreground">{{ comp.games }} games · {{ comp.avg }} avg place</p>
                </div>
            </div>
        </aside>
    </div>
</template>

