<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { type BreadcrumbItem } from '@/types';
import { LoaderCircle } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';

const form = ref({
    username: '',
    tag_line: ''
})
const result = ref(null)
const error = ref(null)
const loading = ref(false)

async function searchSummoner() {
    error.value = null
    loading.value = true
    try {
        const response = await axios.get(route('summoner.search'), {
            params: {
                username: form.value.username,
                tag_line: form.value.tag_line
            }
        })
        result.value = response.data
    } catch (e) {
        error.value = e.response?.data?.message || 'An error occurred.'
    } finally {
        loading.value = false
    }
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Welcome',
        href: '/',
    },
];
</script>

<template>
    <Head title="TFTracker" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col items-center justify-center gap-4 rounded-xl p-1 overflow-x-auto">
            <div
                class="w-full max-w-2xl p-8 bg-white rounded-4xl shadow-lg dark:bg-gray-800 transition-all duration-300">
                <form @submit.prevent="searchSummoner" class="flex items-end gap-6 mb-2">
                    <div class="flex flex-col w-3/5">
                        <label class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Username</label>
                        <input v-model="form.username" type="text"
                               class="px-4 py-2.5 h-[42px]  border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all duration-200 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               required maxlength="100" />
                    </div>
                    <div class="flex flex-col w-1/4">
                    <label class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-200">Tag Line</label>
                        <input v-model="form.tag_line" type="text"
                               class="px-4 py-2.5 h-[42px] text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all duration-200 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               maxlength="100" />
                    </div>
                    <Button :disabled="loading" type="submit" class="min-w-[100px] h-[42px] py-2.5">
                    <span v-if="!loading">Search</span>
                        <LoaderCircle v-else class="h-4 w-4 animate-spin" />
                    </Button>
                </form>
                <div v-if="error"
                     class="mt-4 p-4 bg-red-100 border border-red-200 rounded-md text-red-600 text-center dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
                    {{ error }}
                </div>
                <div v-if="result" class="mt-6 flex justify-center">
                    <div
                        class="w-full max-w-lg bg-gray-100 dark:bg-gray-700 rounded-lg shadow-md p-6 overflow-auto transition-all duration-300">
                        <h3 class="text-lg font-bold mb-4 text-gray-800 dark:text-gray-200">Summoner Result</h3>
                        <pre
                            class="whitespace-pre-wrap break-words text-gray-700 dark:text-gray-100 max-h-64 overflow-y-auto px-4 py-3 bg-white dark:bg-gray-800 rounded-md">
                            {{ result }}
                        </pre>
                    </div>
                </div>
            </div>
</div>
    </AppLayout>

</template>
