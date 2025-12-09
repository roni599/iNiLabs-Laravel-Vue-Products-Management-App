import { createApp } from 'vue';
import router from './router/router';
import App from './App.vue';

import '@fortawesome/fontawesome-free/css/all.css';

createApp(App).use(router).mount('#app');