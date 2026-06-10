<template>
    <article
        class="min-h-[172px] animate-pulse rounded-xl border border-sidebar-border/70 bg-white p-4 dark:border-sidebar-border dark:bg-sidebar-accent"
    >
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-lg bg-muted" />
                <div class="space-y-1">
                    <div class="h-5 w-36 rounded bg-muted" />
                    <div class="h-3 w-28 rounded bg-muted" />
                </div>
            </div>
            <div class="h-4 w-20 rounded bg-muted" />
        </div>

        <div class="mt-3 flex gap-2 overflow-x-auto pb-1">
            <div v-for="unit in 7" :key="`skeleton-unit-${unit}`" class="flex w-16 shrink-0 flex-col items-center gap-1">
                <div class="min-h-3 w-8 rounded bg-muted" />
                <div class="h-14 w-14 rounded-md border-2 border-sidebar-border/70 bg-muted" />
                <div class="h-[10px] w-full rounded bg-muted" />
            </div>
        </div>
    </article>
</template>

