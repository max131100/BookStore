import {createRouter, createWebHistory} from "vue-router";

const routes = [
    {
        path: '/',
        component: () => import('./components/Home.vue'),
        name: 'home.index'
    },

    {
        path: '/login',
        component: () => import('./components/SignIn.vue'),
        name: 'login'
    },

    {
        path: '/cart',
        component: () => import('./components/Cart.vue'),
        name: 'cart'
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
