<script setup lang="ts">
defineProps<{
    foods: Array<{
        id: number;
        name: string;
        description: string | null;
        calories: number | null;
        protein: number | null;
        carbs: number | null;
        fat: number | null;
        image: string | null;
    }>;
}>();
</script>

<template>
    <div class="p-4 space-y-4">
        <div v-for="food in foods" :key="food.id" class="flex gap-4 p-4 border rounded-lg">
            <div v-if="food.image" class="w-24 h-24 flex-shrink-0">
                <img
                    :src="route('food.image', { food: food.id })"
                    :alt="food.name"
                    class="w-full h-full object-cover rounded-lg"
                />
            </div>
            <div>
                <h3 class="font-semibold text-lg">{{ food.name }}</h3>
                <p v-if="food.description" class="text-gray-600 dark:text-gray-400">{{ food.description }}</p>
                <div class="mt-2 grid grid-cols-4 gap-4">
                    <div v-if="food.calories != null">
                        <span class="text-sm text-gray-500">Calories:</span>
                        <p>{{ food.calories }}</p>
                    </div>
                    <div v-if="food.protein != null">
                        <span class="text-sm text-gray-500">Protein:</span>
                        <p>{{ food.protein }}g</p>
                    </div>
                    <div v-if="food.carbs != null">
                        <span class="text-sm text-gray-500">Carbs:</span>
                        <p>{{ food.carbs }}g</p>
                    </div>
                    <div v-if="food.fat != null">
                        <span class="text-sm text-gray-500">Fat:</span>
                        <p>{{ food.fat }}g</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
