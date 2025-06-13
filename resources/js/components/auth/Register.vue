<template>
    <div class="form">
        <h1 class="form__title">Register</h1>
        
        <div v-if="error" class="alert alert--error">
            {{ error }}
        </div>

        <form @submit.prevent="handleSubmit">
            <div class="form__group">
                <label class="form__label" for="name">Name</label>
                <input
                    type="text"
                    id="name"
                    v-model="form.name"
                    class="form__input"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    v-model="form.email"
                    class="form__input"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    v-model="form.password"
                    class="form__input"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="password_confirmation">Confirm Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    class="form__input"
                    required
                >
            </div>

            <button type="submit" class="form__button" :disabled="loading">
                {{ loading ? 'Creating account...' : 'Register' }}
            </button>
        </form>

        <p class="form__footer">
            Already have an account? 
            <router-link to="/login" class="form__link">Login</router-link>
        </p>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const loading = ref(false);
const error = ref('');

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const handleSubmit = async () => {
    loading.value = true;
    error.value = '';

    try {
        const response = await axios.post('/api/register', form);
        localStorage.setItem('token', response.data.token);
        router.push('/tickets');
    } catch (err) {
        error.value = err.response?.data?.message || 'An error occurred during registration';
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.form__footer {
    margin-top: 1.5rem;
    text-align: center;
    color: #4a5568;
}

.form__link {
    color: #4299e1;
    text-decoration: none;
    font-weight: 500;
}

.form__link:hover {
    text-decoration: underline;
}
</style> 