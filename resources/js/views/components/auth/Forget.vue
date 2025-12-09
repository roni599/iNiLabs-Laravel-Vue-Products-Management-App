<template>
    <div class="forgot-wrapper">
        <div class="forgot-container">
            <div class="forgot-left">
                <h2>Reset Your Password</h2>
                <p>We'll help you get back into your account quickly and securely.</p>

                <div class="features">
                    <div class="feature">
                        <i class="fas fa-shield-alt"></i>
                        <span>Secure password reset</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-clock"></i>
                        <span>One-time reset process</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-user-check"></i>
                        <span>Immediate account access</span>
                    </div>
                </div>
            </div>

            <div class="forgot-right">
                <div class="logo" @click="goToLogin">
                    <img :src="logo" alt="Logo" class="my-logo">
                    <span>iNiLabs</span>
                </div>

                <!-- Single Form with All Fields -->
                <div v-if="!success" class="reset-form">
                    <div class="forgot-header">
                        <h1>Reset Your Password</h1>
                        <p>Enter your email and new password to reset</p>
                    </div>

                    <div class="success-message" v-if="successMessage">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ successMessage }}</span>
                    </div>

                    <div class="error-message" v-if="error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ error }}</span>
                    </div>

                    <!-- Email Field -->
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrapper">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" id="email" placeholder="you@example.com" v-model="email"
                                @keyup.enter="resetPassword" :disabled="loading" :class="{ 'error-input': emailError }">
                        </div>
                        <small class="error-text" v-if="emailError">{{ emailError }}</small>
                    </div>

                    <!-- New Password Field -->
                    <div class="input-group">
                        <label for="newPassword">New Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input :type="showNewPassword ? 'text' : 'password'" id="newPassword"
                                placeholder="Enter new password" v-model="newPassword" @keyup.enter="resetPassword"
                                :disabled="loading" :class="{ 'error-input': newPasswordError }">
                            <span class="password-toggle" @click="showNewPassword = !showNewPassword">
                                <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </span>
                        </div>
                        <small class="error-text" v-if="newPasswordError">{{ newPasswordError }}</small>
                        <div class="password-strength" v-if="newPassword">
                            <div class="strength-bar" :class="getPasswordStrengthClass"></div>
                            <small class="strength-text">{{ getPasswordStrengthText }}</small>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="input-group">
                        <label for="confirmPassword">Confirm New Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input :type="showConfirmPassword ? 'text' : 'password'" id="confirmPassword"
                                placeholder="Confirm new password" v-model="confirmPassword"
                                @keyup.enter="resetPassword" :disabled="loading"
                                :class="{ 'error-input': confirmPasswordError }">
                            <span class="password-toggle" @click="showConfirmPassword = !showConfirmPassword">
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </span>
                        </div>
                        <small class="error-text" v-if="confirmPasswordError">{{ confirmPasswordError }}</small>
                    </div>

                    <div class="password-requirements">
                        <p><strong>Password Requirements:</strong></p>
                        <ul>
                            <li :class="{ 'valid': newPassword.length >= 8 }">
                                <i :class="newPassword.length >= 8 ? 'fas fa-check-circle' : 'fas fa-circle'"></i>
                                Minimum 8 characters
                            </li>
                            <li :class="{ 'valid': /[A-Z]/.test(newPassword) && /[a-z]/.test(newPassword) }">
                                <i
                                    :class="/[A-Z]/.test(newPassword) && /[a-z]/.test(newPassword) ? 'fas fa-check-circle' : 'fas fa-circle'"></i>
                                Uppercase & lowercase letters
                            </li>
                            <li :class="{ 'valid': /\d/.test(newPassword) }">
                                <i :class="/\d/.test(newPassword) ? 'fas fa-check-circle' : 'fas fa-circle'"></i>
                                At least one number
                            </li>
                        </ul>
                    </div>

                    <button class="reset-button" @click="resetPassword" :disabled="loading"
                        :class="{ 'loading': loading }">
                        <span v-if="loading" class="loading-spinner"></span>
                        <i v-else class="fas fa-key"></i>
                        <span>{{ loading ? 'Resetting...' : 'Reset Password' }}</span>
                    </button>

                    <div class="back-link">
                        <a href="#" @click.prevent="goToLogin">
                            <i class="fas fa-arrow-left"></i>
                            Back to Sign In
                        </a>
                    </div>
                </div>
                <div v-if="success" class="success-section">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>

                    <div class="forgot-header">
                        <h1>Password Reset Successful!</h1>
                        <p>Your password has been updated successfully.</p>
                    </div>

                    <div class="instructions">
                        <p>You can now log in to your account using your new password.</p>
                    </div>

                    <button class="login-button" @click="goToLogin">
                        <i class="fas fa-sign-in-alt"></i>
                        Go to Sign In
                    </button>
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

