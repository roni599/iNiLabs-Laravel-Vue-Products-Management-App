<!-- resources/js/components/LoginComponent.vue -->
<template>
    <div class="login-wrapper">
        <div class="login-container">
            <!-- Left side with welcome message -->
            <div class="login-left">
                <h2>Welcome Back!</h2>
                <p>Sign in to access your account and explore our exclusive products.</p>

                <div class="features">
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure & encrypted login</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bolt"></i>
                        <span>Fast and seamless experience</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-headset"></i>
                        <span>24/7 customer support</span>
                    </div>
                </div>
            </div>

            <!-- Right side with login form -->
            <div class="login-right">
                <div class="logo">
                    <!-- <i class="fas fa-cube"></i> -->
                    <img :src="logo" alt="Logo" class="my-logo">
                    <span>iNiLabs</span>
                </div>

                <div class="login-header">
                    <h1>Sign In</h1>
                    <p>Enter your credentials to access your account</p>
                </div>

                <!-- Success message display -->
                <div class="success-message" v-if="successMessage">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ successMessage }}</span>
                </div>

                <!-- Error message display -->
                <div class="error-message" v-if="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ error }}</span>
                </div>

                <!-- Login form -->
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" placeholder="you@example.com" v-model="email"
                            @keyup.enter="handleLogin" :disabled="loading" :class="{ 'error-input': emailError }">
                    </div>
                    <small class="error-text" v-if="emailError">{{ emailError }}</small>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input :type="showPassword ? 'text' : 'password'" id="password"
                            placeholder="Enter your password" v-model="password" @keyup.enter="handleLogin"
                            :disabled="loading" :class="{ 'error-input': passwordError }">
                        <span class="password-toggle" @click="showPassword = !showPassword">
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </span>
                    </div>
                    <small class="error-text" v-if="passwordError">{{ passwordError }}</small>
                </div>

                <div class="options">
                    <div class="remember">
                        <input type="checkbox" id="remember" v-model="rememberMe">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password" @click.prevent="handleForgotPassword">Forgot password?</a>
                </div>

                <button class="login-button" @click="handleLogin" :disabled="loading" :class="{ 'loading': loading }">
                    <span v-if="loading" class="loading-spinner"></span>
                    <i v-else class="fas fa-sign-in-alt"></i>
                    <span>{{ loading ? 'Signing in...' : 'Sign In' }}</span>
                </button>

                <div class="divider">
                    <span>Or continue with</span>
                </div>

                <div class="social-login">
                    <div class="social-btn" @click="socialLogin('google')">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-btn" @click="socialLogin('facebook')">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-btn" @click="socialLogin('twitter')">
                        <i class="fab fa-twitter"></i>
                    </div>
                </div>

                <div class="signup-link">
                    Don't have an account? <a href="#" @click.prevent="goToSignup">Sign up now</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import logo from '@/assets/logo/inilabsn_logo.jpg';

// Reactive state
const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const loading = ref(false);
const error = ref('');
const successMessage = ref('');
const showPassword = ref(false);
const router = useRouter();

// Form errors
const formErrors = reactive({
    email: '',
    password: ''
});

// Computed properties
const emailError = computed(() => formErrors.email);
const passwordError = computed(() => formErrors.password);

// Form validation
const validateForm = () => {
    formErrors.email = '';
    formErrors.password = '';

    let isValid = true;

    // Email validation
    if (!email.value.trim()) {
        formErrors.email = 'Email is required';
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        formErrors.email = 'Please enter a valid email address';
        isValid = false;
    }

    // Password validation
    if (!password.value) {
        formErrors.password = 'Password is required';
        isValid = false;
    } else if (password.value.length < 6) {
        formErrors.password = 'Password must be at least 6 characters';
        isValid = false;
    }

    return isValid;
};

const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

