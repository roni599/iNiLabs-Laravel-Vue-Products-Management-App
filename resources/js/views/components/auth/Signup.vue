<template>
    <div class="register-wrapper">
        <div class="register-container">
            <div class="register-left">
                <h2>Join Our Community!</h2>
                <p>Create an account to unlock exclusive features and personalized experiences.</p>

                <div class="features">
                    <div class="feature">
                        <i class="fas fa-gift"></i>
                        <span>Exclusive member benefits</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Faster checkout process</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-heart"></i>
                        <span>Personalized recommendations</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-bell"></i>
                        <span>Early access to new products</span>
                    </div>
                </div>
            </div>

            <div class="register-right">
                <div class="logo" @click="goToLogin">
                    <img :src="logo" alt="Logo" class="my-logo">
                    <span>iNiLabs</span>
                </div>

                <div class="register-header">
                    <h1>Create Account</h1>
                    <p>Fill in your details to get started</p>
                </div>

                <div class="success-message" v-if="successMessage">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ successMessage }}</span>
                </div>

                <div class="error-message" v-if="error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ error }}</span>
                </div>

                <form @submit.prevent="handleRegister">
                    <div class="input-group">
                        <label for="name">Full Name</label>
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" id="name" placeholder="John Doe" v-model="name"
                                @keyup.enter="handleRegister" :disabled="loading" :class="{ 'error-input': nameError }">
                        </div>
                        <small class="error-text" v-if="nameError">{{ nameError }}</small>
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" placeholder="you@example.com" v-model="email"
                                @keyup.enter="handleRegister" :disabled="loading"
                                :class="{ 'error-input': emailError }">
                        </div>
                        <small class="error-text" v-if="emailError">{{ emailError }}</small>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input :type="showPassword ? 'text' : 'password'" id="password"
                                placeholder="Create a strong password" v-model="password" @keyup.enter="handleRegister"
                                :disabled="loading" :class="{ 'error-input': passwordError }">
                            <span class="password-toggle" @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </span>
                        </div>
                        <small class="error-text" v-if="passwordError">{{ passwordError }}</small>
                        <div class="password-strength" v-if="password">
                            <div class="strength-bar" :class="getPasswordStrengthClass"></div>
                            <small class="strength-text">{{ getPasswordStrengthText }}</small>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword"
                                placeholder="Confirm your password" v-model="confirmPassword"
                                @keyup.enter="handleRegister" :disabled="loading"
                                :class="{ 'error-input': confirmPasswordError }">
                            <span class="password-toggle" @click="showConfirmPassword = !showConfirmPassword">
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </span>
                        </div>
                        <small class="error-text" v-if="confirmPasswordError">{{ confirmPasswordError }}</small>
                    </div>

                    <div class="terms">
                        <input type="checkbox" id="terms" v-model="acceptTerms" :disabled="loading">
                        <label for="terms">
                            I agree to the <a href="#" @click.prevent="showTerms">Terms of Service</a> and
                            <a href="#" @click.prevent="showPrivacy">Privacy Policy</a>
                        </label>
                        <small class="error-text" v-if="termsError">{{ termsError }}</small>
                    </div>

                    <button class="register-button" type="submit" :disabled="loading" :class="{ 'loading': loading }">
                        <span v-if="loading" class="loading-spinner"></span>
                        <i v-else class="fas fa-user-plus"></i>
                        <span>{{ loading ? 'Creating Account...' : 'Sign Up' }}</span>
                    </button>
                </form>

                <div class="divider">
                    <span>Or sign up with</span>
                </div>

                <div class="social-register">
                    <div class="social-btn" @click="socialRegister('google')">
                        <i class="fab fa-google"></i>
                    </div>
                    <div class="social-btn" @click="socialRegister('facebook')">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div class="social-btn" @click="socialRegister('twitter')">
                        <i class="fab fa-twitter"></i>
                    </div>
                </div>

                <div class="login-link">
                    Already have an account? <a href="#" @click.prevent="goToLogin">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import logo from '@/assets/logo/inilabsn_logo.jpg';

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const acceptTerms = ref(false);
const loading = ref(false);
const error = ref('');
const successMessage = ref('');
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const router = useRouter();

