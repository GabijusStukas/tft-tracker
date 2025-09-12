<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

const emit = defineEmits(['food-added']);
const addFoodDialogOpen = ref(false);
const nameInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    name: '',
    description: '',
    calories: null,
    protein: null,
    carbs: null,
    fat: null,
    image: null as File | null,
});

function closeDialog() {
    addFoodDialogOpen.value = false;
    form.reset();
}

function handleImageUpload(e: Event) {
    const input = e.target as HTMLInputElement;
    if (input.files?.length) {
        form.image = input.files[0];
    }
}

function submit(e: Event) {
    e.preventDefault();
    form.post(route('submit-meal'), {
        onSuccess: (page) => {
            emit('food-added', page.props.food);
            closeDialog();
        },
        onError: () => {
        },
        preserveScroll: true,
        forceFormData: true,
    });
}
</script>

<template>
    <Dialog v-model:open="addFoodDialogOpen">
        <DialogTrigger class="w-fit" as-child>
            <Button>Add New Food</Button>
        </DialogTrigger>
        <DialogContent>
            <form class="space-y-6" @submit="submit">
                <DialogHeader class="space-y-3">
                    <DialogTitle>Add New Food</DialogTitle>
                    <DialogDescription>
                        Fill in the details to add a new food item.
                    </DialogDescription>
                </DialogHeader>
                <div class="grid gap-2">
                    <Label for="name" class="sr-only">Name</Label>
                    <Input id="name" ref="nameInput" v-model="form.name" placeholder="Name" required />
                </div>
                <div class="grid gap-2">
                    <Label for="description" class="sr-only">Description</Label>
                    <Input id="description" v-model="form.description" placeholder="Description" />
                </div>
                <div class="grid gap-2">
                    <Label for="calories" class="sr-only">Calories</Label>
                    <Input id="calories" type="number" v-model.number="form.calories" placeholder="Calories" min="0" />
                </div>
                <div class="grid gap-2">
                    <Label for="protein" class="sr-only">Protein</Label>
                    <Input id="protein" type="number" v-model.number="form.protein" placeholder="Protein (g)" min="0" />
                </div>
                <div class="grid gap-2">
                    <Label for="carbs" class="sr-only">Carbs</Label>
                    <Input id="carbs" type="number" v-model.number="form.carbs" placeholder="Carbs (g)" min="0" />
                </div>
                <div class="grid gap-2">
                    <Label for="fat" class="sr-only">Fat</Label>
                    <Input id="fat" type="number" v-model.number="form.fat" placeholder="Fat (g)" min="0" />
                </div>
                <div class="grid gap-2">
                    <Label for="image">Image</Label>
                    <Input id="image" type="file" accept="image/*" @change="handleImageUpload" />
                </div>
                <DialogFooter class="gap-2">
                    <DialogClose as-child>
                        <Button variant="secondary" @click="closeDialog">Cancel</Button>
                    </DialogClose>
                    <Button type="submit" :disabled="form.processing">Add Food</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