// Login handler
const handleLogin = async () => {
    error.value = '';
    successMessage.value = '';

    if (!validateForm()) {
        return;
    }

    loading.value = true;

    try {
        // Make API call to Laravel backend
        const response = await axios.post('/api/login', {
            email: email.value,
            password: password.value
        });

        // Store token
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        if (rememberMe.value) {
            localStorage.setItem('rememberMe', 'true');
            localStorage.setItem('userEmail', email.value);
        } else {
            localStorage.removeItem('rememberMe');
            localStorage.removeItem('userEmail');
        }

        successMessage.value = 'Login successful! Redirecting...';

        // Redirect to products page after successful login
        setTimeout(() => {
            router.push('/products');
        }, 1500);

    } catch (err) {
        error.value = err.response?.data?.message || 'Login failed. Please try again.';
    } finally {
        loading.value = false;
    }
};

const handleForgotPassword = () => {
    if (!email.value.trim()) {
        error.value = 'Please enter your email address to reset password';
        return;
    }

    if (!isValidEmail(email.value)) {
        error.value = 'Please enter a valid email address';
        return;
    }

    successMessage.value = `Password reset instructions sent to ${email.value}`;
    error.value = '';

    setTimeout(() => {
        successMessage.value = '';
    }, 3000);
};

const socialLogin = (provider) => {
    successMessage.value = `Signing in with ${provider}...`;
    error.value = '';

    setTimeout(() => {
        successMessage.value = '';
        error.value = 'Social login would be implemented with OAuth in a real application';
    }, 1500);
};

const goToSignup = () => {
    router.push('/register');
};

// Lifecycle hook
onMounted(() => {
    if (localStorage.getItem('rememberMe') === 'true') {
        rememberMe.value = true;
        const savedEmail = localStorage.getItem('userEmail');
        if (savedEmail) {
            email.value = savedEmail;
        }
    }
});
</script>

<style scoped>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

html,
body {
    height: 100%;
    overflow: hidden;
}

