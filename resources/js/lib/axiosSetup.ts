import axios from 'axios';
import { useAuth } from '@/composables/useAuth';

export function setupAxiosInterceptors() {
    axios.interceptors.request.use((config) => {
        const auth = useAuth();
        const token = auth.token.value;

        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }

        return config;
    });

    axios.interceptors.response.use(
        (response) => response,
        async (error) => {
            const originalRequest = error.config;

            // If 401 and haven't retried yet, try to refresh token
            if (error.response?.status === 401 && !originalRequest._retry) {
                originalRequest._retry = true;
                const auth = useAuth();

                try {
                    const refreshed = await auth.refreshToken();
                    if (refreshed) {
                        // Retry original request with new token
                        return axios(originalRequest);
                    } else {
                        // Refresh failed, redirect to login
                        window.location.href = '/login';
                    }
                } catch (e) {
                    window.location.href = '/login';
                    return Promise.reject(e);
                }
            }

            return Promise.reject(error);
        }
    );
}

