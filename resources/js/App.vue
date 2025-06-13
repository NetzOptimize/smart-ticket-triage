<template>
    <div class="app">
        <nav class="nav" v-if="isAuthenticated">
            <div class="nav__container">
                <router-link to="/tickets" class="nav__brand">Smart Ticket Triage</router-link>
                <div class="nav__menu">
                    <router-link to="/tickets" class="nav__link">Tickets</router-link>
                    <router-link to="/tickets/create" class="nav__link">New Ticket</router-link>
                    <button @click="logout" class="nav__link nav__link--button">Logout</button>
                </div>
            </div>
        </nav>

        <main class="main">
            <div class="main__container">
                <router-view></router-view>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();
const isAuthenticated = computed(() => !!localStorage.getItem('token'));

const logout = () => {
    localStorage.removeItem('token');
    router.push('/login');
};
</script>

<style>
/* Reset and base styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f5f7fa;
}

/* Layout */
.app {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.main {
    flex: 1;
    padding: 2rem 0;
}

.main__container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Navigation */
.nav {
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.nav__container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.nav__brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
    text-decoration: none;
}

.nav__menu {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.nav__link {
    color: #4a5568;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}

.nav__link:hover {
    color: #2c3e50;
}

.nav__link--button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-family: inherit;
}

/* Form styles */
.form {
    background: #fff;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 0 auto;
}

.form__title {
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.form__group {
    margin-bottom: 1.5rem;
}

.form__label {
    display: block;
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-weight: 500;
}

.form__input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.2s;
}

.form__input:focus {
    outline: none;
    border-color: #4299e1;
}

.form__button {
    background-color: #4299e1;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.form__button:hover {
    background-color: #3182ce;
}

/* Alert styles */
.alert {
    padding: 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.alert--error {
    background-color: #fed7d7;
    color: #c53030;
}

.alert--success {
    background-color: #c6f6d5;
    color: #2f855a;
}
</style> 