const formErrors = ref({
    name: '',
    email: '',
    password: '',
    confirmPassword: '',
    terms: ''
});

const nameError = computed(() => formErrors.value.name);
const emailError = computed(() => formErrors.value.email);
const passwordError = computed(() => formErrors.value.password);
const confirmPasswordError = computed(() => formErrors.value.confirmPassword);
const termsError = computed(() => formErrors.value.terms);

const getPasswordStrengthClass = computed(() => {
    if (!password.value) return 'strength-0';
    const length = password.value.length;
    const hasUpper = /[A-Z]/.test(password.value);
    const hasLower = /[a-z]/.test(password.value);
    const hasNumber = /\d/.test(password.value);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password.value);

    let score = 0;
    if (length >= 8) score++;
    if (length >= 12) score++;
    if (hasUpper && hasLower) score++;
    if (hasNumber) score++;
    if (hasSpecial) score++;

    if (score <= 1) return 'strength-1';
    if (score <= 3) return 'strength-2';
    return 'strength-3';
});

const getPasswordStrengthText = computed(() => {
    const cls = getPasswordStrengthClass.value;
    switch (cls) {
        case 'strength-1': return 'Weak';
        case 'strength-2': return 'Good';
        case 'strength-3': return 'Strong';
        default: return '';
    }
});

const validateForm = () => {
    formErrors.value = {
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
        terms: ''
    };

    let isValid = true;
    if (!name.value.trim()) {
        formErrors.value.name = 'Full name is required';
        isValid = false;
    } else if (name.value.trim().length < 2) {
        formErrors.value.name = 'Name must be at least 2 characters';
        isValid = false;
    }
    if (!email.value.trim()) {
        formErrors.value.email = 'Email is required';
        isValid = false;
    } else if (!isValidEmail(email.value)) {
        formErrors.value.email = 'Please enter a valid email address';
        isValid = false;
    }
    if (!password.value) {
        formErrors.value.password = 'Password is required';
        isValid = false;
    } else if (password.value.length < 8) {
        formErrors.value.password = 'Password must be at least 8 characters';
        isValid = false;
    } else if (!/[A-Z]/.test(password.value) || !/[a-z]/.test(password.value)) {
        formErrors.value.password = 'Password must contain both uppercase and lowercase letters';
        isValid = false;
    } else if (!/\d/.test(password.value)) {
        formErrors.value.password = 'Password must contain at least one number';
        isValid = false;
    }
    if (!confirmPassword.value) {
        formErrors.value.confirmPassword = 'Please confirm your password';
        isValid = false;
    } else if (password.value !== confirmPassword.value) {
        formErrors.value.confirmPassword = 'Passwords do not match';
        isValid = false;
    }

    if (!acceptTerms.value) {
        formErrors.value.terms = 'You must accept the terms and conditions';
        isValid = false;
    }

    return isValid;
};

const isValidEmail = (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
};

