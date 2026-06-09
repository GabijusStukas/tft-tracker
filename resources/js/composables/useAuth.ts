import { ref, computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at?: string;
}

interface AuthResponse {
    access_token: string;
    token_type: string;
    expires_in: number;
    user: User;
}

const JWT_COOKIE_NAME = 'jwt_token';

const getCookie = (name: string): string | null => {
    if (typeof document === 'undefined') return null;

    const cookie = document.cookie
        .split('; ')
        .find((entry) => entry.startsWith(`${name}=`));

    if (!cookie) return null;

    return decodeURIComponent(cookie.slice(name.length + 1));
};

const setCookie = (name: string, value: string): void => {
    if (typeof document === 'undefined') return;

    document.cookie = `${name}=${encodeURIComponent(value)}; path=/; SameSite=Lax`;
};

const clearCookie = (name: string): void => {
    if (typeof document === 'undefined') return;

    document.cookie = `${name}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT; SameSite=Lax`;
};

const token = ref<string | null>(localStorage.getItem(JWT_COOKIE_NAME) ?? getCookie(JWT_COOKIE_NAME));
const user = ref<User | null>(null);
const isAuthenticated = computed(() => !!token.value);

if (token.value && !getCookie(JWT_COOKIE_NAME)) {
    setCookie(JWT_COOKIE_NAME, token.value);
}

const fetchUserFromServer = async () => {
    if (!token.value) return null;

    try {
        const resp = await fetch('/auth/user', {
            headers: {
                'Authorization': `Bearer ${token.value}`,
                'Content-Type': 'application/json',
            },
        });

        if (!resp.ok) {
            return null;
        }

        const data = await resp.json();
        user.value = data;
        return data;
    } catch (e) {
        console.error('Failed to fetch user from server', e);
        return null;
    }
};

export const useAuth = () => {
    (async () => {
        if (token.value && !user.value) {
            await fetchUserFromServer();
        }
    })();

    const setToken = (newToken: string) => {
        token.value = newToken;
        localStorage.setItem(JWT_COOKIE_NAME, newToken);
        setCookie(JWT_COOKIE_NAME, newToken);
    };

    const setUser = (newUser: User) => {
        user.value = newUser;
    };

    const login = (authResponse: AuthResponse) => {
        setToken(authResponse.access_token);
        fetchUserFromServer();
    };

    const logout = async () => {
        try {
            await fetch('/api/auth/logout', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token.value}`,
                    'Content-Type': 'application/json',
                },
            });
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            token.value = null;
            user.value = null;
            localStorage.removeItem(JWT_COOKIE_NAME);
            clearCookie(JWT_COOKIE_NAME);
        }
    };

    const refreshToken = async () => {
        if (!token.value) return false;

        try {
            const response = await fetch('/api/auth/refresh', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token.value}`,
                    'Content-Type': 'application/json',
                },
            });

            if (!response.ok) {
                await logout();
                return false;
            }

            const data = await response.json();
            setToken(data.access_token);
            return true;
        } catch (error) {
            console.error('Token refresh error:', error);
            await logout();
            return false;
        }
    };

    const getAuthHeader = () => {
        if (!token.value) return {};
        return {
            'Authorization': `Bearer ${token.value}`,
        };
    };

    // Set up auto-refresh 50 minutes from now (token expires in 60 minutes)
    const setupAutoRefresh = () => {
        if (typeof window !== 'undefined' && token.value) {
            setInterval(() => {
                refreshToken();
            }, 50 * 60 * 1000);
        }
    };

    return {
        token: computed(() => token.value),
        user: computed(() => user.value),
        isAuthenticated,
        setToken,
        setUser,
        login,
        logout,
        refreshToken,
        fetchUser: fetchUserFromServer,
        getAuthHeader,
        setupAutoRefresh,
    };
};

