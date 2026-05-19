import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import GuestLayout from './Layouts/GuestLayout.vue'; 

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
    const page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
    
    page.then((module) => {
        // Tự động gán GuestLayout cho TẤT CẢ các trang (kể cả Login, Register) 
        // miễn là trang đó chưa khai báo layout riêng.
        module.default.layout = module.default.layout || GuestLayout;
    });
    
    return page;
},
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue) // Thử bỏ biến Ziggy ở đây nếu bị lỗi trắng trang
            .mount(el);
    },
    progress: {
        color: '#4A7055', // Màu xanh của Duy luôn
    },
});