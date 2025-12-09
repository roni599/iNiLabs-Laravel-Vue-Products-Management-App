import { createApp } from 'vue';
import router from './router/router';
import App from './App.vue';
import '@fortawesome/fontawesome-free/css/all.css';
import Swal from 'sweetalert2';
import axios from 'axios';


// ✅ Create an Axios instance with baseURL and withCredentials
const axiosInstance = axios.create({
    baseURL: 'http://127.0.0.1:8000', // Laravel backend URL
    withCredentials: true, // Cookie পাঠানোর জন্য খুব গুরুত্বপূর্ণ (Sanctum)
});

// Make Axios instance available globally in Vue components
const app = createApp(App);
app.config.globalProperties.$axios = axiosInstance;

// ✅ Register global toast using SweetAlert2
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

// Mount the app with router
app.use(router).mount('#app');