const handleRegister = async () => {
    error.value = '';
    successMessage.value = '';

    if (!validateForm()) return;

    loading.value = true;

    try {
        const response = await axios.post('/api/register', {
            name: name.value.trim(),
            email: email.value.trim(),
            password: password.value,
            password_confirmation: confirmPassword.value
        });
        localStorage.setItem('token', response.data.token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        successMessage.value = 'Registration successful! Redirecting...';

        setTimeout(() => {
            router.push('/products');
        }, 1500);

    } catch (err) {

        error.value = err.response?.data?.message || 'Registration failed.';
    } finally {
        loading.value = false;
    }
};


const socialRegister = (provider) => {
    successMessage.value = `Signing up with ${provider}...`;
    error.value = '';

    setTimeout(() => {
        successMessage.value = '';
        error.value = 'Social registration would be implemented with OAuth in a real application';
    }, 1500);
};

const goToLogin = () => {
    router.push('/login');
};

const showTerms = () => {
    alert('Terms of Service would be displayed here in a real application.');
};

const showPrivacy = () => {
    alert('Privacy Policy would be displayed here in a real application.');
};

onMounted(() => {
    document.getElementById('name')?.focus();
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

.register-wrapper {
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

.register-container {
    display: flex;
    max-width: 950px;
    width: 100%;
    background-color: white;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.8s ease-out;
    height: auto;
    min-height: 680px;
    max-height: 650px;
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

.register-left {
    flex: 1;
    background: linear-gradient(rgba(110, 142, 251, 0.85), rgba(167, 119, 227, 0.85));
    color: white;
    padding: 40px 35px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.register-left h2 {
    font-size: 28px;
    margin-bottom: 15px;
    line-height: 1.2;
}

.register-left p {
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

.register-right {
    flex: 1;
    padding: 35px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.logo {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    color: #6e8efb;
    cursor: pointer;
}

.logo:hover {
    opacity: 0.8;
}

.logo i {
    font-size: 24px;
    margin-right: 10px;
}

.logo span {
    font-size: 22px;
    font-weight: 700;
}

.register-header {
    margin-bottom: 10px;
}

.register-header h1 {
    color: #333;
    font-size: 26px;
    margin-bottom: 8px;
    line-height: 1.2;
}

.register-header p {
    color: #666;
    margin-bottom: 10px;
    line-height: 1.4;
    font-size: 14px;
}

.input-group {
    margin-bottom: 5px;
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

.password-strength {
    margin-top: 8px;
}

.strength-bar {
    height: 4px;
    border-radius: 2px;
    margin-bottom: 4px;
    transition: all 0.3s;
}

.strength-0 {
    width: 0%;
    background-color: #e0e0e0;
}

.strength-1 {
    width: 33%;
    background-color: #ff5252;
}

.strength-2 {
    width: 66%;
    background-color: #ffb74d;
}

.strength-3 {
    width: 100%;
    background-color: #4caf50;
}

.strength-text {
    color: #666;
    font-size: 11px;
    font-weight: 500;
}

.terms {
    margin: 20px 0;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 13px;
}

.terms input {
    margin-top: 3px;
    transform: scale(0.9);
}

.terms label {
    color: #666;
    line-height: 1.4;
}

.terms a {
    color: #6e8efb;
    text-decoration: none;
    font-weight: 500;
}

.terms a:hover {
    text-decoration: underline;
}

.register-button {
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
    margin-bottom: 10px;
    width: 100%;
    height: 48px;
}

.register-button:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 7px 15px rgba(110, 142, 251, 0.3);
}

.register-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.register-button i {
    margin-right: 8px;
    font-size: 14px;
}

.divider {
    text-align: center;
    position: relative;
    margin: 5px 0;
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

.social-register {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-bottom: 10px;
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

.login-link {
    text-align: center;
    color: #666;
    font-size: 14px;
    width: 100%;
    margin-top: 5px;
}

.login-link a {
    color: #6e8efb;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
}

.login-link a:hover {
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

/* Responsive design */
@media (max-width: 768px) {
    .register-container {
        flex-direction: column;
        max-width: 450px;
        max-height: 700px;
        min-height: auto;
    }

    .register-left {
        padding: 25px 20px;
        text-align: center;
    }

    .register-right {
        padding: 25px 20px;
    }

    .feature {
        justify-content: center;
        text-align: left;
        margin-bottom: 12px;
    }

    .register-left h2 {
        font-size: 24px;
    }

    .register-left p {
        font-size: 14px;
    }

    .register-header h1 {
        font-size: 22px;
    }

    .register-header p {
        font-size: 13px;
    }

    .terms {
        font-size: 12px;
    }
}

@media (max-width: 480px) {
    .register-wrapper {
        padding: 10px;
    }

    .register-container {
        max-width: 380px;
        max-height: 650px;
    }

    .terms {
        flex-wrap: wrap;
    }
}

/* Prevent any scrolling */
html,
body,
#app {
    overflow: hidden !important;
    height: 100% !important;
    width: 100% !important;
}
</style>