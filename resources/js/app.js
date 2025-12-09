// import { createApp } from 'vue';
// import router from './router/router';
// import App from './App.vue';

// import '@fortawesome/fontawesome-free/css/all.css';

// createApp(App).use(router).mount('#app');


import { createApp } from 'vue';
import router from './router/router';
import App from './App.vue';

import '@fortawesome/fontawesome-free/css/all.css';
import Swal from 'sweetalert2';

const app = createApp(App);

// Register a global property for toast
app.config.globalProperties.$toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

app.use(router).mount('#app');
