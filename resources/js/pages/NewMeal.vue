<script setup lang="ts">
import { ref } from 'vue';
import {Head} from "@inertiajs/vue3";
import type {BreadcrumbItem} from "@/types";
import AppLayout from "@/layouts/AppLayout.vue";
import MealDetails from "@/components/new-meal/MealDetails.vue";
import AddFood from "@/components/new-meal/AddFood.vue";
import Heading from "@/components/Heading.vue";
import FoodByUsers from "@/components/new-meal/FoodByUsers.vue";

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'New Meal', href: '/new-meal' },
];

const foods = ref<any[]>([]);

function handleFoodAdded(food: any) {
    foods.value.push(food);
}
</script>

<template>
    <Head title="New Meal" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">

            <div class="flex gap-4 items-center">
                <AddFood @food-added="handleFoodAdded"></AddFood>
                <Heading class="!m-0" title="Food list" description="Items added to your list" />
            </div>


            <div class="relative h-[400px] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border overflow-auto">
                <MealDetails :foods="foods" />
            </div>

            <Heading class="!m-0" title="Food by users" description="Browse food items created by other users" />
            <div class="relative h-[400px] rounded-xl border border-sidebar-border/70 dark:border-sidebar-border overflow-auto">
                <FoodByUsers />
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    background: #2563eb;
    color: #fff;
    border: none;
    cursor: pointer;
}
</style>
