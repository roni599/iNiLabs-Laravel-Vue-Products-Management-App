<template>
    <div>
        <h1>Products</h1>
        <input v-model="name" placeholder="Name">
        <input v-model="price" placeholder="Price">
        <button @click="createProduct">Add Product</button>

        <ul>
            <li v-for="product in products" :key="product.id">
                {{ product.name }} - {{ product.price }}
                <button @click="deleteProduct(product.id)">Delete</button>
            </li>
        </ul>
        <button @click="logout">Logout</button>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() { return { products: [], name: '', price: '' }; },
    async mounted() { await this.getProducts(); },
    methods: {
        async getProducts() {
            const token = localStorage.getItem('token');
            const res = await axios.get('/api/products', { headers: { Authorization: `Bearer ${token}` } });
            this.products = res.data;
        },
        async createProduct() {
            const token = localStorage.getItem('token');
            await axios.post('/api/products', { name: this.name, price: this.price }, { headers: { Authorization: `Bearer ${token}` } });
            this.name = ''; this.price = ''; this.getProducts();
        },
        async deleteProduct(id) {
            const token = localStorage.getItem('token');
            await axios.delete(`/api/products/${id}`, { headers: { Authorization: `Bearer ${token}` } });
            this.getProducts();
        },
        async logout() {
            const token = localStorage.getItem('token');
            await axios.post('/api/logout', {}, { headers: { Authorization: `Bearer ${token}` } });
            localStorage.removeItem('token');
            this.$router.push('/');
        }
    }
}
</script>