const email = ref('');
const newPassword = ref('');
const confirmPassword = ref('');
const loading = ref(false);
const error = ref('');
const successMessage = ref('');
const success = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const router = useRouter();

const formErrors = ref({
    email: '',
    newPassword: '',
    confirmPassword: ''
});

const emailError = computed(() => formErrors.value.email);
const newPasswordError = computed(() => formErrors.value.newPassword);
const confirmPasswordError = computed(() => formErrors.value.confirmPassword);

const getPasswordStrengthClass = computed(() => {
    if (!newPassword.value) return 'strength-0';
    const length = newPassword.value.length;
    const hasUpper = /[A-Z]/.test(newPassword.value);
    const hasLower = /[a-z]/.test(newPassword.value);
    const hasNumber = /\d/.test(newPassword.value);

    let score = 0;
    if (length >= 8) score++;
    if (length >= 12) score++;
    if (hasUpper && hasLower) score++;
    if (hasNumber) score++;

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
        email: '',
        newPassword: '',
        confirmPassword: ''
    };

    let isValid = true;

    // Email validation
    if (!email.value.trim()) {
        formErrors.value.email = 'Email is required';
        isValid = false;
    } else {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email.value)) {
            formErrors.value.email = 'Please enter a valid email address';
            isValid = false;
        }
    }

    // New password validation
    if (!newPassword.value) {
        formErrors.value.newPassword = 'New password is required';
        isValid = false;
    } else if (newPassword.value.length < 8) {
        formErrors.value.newPassword = 'Password must be at least 8 characters';
        isValid = false;
    } else if (!/[A-Z]/.test(newPassword.value) || !/[a-z]/.test(newPassword.value)) {
        formErrors.value.newPassword = 'Must contain uppercase & lowercase letters';
        isValid = false;
    } else if (!/\d/.test(newPassword.value)) {
        formErrors.value.newPassword = 'Must contain at least one number';
        isValid = false;
    }

    // Confirm password validation
    if (!confirmPassword.value) {
        formErrors.value.confirmPassword = 'Please confirm your password';
        isValid = false;
    } else if (newPassword.value !== confirmPassword.value) {
        formErrors.value.confirmPassword = 'Passwords do not match';
        isValid = false;
    }

    return isValid;
};

const resetPassword = async () => {
    error.value = '';
    successMessage.value = '';

    if (!validateForm()) {
        return;
    }
    console.log(email, getPasswordStrengthClass, confirmPassword)

    loading.value = true;

    try {
        await axios.post('/api/reset-password', {
            email: email.value,
            password: newPassword.value,
            password_confirmation: confirmPassword.value
        });


        await new Promise(resolve => setTimeout(resolve, 1000));

        success.value = true;
        successMessage.value = 'Password reset successfully!';

    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to reset password. Please try again.';
    } finally {
        loading.value = false;
    }
};

const goToLogin = () => {
    router.push('/login');
};

onMounted(() => {
    setTimeout(() => {
        document.getElementById('email')?.focus();
    }, 100);
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
    width: 100%;
    overflow: hidden;
    position: fixed;
}

.forgot-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    overflow: hidden;
    z-index: 1000;
}

