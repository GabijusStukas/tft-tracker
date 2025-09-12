<script setup>
import { ref } from 'vue'
import axios from 'axios'

const form = ref({
    region: '',
    username: '',
    tag_line: ''
})
const result = ref(null)
const error = ref(null)
const loading = ref(false)

async function searchSummoner() {
    result.value = null
    error.value = null
    loading.value = true
    try {
        const response = await axios.get(route('summoner.search'), {
            params: {
                region: form.value.region,
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
</script>

<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
        <div class="w-full max-w-2xl p-8 bg-white rounded-lg shadow-lg dark:bg-gray-800 transition-all duration-300">
            <form @submit.prevent="searchSummoner" class="flex flex-wrap items-end gap-6 mb-8">
                <div class="flex flex-col w-1/4">
                    <label class="mb-2 font-medium text-gray-700 dark:text-gray-200">Region</label>
                    <input v-model="form.region" type="text"
                           class="px-4 py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all duration-200 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           required maxlength="10" />
                </div>
                <div class="flex flex-col w-1/3">
                    <label class="mb-2 font-medium text-gray-700 dark:text-gray-200">Username</label>
                    <input v-model="form.username" type="text"
                           class="px-4 py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all duration-200 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           required maxlength="100" />
                </div>
                <div class="flex flex-col w-1/3">
                    <label class="mb-2 font-medium text-gray-700 dark:text-gray-200">Tag Line</label>
                    <input v-model="form.tag_line" type="text"
                           class="px-4 py-2.5 border border-gray-300 rounded-md focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition-all duration-200 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                           maxlength="100" />
                </div>
                <button type="submit"
                        class="py-2.5 px-8 bg-orange-500 text-white rounded-md hover:bg-orange-600 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="loading">
                    <span v-if="loading">Searching...</span>
                    <span v-else>Search</span>
                </button>
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
                        class="whitespace-pre-wrap break-words text-gray-700 dark:text-gray-100 max-h-64 overflow-y-auto px-4 py-3 bg-white dark:bg-gray-800 rounded-md">{{ result
                        }}</pre>
                </div>
            </div>
        </div>
    </div>
</template>
