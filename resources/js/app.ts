import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';
import { setupAxiosInterceptors } from './lib/axiosSetup';
import { useAuth } from './composables/useAuth';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

setupAxiosInterceptors();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin)
            .use(ZiggyVue)
            .mount(el);

        const auth = useAuth();
        if (auth.isAuthenticated.value) {
            auth.setupAutoRefresh();
        }
    },
    progress: {
        color: '#4B5563',
    },
});

initializeTheme();
