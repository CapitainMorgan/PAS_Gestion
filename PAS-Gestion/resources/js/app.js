import '../css/app.css';
import '../css/table.css'
import '../css/pagination.css'
import '../css/default.css'
import './bootstrap';



import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css';

import axios from 'axios';
window.axios = axios;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, {
                position: POSITION.TOP_RIGHT,
                timeout: 5000,
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
