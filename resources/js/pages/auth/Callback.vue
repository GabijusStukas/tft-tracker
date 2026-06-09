<script setup lang="ts">
import { onMounted, defineProps } from 'vue';
import { useAuth } from '@/composables/useAuth';

const props = defineProps({
    access_token: String,
    token_type: String,
    expires_in: Number,
});

const auth = useAuth();

onMounted(async () => {
    try {
        if (props.access_token) {
            auth.setToken(props.access_token);

            await auth.fetchUser();

            auth.setupAutoRefresh();

            window.location.href = '/';
        } else {
            window.location.href = '/login';
        }
    } catch (error) {
        console.error('OAuth callback error:', error);
        window.location.href = '/login';
    }
});
</script>

<template>
    <div class="flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-2xl font-bold mb-4">Completing sign in...</h1>
            <p class="text-muted-foreground">Please wait while we complete your authentication.</p>
        </div>
    </div>
</template>
