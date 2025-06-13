<template>
    <div class="form">
        <h1 class="form__title">Create New Ticket</h1>

        <div v-if="error" class="alert alert--error">
            {{ error }}
        </div>

        <form @submit.prevent="handleSubmit">
            <div class="form__group">
                <label class="form__label" for="subject">Subject</label>
                <input
                    type="text"
                    id="subject"
                    v-model="form.subject"
                    class="form__input"
                    required
                >
            </div>

            <div class="form__group">
                <label class="form__label" for="body">Description</label>
                <textarea
                    id="body"
                    v-model="form.body"
                    class="form__input form__input--textarea"
                    rows="6"
                    required
                ></textarea>
            </div>

            <div class="form__actions">
                <router-link to="/tickets" class="form__button form__button--secondary">
                    Cancel
                </router-link>
                <button type="submit" class="form__button" :disabled="loading">
                    {{ loading ? 'Creating...' : 'Create Ticket' }}
                </button>
            </div>
        </form>
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
    subject: '',
    body: ''
});

const handleSubmit = async () => {
    loading.value = true;
    error.value = '';

    try {
        await axios.post('/api/tickets', form);
        router.push('/tickets');
    } catch (err) {
        if (err.response?.status === 401) {
            router.push('/login');
        } else {
            error.value = err.response?.data?.message || 'Failed to create ticket';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.form__input--textarea {
    resize: vertical;
    min-height: 120px;
}

.form__actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
}

.form__button--secondary {
    background-color: #e2e8f0;
    color: #4a5568;
}

.form__button--secondary:hover {
    background-color: #cbd5e0;
}
</style> 