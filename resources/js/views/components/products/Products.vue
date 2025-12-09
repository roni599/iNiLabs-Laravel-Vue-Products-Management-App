<template>
    <div class="products-container">
        <div class="products-header">
            <h1><i class="fas fa-boxes"></i> Product Management</h1>
            <div class="header-actions">
                <button class="add-product-btn" @click="openAddModal">
                    <i class="fas fa-plus"></i> Add Product
                </button>
                <button class="logout-btn" @click="logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
        <div class="products-table-container">
            <div class="table-header">
                <h2><i class="fas fa-list"></i> Products List</h2>
                <div class="table-actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" v-model="searchQuery" placeholder="Search products...">
                    </div>
                    <div class="status-filter">
                        <select v-model="statusFilter" @change="filterProducts">
                            <option value="all">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(product, index) in filteredProducts" :key="product.id">
                            <td class="sl-column">{{ index + 1 }}</td>
                            <td class="product-info">
                                <div class="product-image-container">
                                    <div class="product-image" v-if="product.image">
                                        <img :src="getImageUrl(product.image)" :alt="product.name"
                                            @error="handleImageError">
                                    </div>
                                    <div class="no-image" v-else>
                                        <i class="fas fa-image"></i>
                                    </div>
                                </div>
                                <div class="product-details">
                                    <strong>{{ product.name }}</strong>
                                </div>
                            </td>
                            <td class="description-column">
                                <div class="description-text">
                                    {{ product.description || 'No description' }}
                                </div>
                            </td>
                            <td class="price-column">
                                ${{ parseFloat(product.price).toFixed(2) }}
                            </td>
                            <td class="status-column">
                                <span :class="['status-badge', product.status ? 'active' : 'inactive']">
                                    <i :class="product.status ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                    {{ product.status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="actions-column">
                                <button class="action-btn view-btn" @click="viewProduct(product.id)" title="View">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-btn edit-btn" @click="editProduct(product)" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-btn delete-btn" @click="confirmDelete(product)" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <tr v-if="filteredProducts.length === 0">
                            <td colspan="6" class="no-data">
                                <i class="fas fa-box-open"></i>
                                No products found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="showAddModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-plus-circle"></i> Add New Product</h3>
                    <button class="close-btn" @click="closeAddModal">&times;</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="createProduct" class="modal-form" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="name">Product Name *</label>
                                    <input type="text" id="name" v-model="newProduct.name"
                                        placeholder="Enter product name">
                                    <span class="error" v-if="errors.name">{{ errors.name }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="price">Price *</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                        <input type="number" id="price" v-model="newProduct.price" placeholder="0.00"
                                            step="0.01" min="0">
                                    </div>
                                    <span class="error" v-if="errors.price">{{ errors.price }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" v-model="newProduct.status" class="status-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-column">
                                <div class="form-group">
                                    <label for="image">Product Image</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" id="image" ref="imageInput" @change="handleImageUpload"
                                            accept="image/*" hidden>
                                        <div class="file-upload-area" @click="triggerFileInput"
                                            :class="{ 'has-image': newProduct.imagePreview }">
                                            <div v-if="newProduct.imagePreview" class="image-preview">
                                                <img :src="newProduct.imagePreview" alt="Preview">
                                                <button type="button" class="remove-image" @click.stop="removeImage">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div v-else class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Click to upload image</p>
                                                <small>JPG, PNG or GIF (Max: 2MB)</small>
                                            </div>
                                        </div>
                                        <span class="error" v-if="errors.image">{{ errors.image }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label for="description">Description</label>
                            <textarea id="description" v-model="newProduct.description"
                                placeholder="Enter product description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="closeAddModal">Cancel</button>
                    <button class="btn btn-primary" @click="createProduct" :disabled="loading">
                        {{ loading ? 'Uploading...' : 'Add Product' }}
                    </button>
                </div>
            </div>
        </div>
        <div v-if="showEditModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-edit"></i> Edit Product</h3>
                    <button class="close-btn" @click="closeEditModal">&times;</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="updateProduct" class="modal-form" enctype="multipart/form-data">
                        <div class="form-row">
                            <div class="form-column">
                                <div class="form-group">
                                    <label for="edit-name">Product Name *</label>
                                    <input type="text" id="edit-name" v-model="editProductData.name">
                                    <span class="error" v-if="editErrors.name">{{ editErrors.name }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="edit-price">Price *</label>
                                    <div class="input-with-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                        <input type="number" id="edit-price" v-model="editProductData.price" step="0.01"
                                            min="0">
                                    </div>
                                    <span class="error" v-if="editErrors.price">{{ editErrors.price }}</span>
                                </div>

                                <div class="form-group">
                                    <label for="edit-status">Status</label>
                                    <select id="edit-status" v-model="editProductData.status" class="status-select">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-column">
                                <div class="form-group">
                                    <label for="edit-image">Product Image</label>
                                    <div class="file-upload-wrapper">
                                        <input type="file" id="edit-image" ref="editImageInput"
                                            @change="handleEditImageUpload" accept="image/*" hidden>
                                        <div class="file-upload-area" @click="triggerEditFileInput"
                                            :class="{ 'has-image': editProductData.imagePreview || editProductData.image }">
                                            <div v-if="editProductData.imagePreview || editProductData.image"
                                                class="image-preview">
                                                <img :src="editProductData.imagePreview || getImageUrl(editProductData.image)"
                                                    alt="Preview">
                                                <button type="button" class="remove-image"
                                                    @click.stop="removeEditImage">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                            <div v-else class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p>Click to upload image</p>
                                                <small>JPG, PNG or GIF (Max: 2MB)</small>
                                            </div>
                                        </div>
                                        <div class="image-options"
                                            v-if="editProductData.image && !editProductData.imagePreview">
                                            <p class="current-image-info">Current image will be kept if no new image is
                                                selected</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label for="edit-description">Description</label>
                            <textarea id="edit-description" v-model="editProductData.description" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="closeEditModal">Cancel</button>
                    <button class="btn btn-primary" @click="updateProduct" :disabled="editLoading">
                        {{ editLoading ? 'Updating...' : 'Update Product' }}
                    </button>
                </div>
            </div>
        </div>
        <div v-if="showDeleteModal" class="modal-overlay">
            <div class="modal-content delete-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ productToDelete?.name }}</strong>?</p>
                    <p class="warning-text">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="cancelDelete">Cancel</button>
                    <button class="btn btn-danger" @click="deleteProduct" :disabled="deleteLoading">
                        {{ deleteLoading ? 'Deleting...' : 'Delete Product' }}
                    </button>
                </div>
            </div>
        </div>
        <!-- View Product Modal -->
        <div v-if="showViewModal" class="modal-overlay">
            <div class="modal-content view-modal">
                <div class="modal-header">
                    <h3><i class="fas fa-eye"></i> Product Details</h3>
                    <button class="close-btn" @click="closeViewModal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="product-view-container">
                        <div class="product-view-row">
                            <div class="product-image-view">
                                <div v-if="viewProductData.image" class="view-image-container">
                                    <img :src="getImageUrl(viewProductData.image)" :alt="viewProductData.name"
                                        @error="handleImageError">
                                </div>
                                <div v-else class="no-image-view">
                                    <i class="fas fa-image"></i>
                                    <span>No Image</span>
                                </div>
                            </div>
                            <div class="product-details-view">
                                <div class="detail-item">
                                    <strong>Product ID:</strong>
                                    <span>#{{ viewProductData.id }}</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Product Name:</strong>
                                    <span>{{ viewProductData.name }}</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Price:</strong>
                                    <span class="price-view">Tk.{{ parseFloat(viewProductData.price).toFixed(2)
                                        }}</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Status:</strong>
                                    <span :class="['status-view', viewProductData.status ? 'active' : 'inactive']">
                                        <i
                                            :class="viewProductData.status ? 'fas fa-check-circle' : 'fas fa-times-circle'"></i>
                                        {{ viewProductData.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="product-view-row">
                            <div class="description-view">
                                <strong>Description:</strong>
                                <div class="description-content">
                                    {{ viewProductData.description || 'No description available' }}
                                </div>
                            </div>
                        </div>
                        <div v-if="viewProductData.features" class="product-view-row">
                            <div class="features-view">
                                <strong>Features:</strong>
                                <div class="features-content">
                                    {{ viewProductData.features }}
                                </div>
                            </div>
                        </div>
                        <div class="product-view-row">
                            <div class="timestamps-view">
                                <div class="timestamp-item">
                                    <strong>Created:</strong>
                                    <span>{{ formatDate(viewProductData.created_at) }}</span>
                                </div>
                                <div class="timestamp-item">
                                    <strong>Last Updated:</strong>
                                    <span>{{ formatDate(viewProductData.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="closeViewModal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'ProductsPage',
    data() {
        return {
            products: [],
            newProduct: {
                name: '',
                price: '',
                image: null,
                imagePreview: null,
                description: '',
                status: '1'
            },
            editProductData: {
                id: null,
                name: '',
                price: '',
                image: '',
                imageFile: null,
                imagePreview: null,
                description: '',
                status: '1'
            },
            viewProductData: {
                id: null,
                name: '',
                sku: '',
                price: '',
                cost_price: '',
                quantity: 0,
                image: '',
                description: '',
                features: '',
                status: 1,
                category: null,
                created_at: null,
                updated_at: null
            },
            errors: {},
            editErrors: {},
            loading: false,
            editLoading: false,
            deleteLoading: false,
            showAddModal: false,
            showViewModal: false,
            viewError: '',
            showEditModal: false,
            showDeleteModal: false,
            productToDelete: null,
            searchQuery: '',
            statusFilter: 'all'
        };
    },
    computed: {
        filteredProducts() {
            let filtered = this.products;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(product =>
                    product.name.toLowerCase().includes(query) ||
                    product.description?.toLowerCase().includes(query)
                );
            }

            if (this.statusFilter !== 'all') {
                filtered = filtered.filter(product =>
                    product.status.toString() === this.statusFilter
                );
            }

            return filtered;
        }
    },
    async mounted() {
        await this.getProducts();
    },
    methods: {
        getImageUrl(imagePath) {
            if (!imagePath) return '';
            if (imagePath.startsWith('http')) return imagePath;
            return `/${imagePath}`;
        },

        async getProducts() {
            try {
                const token = localStorage.getItem('token');
                const res = await axios.get('/api/products', {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                this.products = res.data;
            } catch (error) {
                console.error('Error fetching products:', error);
                alert('Failed to load products');
            }
        },

        validateProduct(product) {
            const errors = {};
            if (!product.name.trim()) {
                errors.name = 'Product name is required';
            }
            if (!product.price || parseFloat(product.price) <= 0) {
                errors.price = 'Valid price is required';
            }
            return errors;
        },

        triggerFileInput() {
            this.$refs.imageInput.click();
        },

        triggerEditFileInput() {
            this.$refs.editImageInput.click();
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                this.errors.image = 'Image size should be less than 2MB';
                return;
            }

            if (!file.type.match('image.*')) {
                this.errors.image = 'Please select an image file';
                return;
            }

            this.errors.image = '';
            this.newProduct.image = file;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.newProduct.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        handleEditImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('Image size should be less than 2MB');
                return;
            }

            if (!file.type.match('image.*')) {
                alert('Please select an image file');
                return;
            }

            this.editProductData.imageFile = file;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.editProductData.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeImage() {
            this.newProduct.image = null;
            this.newProduct.imagePreview = null;
            this.$refs.imageInput.value = '';
        },

        removeEditImage() {
            this.editProductData.imageFile = null;
            this.editProductData.imagePreview = null;
            this.editProductData.image = '';
            this.$refs.editImageInput.value = '';
        },

        openAddModal() {
            this.errors = {};
            this.newProduct = {
                name: '',
                price: '',
                image: null,
                imagePreview: null,
                description: '',
                status: '1'
            };
            this.showAddModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeAddModal() {
            this.showAddModal = false;
            this.errors = {};
            document.body.style.overflow = '';
        },

        async createProduct() {
            this.errors = {};
            const validationErrors = this.validateProduct(this.newProduct);

            if (Object.keys(validationErrors).length > 0) {
                this.errors = validationErrors;
                return;
            }

            this.loading = true;
            try {
                const token = localStorage.getItem('token');

                const formData = new FormData();
                formData.append('name', this.newProduct.name);
                formData.append('price', parseFloat(this.newProduct.price));
                formData.append('description', this.newProduct.description);
                formData.append('status', parseInt(this.newProduct.status));

                if (this.newProduct.image) {
                    formData.append('image', this.newProduct.image);
                }

                await axios.post('/api/products', formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.closeAddModal();
                await this.getProducts();
                this.$toast.fire({
                    icon: 'success',
                    title: 'Product added successfully!',
                    background: '#28a745',
                    color: '#ffffff'
                });

            } catch (error) {
                console.error('Error creating product:', error);

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    for (const field in errors) {
                        this.errors[field] = errors[field][0];
                    }
                } else {
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Failed to create product!',
                        background: '#dc3545',
                        color: '#ffffff'
                    });
                }
            } finally {
                this.loading = false;
            }
        }
        ,
        editProduct(product) {
            this.editProductData = {
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                imageFile: null,
                imagePreview: null,
                description: product.description,
                status: product.status.toString()
            };
            this.editErrors = {};
            this.showEditModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeEditModal() {
            this.showEditModal = false;
            this.editErrors = {};
            document.body.style.overflow = '';
        },
        async viewProduct(productId) {
            this.viewError = '';
            this.showViewModal = true;

            try {
                const token = localStorage.getItem('token');
                const response = await axios.get(`/api/products/${productId}`, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    }
                });
                this.viewProductData = response.data || response.data;
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error fetching product details:', error);

                if (error.response?.status === 404) {
                    this.viewError = 'Product not found. It may have been deleted.';
                } else if (error.response?.status === 401) {
                    this.viewError = 'Unauthorized. Please login again.';
                    this.logout();
                } else if (error.response?.data?.message) {
                    this.viewError = error.response.data.message;
                } else {
                    this.viewError = 'Failed to load product details. Please try again.';
                }
            } finally {

            }
        },
        closeViewModal() {
            this.showViewModal = false;
            this.viewError = '';
            this.viewProductData = {
                id: null,
                name: '',
                sku: '',
                price: '',
                cost_price: '',
                quantity: 0,
                image: '',
                description: '',
                features: '',
                status: 1,
                category: null,
                created_at: null,
                updated_at: null
            };
            document.body.style.overflow = '';
        },
        async updateProduct() {
            this.editErrors = {};
            const validationErrors = this.validateProduct(this.editProductData);

            if (Object.keys(validationErrors).length > 0) {
                this.editErrors = validationErrors;
                return;
            }

            this.editLoading = true;
            try {
                const token = localStorage.getItem('token');

                const formData = new FormData();
                formData.append('name', this.editProductData.name);
                formData.append('price', parseFloat(this.editProductData.price));
                formData.append('description', this.editProductData.description);
                formData.append('status', parseInt(this.editProductData.status));
                formData.append('_method', 'PUT');

                if (this.editProductData.imageFile) {
                    formData.append('image', this.editProductData.imageFile);
                }

                await axios.post(`/api/products/${this.editProductData.id}`, formData, {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.closeEditModal();
                await this.getProducts();
                this.$toast.fire({
                    icon: 'success',
                    title: 'Product updated successfully!',
                    background: '#28a745',
                    color: '#ffffff'
                });

            } catch (error) {
                console.error('Error updating product:', error);

                if (error.response?.data?.errors) {
                    const errors = error.response.data.errors;
                    for (const field in errors) {
                        this.editErrors[field] = errors[field][0];
                    }
                } else {
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Failed to update product!',
                        background: '#dc3545',
                        color: '#ffffff'
                    });
                }
            } finally {
                this.editLoading = false;
            }
        },


        confirmDelete(product) {
            this.productToDelete = product;
            this.showDeleteModal = true;
            document.body.style.overflow = 'hidden';
        },

        cancelDelete() {
            this.productToDelete = null;
            this.showDeleteModal = false;
            document.body.style.overflow = '';
        },

        async deleteProduct() {
            if (!this.productToDelete) return;

            this.deleteLoading = true;
            try {
                const token = localStorage.getItem('token');
                await axios.delete(`/api/products/${this.productToDelete.id}`, {
                    headers: { Authorization: `Bearer ${token}` }
                });

                this.showDeleteModal = false;
                this.productToDelete = null;
                await this.getProducts();

                this.$toast.fire({
                    icon: 'success',
                    title: 'Product deleted successfully!',
                    background: '#28a745',
                    color: '#ffffff'
                });

            } catch (error) {
                console.error('Error deleting product:', error);
                this.$toast.fire({
                    icon: 'error',
                    title: 'Failed to delete product!',
                    background: '#dc3545',
                    color: '#ffffff'
                });

            } finally {
                this.deleteLoading = false;
                document.body.style.overflow = '';
            }
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        filterProducts() {
        },

        handleImageError(event) {
            event.target.style.display = 'none';
            event.target.parentElement.innerHTML = '<i class="fas fa-image"></i>';
        },

        async logout() {
            try {
                const token = localStorage.getItem('token');
                await axios.post('/api/logout', {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });
            } catch (error) {
                console.error('Logout error:', error);
            }

            localStorage.removeItem('token');
            localStorage.removeItem('user');
            this.$router.push('/');
        }
    }
};
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}


/* Add to existing button styles */
.view-btn {
    background-color: #6c757d;
    color: white;
}

.view-btn:hover {
    background-color: #5a6268;
    transform: translateY(-2px);
}

/* Add to existing modal styles or create new ones */
.view-modal {
    max-width: 600px;
}

.product-view-container {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.product-view-row {
    display: flex;
    gap: 25px;
}

.product-image-view {
    flex: 0 0 200px;
}

.view-image-container {
    width: 200px;
    height: 200px;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
}

.view-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image-view {
    width: 200px;
    height: 200px;
    border-radius: 8px;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
    color: #adb5bd;
    gap: 10px;
}

.no-image-view i {
    font-size: 40px;
}

.no-image-view span {
    font-size: 14px;
}

.product-details-view {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-item {
    display: flex;
    align-items: center;
    padding-bottom: 8px;
    border-bottom: 1px solid #f0f0f0;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-item strong {
    min-width: 120px;
    color: #495057;
    font-size: 14px;
}

.detail-item span {
    color: #2c3e50;
    font-size: 14px;
    flex: 1;
}

.price-view {
    color: #28a745 !important;
    font-weight: 600;
}

.status-view {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.status-view.active {
    background-color: #d4edda;
    color: #155724;
}

.status-view.inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.description-view {
    width: 100%;
}

.description-view strong {
    display: block;
    margin-bottom: 10px;
    color: #495057;
    font-size: 14px;
}

.description-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    line-height: 1.6;
    color: #495057;
    font-size: 14px;
    max-height: 200px;
    overflow-y: auto;
}

/* Responsive styles for view modal */
@media (max-width: 768px) {
    .product-view-row {
        flex-direction: column;
        gap: 20px;
    }

    .product-image-view {
        flex: 0 0 auto;
        align-self: center;
    }

    .view-image-container,
    .no-image-view {
        width: 150px;
        height: 150px;
    }

    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    .detail-item strong {
        min-width: auto;
    }
}

@media (max-width: 480px) {

    .view-image-container,
    .no-image-view {
        width: 120px;
        height: 120px;
    }

    .no-image-view i {
        font-size: 30px;
    }
}

html,
body {
    overflow: hidden;
    height: 100%;
    width: 100%;
}

.products-container {
    padding: 20px;
    background-color: #f8f9fa;
    min-height: 100vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e0e0e0;
    flex-shrink: 0;
}

.products-header h1 {
    color: #2c3e50;
    font-size: 28px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.header-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.add-product-btn {
    background: linear-gradient(to right, #28a745, #20c997);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    font-size: 15px;
}

.add-product-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
}

.logout-btn {
    background: linear-gradient(to right, #dc3545, #c82333);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    font-size: 15px;
}

.logout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.products-table-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-shrink: 0;
}

.table-header h2 {
    color: #2c3e50;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.table-actions {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-box {
    position: relative;
    display: flex;
    align-items: center;
}

.search-box i {
    position: absolute;
    left: 12px;
    color: #6c757d;
}

.search-box input {
    padding: 10px 10px 10px 35px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    width: 250px;
    font-size: 14px;
    transition: all 0.3s;
}

.search-box input:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.status-filter select {
    padding: 10px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    font-size: 14px;
    min-width: 150px;
}

.status-filter select:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.table-responsive {
    flex: 1;
    overflow: auto;
    -webkit-overflow-scrolling: touch;
}

.products-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: auto;
}

.products-table thead {
    background: linear-gradient(to right, #3498db, #2980b9);
    color: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.products-table th {
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    font-size: 15px;
    border: none;
    white-space: nowrap;
}

.products-table tbody tr {
    border-bottom: 1px solid #e0e0e0;
    transition: all 0.3s;
}

.products-table tbody tr:hover {
    background-color: #f8f9fa;
}

.products-table td {
    padding: 16px 12px;
    vertical-align: middle;
    border: none;
}

.sl-column {
    text-align: center;
    font-weight: 600;
    color: #495057;
    width: 60px;
    min-width: 60px;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 200px;
}

.product-image-container {
    flex-shrink: 0;
}

.product-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    overflow: hidden;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 20px;
}

.product-details {
    flex: 1;
    min-width: 0;
}

.product-details strong {
    color: #2c3e50;
    font-size: 15px;
    display: block;
    margin-bottom: 4px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.description-column {
    max-width: 300px;
    min-width: 200px;
}

.description-text {
    color: #495057;
    line-height: 1.5;
    word-break: break-word;
    max-height: 60px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
}

.price-column {
    font-weight: 600;
    color: #28a745;
    font-size: 15px;
    min-width: 100px;
    white-space: nowrap;
}

.status-column {
    min-width: 120px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.status-badge.active {
    background-color: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.actions-column {
    min-width: 100px;
    white-space: nowrap;
}

.action-btn {
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.3s;
    margin-right: 6px;
    min-width: 36px;
}

.edit-btn {
    background-color: #17a2b8;
    color: white;
}

.edit-btn:hover {
    background-color: #138496;
    transform: translateY(-2px);
}

.delete-btn {
    background-color: #dc3545;
    color: white;
}

.delete-btn:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.no-data {
    text-align: center;
    padding: 40px !important;
    color: #6c757d;
    font-size: 16px;
}

.no-data i {
    font-size: 24px;
    margin-bottom: 10px;
    display: block;
    color: #adb5bd;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
    backdrop-filter: blur(5px);
    padding: 15px;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 800px;
    max-height: 85vh;
    overflow: hidden;
    animation: modalSlideIn 0.3s ease-out;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-header {
    padding: 20px 25px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(to right, #f8f9fa, #e9ecef);
    border-radius: 12px 12px 0 0;
    flex-shrink: 0;
}

.modal-header h3 {
    color: #2c3e50;
    font-size: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    color: #6c757d;
    cursor: pointer;
    line-height: 1;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s;
    flex-shrink: 0;
}

.close-btn:hover {
    background-color: #f8f9fa;
    color: #dc3545;
}

.modal-body {
    padding: 25px;
    flex: 1;
    overflow-y: auto;
}

.modal-form {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-row {
    display: flex;
    gap: 25px;
    margin-bottom: 0;
}

.form-column {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-width: 0;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s;
    width: 100%;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #3498db;
    outline: none;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.form-group.full-width {
    width: 100%;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
    font-family: inherit;
}

.input-with-icon {
    position: relative;
    width: 100%;
}

.input-with-icon i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
}

.input-with-icon input {
    padding-left: 35px;
    width: 100%;
}

.status-select {
    cursor: pointer;
    background-color: white;
}

.error {
    color: #dc3545;
    font-size: 13px;
    margin-top: 5px;
    display: block;
    min-height: 18px;
}

/* File Upload Styles */
.file-upload-wrapper {
    margin-top: 5px;
}

.file-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background-color: #f8f9fa;
    position: relative;
    min-height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.file-upload-area:hover {
    border-color: #3498db;
    background-color: #e8f4fd;
}

.file-upload-area.has-image {
    border-style: solid;
    padding: 15px;
    min-height: 160px;
}

.upload-placeholder {
    color: #6c757d;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.upload-placeholder i {
    font-size: 40px;
    color: #adb5bd;
}

.upload-placeholder p {
    margin: 0;
    font-weight: 500;
    color: #495057;
    font-size: 14px;
}

.upload-placeholder small {
    color: #adb5bd;
    font-size: 11px;
}

.image-preview {
    position: relative;
    width: 100%;
    height: 130px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.remove-image {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    transition: all 0.3s;
    z-index: 10;
}

.remove-image:hover {
    background: #c82333;
    transform: scale(1.1);
}

.image-options {
    margin-top: 10px;
    text-align: center;
}

.current-image-info {
    font-size: 11px;
    color: #6c757d;
    font-style: italic;
    margin-top: 8px;
}

.modal-footer {
    padding: 20px 25px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    background-color: #f8f9fa;
    border-radius: 0 0 12px 12px;
    flex-shrink: 0;
}

.btn {
    padding: 10px 22px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
    min-width: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}

.btn-primary {
    background: linear-gradient(to right, #3498db, #2980b9);
    color: white;
}

.btn-primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.btn-danger {
    background: linear-gradient(to right, #dc3545, #c82333);
    color: white;
}

.btn-danger:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

.btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.delete-modal .modal-body {
    text-align: center;
    padding: 30px 25px;
}

.delete-modal p {
    margin-bottom: 15px;
    color: #495057;
    font-size: 15px;
    line-height: 1.5;
}

.delete-modal .warning-text {
    color: #dc3545;
    font-weight: 600;
    font-size: 13px;
}

/* Responsive Design */
@media (max-width: 1200px) {

    .products-table th,
    .products-table td {
        padding: 14px 10px;
    }

    .product-image {
        width: 45px;
        height: 45px;
    }

    .no-image {
        width: 45px;
        height: 45px;
        font-size: 18px;
    }
}

@media (max-width: 992px) {
    .form-row {
        flex-direction: column;
        gap: 20px;
    }

    .modal-content {
        max-width: 90%;
        max-height: 85vh;
    }

    .file-upload-area {
        min-height: 150px;
    }

    .file-upload-area.has-image {
        min-height: 150px;
    }

    .image-preview {
        height: 120px;
    }
}

@media (max-width: 768px) {
    .products-container {
        padding: 15px;
        height: 100vh;
        overflow: hidden;
    }

    .products-header {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
        margin-bottom: 15px;
    }

    .header-actions {
        flex-direction: column;
        width: 100%;
    }

    .add-product-btn,
    .logout-btn {
        width: 100%;
        justify-content: center;
        padding: 10px 20px;
    }

    .table-header {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }

    .table-actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
    }

    .search-box input {
        width: 100%;
    }

    .status-filter select {
        width: 100%;
    }

    .products-table-container {
        padding: 15px;
        flex: 1;
        min-height: 0;
    }

    .products-table th,
    .products-table td {
        padding: 12px 8px;
        font-size: 14px;
    }

    .product-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .product-image {
        width: 40px;
        height: 40px;
    }

    .no-image {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .product-details strong {
        font-size: 14px;
    }

    .description-column {
        min-width: 150px;
    }

    .description-text {
        font-size: 13px;
    }

    .price-column {
        font-size: 14px;
        min-width: 80px;
    }

    .status-badge {
        font-size: 11px;
        padding: 4px 8px;
    }

    .actions-column {
        display: flex;
        gap: 5px;
        min-width: 90px;
    }

    .action-btn {
        margin-right: 0;
        padding: 5px 8px;
        min-width: 32px;
        font-size: 12px;
    }

    .modal-content {
        width: 95%;
        margin: 0;
        max-height: 90vh;
    }

    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 20px;
    }

    .modal-header h3 {
        font-size: 16px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        padding: 10px;
        font-size: 14px;
    }

    .btn {
        padding: 10px 20px;
        font-size: 14px;
        min-width: 100px;
    }

    .file-upload-area {
        min-height: 140px;
        padding: 15px;
    }

    .file-upload-area.has-image {
        min-height: 140px;
    }

    .image-preview {
        height: 110px;
    }

    .upload-placeholder i {
        font-size: 32px;
    }
}

@media (max-width: 576px) {
    .products-header h1 {
        font-size: 22px;
    }

    .table-header h2 {
        font-size: 18px;
    }

    .products-table {
        min-width: 600px;
    }

    .modal-footer {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

    .file-upload-area {
        min-height: 130px;
    }

    .file-upload-area.has-image {
        min-height: 130px;
    }

    .image-preview {
        height: 100px;
    }

    .upload-placeholder i {
        font-size: 28px;
    }

    .upload-placeholder p {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .products-header h1 {
        font-size: 20px;
    }

    .products-table th,
    .products-table td {
        padding: 10px 6px;
        font-size: 13px;
    }

    .product-image {
        width: 35px;
        height: 35px;
    }

    .no-image {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }

    .sl-column {
        width: 40px;
        min-width: 40px;
    }

    .product-details strong {
        font-size: 13px;
    }

    .price-column {
        font-size: 13px;
        min-width: 70px;
    }

    .status-badge {
        font-size: 10px;
        padding: 3px 6px;
    }

    .action-btn {
        padding: 4px 6px;
        font-size: 11px;
        min-width: 28px;
    }

    .modal-body {
        padding: 20px 15px;
    }
}
</style>