.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    width: 100vw;
    padding: 20px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    overflow: hidden;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.login-container {
    display: flex;
    max-width: 900px;
    width: 100%;
    background-color: white;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.8s ease-out;
    height: auto;
    min-height: 600px;
    max-height: 700px;
    overflow: hidden;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.login-left {
    flex: 1;
    background: linear-gradient(rgba(110, 142, 251, 0.85), rgba(167, 119, 227, 0.85));
    color: white;
    padding: 40px 35px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.login-left h2 {
    font-size: 28px;
    margin-bottom: 15px;
    line-height: 1.2;
}

.login-left p {
    font-size: 15px;
    line-height: 1.5;
    opacity: 0.9;
    margin-bottom: 30px;
}

.my-logo {
    max-height: 30px;
}

.feature {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.feature i {
    background-color: rgba(255, 255, 255, 0.2);
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
    font-size: 14px;
}

.feature span {
    font-size: 14px;
    line-height: 1.4;
}

.login-right {
    flex: 1;
    padding: 35px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.logo {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    color: #6e8efb;
}

.logo i {
    font-size: 24px;
    margin-right: 10px;
}

.logo span {
    font-size: 22px;
    font-weight: 700;
}

.login-header {
    margin-bottom: 20px;
}

.login-header h1 {
    color: #333;
    font-size: 26px;
    margin-bottom: 8px;
    line-height: 1.2;
}

.login-header p {
    color: #666;
    margin-bottom: 10px;
    line-height: 1.4;
    font-size: 14px;
}

.input-group {
    margin-bottom: 18px;
    width: 100%;
}

.input-group label {
    display: block;
    margin-bottom: 6px;
    color: #555;
    font-weight: 500;
    font-size: 13px;
}

.input-wrapper {
    position: relative;
    width: 100%;
}

.input-wrapper input {
    width: 100%;
    padding: 14px 14px 14px 42px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.3s;
    height: 48px;
}

.input-wrapper input:focus {
    border-color: #6e8efb;
    outline: none;
    box-shadow: 0 0 0 3px rgba(110, 142, 251, 0.2);
}

.input-wrapper input.error-input {
    border-color: #d32f2f;
}

.input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 16px;
    z-index: 1;
}

.error-text {
    color: #d32f2f;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}

.options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    font-size: 13px;
    width: 100%;
}

.remember {
    display: flex;
    align-items: center;
}

.remember input {
    margin-right: 6px;
    transform: scale(0.9);
}

.forgot-password {
    color: #6e8efb;
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    font-size: 13px;
}

.forgot-password:hover {
    text-decoration: underline;
}

.login-button {
    background: linear-gradient(to right, #6e8efb, #a777e3);
    color: white;
    border: none;
    padding: 15px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    width: 100%;
    height: 48px;
}

.login-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 7px 15px rgba(110, 142, 251, 0.3);
}

.login-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.login-button i {
    margin-right: 8px;
    font-size: 14px;
}

.divider {
    text-align: center;
    position: relative;
    margin: 20px 0;
    color: #999;
    width: 100%;
}

.divider::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background-color: #eee;
    z-index: 1;
}

.divider span {
    background-color: white;
    padding: 0 12px;
    position: relative;
    z-index: 2;
    font-size: 13px;
}

.social-login {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 20px;
    width: 100%;
}

.social-btn {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
    background-color: white;
    color: #555;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s;
    flex-shrink: 0;
}

.social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
}

.signup-link {
    text-align: center;
    color: #666;
    font-size: 14px;
    width: 100%;
    margin-top: 5px;
}

.signup-link a {
    color: #6e8efb;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
}

.signup-link a:hover {
    text-decoration: underline;
}

.loading-spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
    margin-right: 8px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.error-message {
    background-color: #ffeaea;
    color: #d32f2f;
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    animation: slideDown 0.3s ease-out;
    width: 100%;
    font-size: 13px;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-message i {
    margin-right: 8px;
    font-size: 14px;
}

.success-message {
    background-color: #e8f5e9;
    color: #2e7d32;
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    animation: slideDown 0.3s ease-out;
    width: 100%;
    font-size: 13px;
}

.success-message i {
    margin-right: 8px;
    font-size: 14px;
}

.password-toggle {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    cursor: pointer;
    font-size: 16px;
    z-index: 1;
    background: transparent;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
}

/* Responsive design - NO SCROLLING VERSION */
@media (max-width: 768px) {
    .login-container {
        flex-direction: column;
        max-width: 420px;
        max-height: 650px;
        min-height: auto;
    }

    .login-left {
        padding: 25px 20px;
        text-align: center;
    }

    .login-right {
        padding: 25px 20px;
    }

    .feature {
        justify-content: center;
        text-align: left;
        margin-bottom: 12px;
    }

    .login-left h2 {
        font-size: 24px;
    }

    .login-left p {
        font-size: 14px;
    }

    .login-header h1 {
        font-size: 22px;
    }

    .login-header p {
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .login-wrapper {
        padding: 10px;
    }

    .options {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .social-login {
        gap: 8px;
    }

    .login-container {
        max-width: 350px;
        max-height: 620px;
    }
}

/* Prevent any scrolling on mobile */
@media (max-height: 700px) {
    .login-wrapper {
        padding: 10px;
    }

    .login-container {
        max-height: 580px;
        min-height: auto;
    }

    .login-right {
        padding: 25px 20px;
    }

    .login-left {
        padding: 25px 20px;
    }

    .feature {
        margin-bottom: 10px;
    }

    .input-group {
        margin-bottom: 15px;
    }
}

/* Extra small screens */
@media (max-height: 600px) {
    .login-container {
        max-height: 520px;
        transform: scale(0.95);
    }
}

/* Ensure no scrolling anywhere */
html,
body,
#app {
    overflow: hidden !important;
    height: 100% !important;
    width: 100% !important;
}
</style>