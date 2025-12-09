<template>
    <div>
        <h1>Login</h1>
        <input v-model="email" placeholder="Email">
        <input type="password" v-model="password" placeholder="Password">
        <button @click="login">Login</button>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() { return { email: '', password: '' }; },
    methods: {
        async login() {
            try {
                const res = await axios.post('/api/login', { email: this.email, password: this.password });
                localStorage.setItem('token', res.data.token);
                this.$router.push('/products');
            } catch (err) { alert(err.response.data.message); }
        }
    }
}
</script>
