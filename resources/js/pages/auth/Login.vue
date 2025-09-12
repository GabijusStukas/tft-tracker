<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { ref } from 'vue';

const processing = ref(false);

const redirectToGoogle = () => {
    processing.value = true;
    window.location.href = '/auth/google/redirect';
};
</script>

<template>
    <AuthBase title="Log in to your account" description="Please log in to continue.">
        <Head title="Log in" />

        <Button :disabled="processing" @click="redirectToGoogle" class="mt-4 w-full google-login">
            <span class="google-icon" v-if="!processing">
                <svg width="20" height="20" viewBox="0 0 48 48"><g><path fill="#4285F4" d="M24 9.5c3.54 0 6.7 1.23 9.19 3.25l6.85-6.85C36.09 2.36 30.45 0 24 0 14.61 0 6.36 5.64 2.44 14.02l7.98 6.21C12.18 13.09 17.62 9.5 24 9.5z"/><path fill="#34A853" d="M46.09 24.5c0-1.64-.15-3.22-.43-4.75H24v9.02h12.44c-.54 2.91-2.18 5.38-4.65 7.04l7.98 6.21C44.36 38.36 46.09 31.89 46.09 24.5z"/><path fill="#FBBC05" d="M10.42 28.23c-.63-1.89-.99-3.89-.99-5.98s.36-4.09.99-5.98l-7.98-6.21C2.16 13.09 0 18.29 0 24c0 5.71 2.16 10.91 5.44 14.02l7.98-6.21z"/><path fill="#EA4335" d="M24 48c6.45 0 12.09-2.36 16.64-6.44l-7.98-6.21c-2.21 1.49-5.04 2.38-8.66 2.38-6.38 0-11.82-3.59-14.58-8.73l-7.98 6.21C6.36 42.36 14.61 48 24 48z"/></g></svg>
            </span>
            <span class="ml-2" v-if="!processing">Log in with Google</span>

            <LoaderCircle v-if="processing" class="h-4 w-4 animate-spin" />
        </Button>

    </AuthBase>
</template>
