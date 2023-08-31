import {createRouter, createWebHistory} from "vue-router";

const routes = [
    {
        path: '/',
        component: () => import('./components/Home.vue'),
        name: 'home.index'
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
