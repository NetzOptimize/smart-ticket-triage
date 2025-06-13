<template>
  <form class="ticket-form" @submit.prevent="submitTicket">
    <div class="ticket-form__group">
      <label class="ticket-form__label" for="subject">Subject</label>
      <input class="ticket-form__input" id="subject" v-model="form.subject" required />
    </div>
    <div class="ticket-form__group">
      <label class="ticket-form__label" for="body">Body</label>
      <textarea class="ticket-form__input" id="body" v-model="form.body" required></textarea>
    </div>
    <div class="ticket-form__group">
      <label class="ticket-form__label" for="category">Category</label>
      <select class="ticket-form__input" id="category" v-model="form.category_id" required>
        <option value="" disabled>Select category</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
    </div>
    <div class="ticket-form__actions">
      <button class="ticket-form__button" type="submit" :disabled="loading">
        {{ loading ? 'Submitting...' : 'Create Ticket' }}
      </button>
      <button class="ticket-form__button ticket-form__button--secondary" type="button" @click="classifyTicket" :disabled="classifying">
        <span v-if="classifying" class="ticket-form__spinner"></span>
        {{ classifying ? 'Classifying...' : 'Classify' }}
      </button>
    </div>
    <div v-if="error" class="ticket-form__error">{{ error }}</div>
    <div v-if="success" class="ticket-form__success">Ticket created successfully!</div>
  </form>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const form = ref({
  subject: '',
  body: '',
  category_id: ''
});
const categories = ref([]);
const loading = ref(false);
const classifying = ref(false);
const error = ref('');
const success = ref(false);

onMounted(async () => {
  try {
    const res = await fetch('/api/categories');
    categories.value = await res.json();
  } catch (e) {
    categories.value = [];
  }
});

async function submitTicket() {
  loading.value = true;
  error.value = '';
  success.value = false;
  try {
    const res = await fetch('/api/tickets', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
      },
      body: JSON.stringify(form.value)
    });
    if (!res.ok) throw new Error('Failed to create ticket');
    success.value = true;
    form.value = { subject: '', body: '', category_id: '' };
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

async function classifyTicket() {
  classifying.value = true;
  error.value = '';
  try {
    const res = await fetch('/api/tickets/classify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
      },
      body: JSON.stringify({ body: form.value.body })
    });
    if (!res.ok) throw new Error('Classification failed');
    const data = await res.json();
    form.value.category_id = data.category_id;
  } catch (e) {
    error.value = e.message;
  } finally {
    classifying.value = false;
  }
}
</script>

<style scoped>
.ticket-form {
  max-width: 400px;
  margin: 0 auto;
  padding: 2rem;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.ticket-form__group {
  margin-bottom: 1.5rem;
}
.ticket-form__label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 600;
}
.ticket-form__input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}
.ticket-form__actions {
  display: flex;
  gap: 1rem;
}
.ticket-form__button {
  padding: 0.5rem 1.5rem;
  border: none;
  border-radius: 4px;
  background: #007bff;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.ticket-form__button:disabled {
  background: #b3d7ff;
  cursor: not-allowed;
}
.ticket-form__button--secondary {
  background: #6c757d;
}
.ticket-form__spinner {
  display: inline-block;
  width: 1em;
  height: 1em;
  border: 2px solid #fff;
  border-top: 2px solid #007bff;
  border-radius: 50%;
  animation: ticket-form__spin 1s linear infinite;
  margin-right: 0.5em;
  vertical-align: middle;
}
@keyframes ticket-form__spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.ticket-form__error {
  color: #dc3545;
  margin-top: 1rem;
}
.ticket-form__success {
  color: #28a745;
  margin-top: 1rem;
}
</style>
