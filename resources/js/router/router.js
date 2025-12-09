
import { createRouter, createWebHistory } from 'vue-router';
import Login from '../views/Login.vue';
import Products from '../views/components/Products.vue';

const routes = [
  { path: '/', component: Login, meta: { requiresGuest: true } },
  { path: '/products', component: Products, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  if (to.meta.requiresAuth && !token) {
    next('/');
  } else if (to.meta.requiresGuest && token) {
    next('/products');
  } else {
    next();
  }
});

export default router;
