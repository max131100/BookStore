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
        path: '/signUp',
        component: () => import('./components/SignUp.vue'),
        name: 'signUp'
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

router.beforeEach((to, from, next) => {
    const accessToken = localStorage.getItem('token');
    if (to.name === 'cart') {
        if (!accessToken) {
            return next({
                name: 'login'
            });
        }
    }

    return next();
})

export default router;