.forgot-container {
    display: flex;
    width: 900px;
    max-width: 90vw;
    background-color: white;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
    animation: fadeIn 0.6s ease-out;
    height: 540px;
    min-height: 540px;
    max-height: 540px;
    overflow: hidden;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(15px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.forgot-left {
    flex: 1;
    background: linear-gradient(rgba(110, 142, 251, 0.85), rgba(167, 119, 227, 0.85));
    color: white;
    padding: 25px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    height: 540px;
}

.forgot-left h2 {
    font-size: 22px;
    margin-bottom: 8px;
    line-height: 1.3;
}

.forgot-left p {
    font-size: 13px;
    line-height: 1.4;
    opacity: 0.9;
    margin-bottom: 20px;
}

.my-logo {
    max-height: 26px;
}

.feature {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.feature i {
    background-color: rgba(255, 255, 255, 0.2);
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
    flex-shrink: 0;
    font-size: 12px;
}

.feature span {
    font-size: 12px;
    line-height: 1.4;
}

.forgot-right {
    flex: 1;
    padding: 25px;
    display: flex;
    flex-direction: column;
    height: 540px;
    overflow: hidden;
    position: relative;
}

.forgot-right,
.reset-form,
.success-section {
    overflow: hidden !important;
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}

.forgot-right::-webkit-scrollbar,
.reset-form::-webkit-scrollbar,
.success-section::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

.logo {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    color: #6e8efb;
    cursor: pointer;
    flex-shrink: 0;
}

.logo:hover {
    opacity: 0.8;
}

.logo span {
    font-size: 18px;
    font-weight: 700;
}

.forgot-header {
    margin-bottom: 5px;
    flex-shrink: 0;
}

.forgot-header h1 {
    color: #333;
    font-size: 20px;
    margin-bottom: 5px;
    line-height: 1.2;
}

.forgot-header p {
    color: #666;
    line-height: 1.4;
    font-size: 12px;
    margin-bottom: 5px;
}

.reset-form,
.success-section {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}

.input-group {
    margin-bottom: 5px;
    width: 100%;
    flex-shrink: 0;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    color: #555;
    font-weight: 500;
    font-size: 12px;
}

.input-wrapper {
    position: relative;
    width: 100%;
}

.input-wrapper input {
    width: 100%;
    padding: 10px 10px 10px 34px;
    border: 1.5px solid #e0e0e0;
    border-radius: 8px;
    font-size: 13px;
    transition: all 0.2s;
    height: 40px;
}

.input-wrapper input:focus {
    border-color: #6e8efb;
    outline: none;
    box-shadow: 0 0 0 2px rgba(110, 142, 251, 0.15);
}

.input-wrapper input.error-input {
    border-color: #d32f2f;
}

.input-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 13px;
    z-index: 1;
}

.error-text {
    color: #d32f2f;
    font-size: 11px;
    margin-top: 3px;
    display: block;
    min-height: 14px;
}

.password-strength {
    margin: 8px 0;
}

.strength-bar {
    height: 3px;
    border-radius: 2px;
    margin-bottom: 3px;
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
    font-size: 10px;
    font-weight: 500;
}

.password-requirements {
    padding: 0px;
    background: #f9f9f9;
    border-radius: 8px;
    text-align: left;
    flex-shrink: 0;
}

.password-requirements p {
    font-size: 11px;
    color: #333;
    margin-bottom: 6px;
}

.password-requirements ul {
    list-style: none;
    padding: 0;
}

.password-requirements li {
    font-size: 10px;
    color: #666;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.password-requirements li.valid {
    color: #4caf50;
}

.password-requirements li i {
    font-size: 9px;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    cursor: pointer;
    font-size: 13px;
    z-index: 1;
    background: transparent;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
}

.reset-button {
    background: linear-gradient(to right, #6e8efb, #a777e3);
    color: white;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 0px;
    width: 100%;
    height: 40px;
    flex-shrink: 0;
    margin-top: 7px;
}

.reset-button:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 5px 12px rgba(110, 142, 251, 0.25);
}

.reset-button:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.reset-button i {
    margin-right: 6px;
    font-size: 12px;
}

.back-link {
    text-align: center;
    margin-top: auto;
    padding-top: 10px;
    flex-shrink: 0;
}

.back-link a {
    color: #6e8efb;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
}

.back-link a:hover {
    color: #5a7df9;
    text-decoration: underline;
}

/* Success Section */
.success-section {
    text-align: center;
    justify-content: center;
    align-items: center;
}

.success-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    color: white;
    font-size: 24px;
    animation: pulse 2s infinite;
    flex-shrink: 0;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(110, 142, 251, 0.4);
    }

    70% {
        box-shadow: 0 0 0 10px rgba(110, 142, 251, 0);
    }

    100% {
        box-shadow: 0 0 0 0 rgba(110, 142, 251, 0);
    }
}

.instructions {
    margin: 10px 0 15px;
    text-align: center;
    color: #666;
    font-size: 12px;
    line-height: 1.5;
    flex-shrink: 0;
}

.login-button {
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    height: 38px;
    border: none;
    background: #6e8efb;
    color: white;
    flex-shrink: 0;
}

.login-button:hover {
    background: #5a7df9;
    transform: translateY(-1px);
}

.loading-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
    margin-right: 6px;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.error-message,
.success-message {
    padding: 8px 10px;
    border-radius: 6px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    animation: slideDown 0.2s ease-out;
    width: 100%;
    font-size: 12px;
    flex-shrink: 0;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-message {
    background-color: #ffeaea;
    color: #d32f2f;
}

.success-message {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.error-message i,
.success-message i {
    margin-right: 6px;
    font-size: 12px;
}

@media (max-width: 768px) {
    .forgot-container {
        flex-direction: column;
        width: 400px;
        height: 580px;
        max-height: 580px;
    }

    .forgot-left {
        height: auto;
        padding: 20px;
        min-height: 180px;
    }

    .forgot-right {
        height: auto;
        padding: 20px;
        flex: 1;
        min-height: 0;
    }

    .feature {
        margin-bottom: 8px;
    }
}

@media (max-width: 480px) {
    .forgot-wrapper {
        padding: 10px;
    }

    .forgot-container {
        width: 350px;
        height: 560px;
    }

    .forgot-left {
        padding: 15px;
    }

    .forgot-right {
        padding: 15px;
    }
}

html,
body,
#app,
.forgot-wrapper,
.forgot-container,
.forgot-left,
.forgot-right,
.reset-form,
.success-section {
    overflow: hidden !important;
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}

::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
    background: transparent !important;
}
</style>