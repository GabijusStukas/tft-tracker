<script setup lang="ts">
interface TraitBadgeItem {
    icon?: string | null;
    name?: string | null;
    style?: number;
}

const props = defineProps<{
    trait: TraitBadgeItem;
    label: string;
}>();

function getTraitAccentColor(style: number): string {
    const styleColors: Record<number, string> = {
        1: '#8C5A2B',
        2: '#C0C0C0',
        3: '#FFD700',
        4: '#7DD3FC',
    };

    return styleColors[style] ?? '#9CA3AF';
}

function getTraitBadgeClass(style: number): string {
    if (style === 5) {
        return 'trait-badge-rainbow';
    }

    if (style === 4) {
        return 'trait-badge-prismatic';
    }

    return 'trait-badge-default';
}

function getTraitBadgeStyle(style: number): Record<string, string> {
    if (style === 4 || style === 5) {
        return {};
    }

    return {
        '--trait-border-color': getTraitAccentColor(style),
    };
}

function getTraitIconStyle(style: number): Record<string, string> {
    return {
        filter: `drop-shadow(0 0 2px ${getTraitAccentColor(style)})`,
    };
}
</script>

<template>
    <div class="group relative">
        <div
            class="trait-badge"
            :class="[getTraitBadgeClass(props.trait.style ?? 0)]"
            :style="getTraitBadgeStyle(props.trait.style ?? 0)"
            :aria-label="props.label"
        >
            <img
                v-if="props.trait.icon"
                :src="props.trait.icon"
                :alt="props.label"
                class="h-5 w-5 object-contain"
                :style="getTraitIconStyle(props.trait.style ?? 0)"
            />
            <span v-else class="text-[10px] font-semibold text-zinc-100">
                {{ props.label.slice(0, 1) }}
            </span>
        </div>
        <div class="trait-badge-tooltip">
            {{ props.label }}
        </div>
    </div>
</template>

<style scoped src="./TraitBadge.css"></style>

