<template>
  <div class="ticket-list">
    <div class="ticket-list__filters">
      <input class="ticket-list__search" v-model="search" placeholder="Search tickets..." @input="fetchTickets" />
      <select class="ticket-list__filter" v-model="category" @change="fetchTickets">
        <option value="">All Categories</option>
        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
    </div>
    <div v-if="loading" class="ticket-list__loading">Loading...</div>
    <div v-else>
      <div v-if="tickets.length === 0" class="ticket-list__empty">No tickets found.</div>
      <ul class="ticket-list__items">
        <li v-for="ticket in tickets" :key="ticket.id" class="ticket-list__item">
          <div class="ticket-list__subject">{{ ticket.subject }}</div>
          <div class="ticket-list__body">{{ ticket.body }}</div>
          <div class="ticket-list__meta">
            <span class="ticket-list__category">{{ ticket.category ? ticket.category.name : 'Uncategorized' }}</span>
            <span :class="['ticket-list__status', 'ticket-list__status--' + ticket.status]">{{ ticket.status }}</span>
          </div>
          <button class="ticket-list__classify" @click="classify(ticket)" :disabled="ticket.status === 'processing' || ticket.status === 'classified'">
            <span v-if="classifyingId === ticket.id" class="ticket-list__spinner"></span>
            {{ classifyingId === ticket.id ? 'Classifying...' : 'Classify' }}
          </button>
        </li>
      </ul>
    </div>
    <div v-if="error" class="ticket-list__error">{{ error }}</div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const tickets = ref([]);
const categories = ref([]);
const loading = ref(false);
const error = ref('');
const search = ref('');
const category = ref('');
const classifyingId = ref(null);

async function fetchTickets() {
  loading.value = true;
  error.value = '';
  try {
    const params = new URLSearchParams();
    if (search.value) params.append('search', search.value);
    if (category.value) params.append('category_id', category.value);
    const res = await fetch(`/api/tickets?${params.toString()}`);
    if (!res.ok) throw new Error('Failed to fetch tickets');
    tickets.value = await res.json();
  } catch (e) {
    error.value = e.message;
  } finally {
    loading.value = false;
  }
}

async function fetchCategories() {
  try {
    const res = await fetch('/api/categories');
    categories.value = await res.json();
  } catch {
    categories.value = [];
  }
}

async function classify(ticket) {
  classifyingId.value = ticket.id;
  error.value = '';
  try {
    const res = await fetch(`/api/tickets/${ticket.id}/classify`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
      }
    });
    if (!res.ok) throw new Error('Classification failed');
    await fetchTickets();
  } catch (e) {
    error.value = e.message;
  } finally {
    classifyingId.value = null;
  }
}

onMounted(() => {
  fetchCategories();
  fetchTickets();
});
</script>

<style scoped>
.ticket-list {
  max-width: 700px;
  margin: 2rem auto;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  padding: 2rem;
}
.ticket-list__filters {
  display: flex;
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.ticket-list__search, .ticket-list__filter {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}
.ticket-list__items {
  list-style: none;
  padding: 0;
  margin: 0;
}
.ticket-list__item {
  border-bottom: 1px solid #eee;
  padding: 1rem 0;
}
.ticket-list__subject {
  font-weight: 600;
  font-size: 1.1rem;
}
.ticket-list__body {
  margin: 0.5rem 0;
}
.ticket-list__meta {
  font-size: 0.9rem;
  color: #666;
  display: flex;
  gap: 1rem;
}
.ticket-list__category {
  font-style: italic;
}
.ticket-list__status {
  text-transform: capitalize;
}
.ticket-list__status--processing {
  color: #007bff;
}
.ticket-list__status--classified {
  color: #28a745;
}
.ticket-list__classify {
  margin-top: 0.5rem;
  padding: 0.3rem 1rem;
  border: none;
  border-radius: 4px;
  background: #007bff;
  color: #fff;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}
.ticket-list__classify:disabled {
  background: #b3d7ff;
  cursor: not-allowed;
}
.ticket-list__spinner {
  display: inline-block;
  width: 1em;
  height: 1em;
  border: 2px solid #fff;
  border-top: 2px solid #007bff;
  border-radius: 50%;
  animation: ticket-list__spin 1s linear infinite;
  margin-right: 0.5em;
  vertical-align: middle;
}
@keyframes ticket-list__spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
.ticket-list__loading {
  text-align: center;
  color: #007bff;
}
.ticket-list__empty {
  text-align: center;
  color: #888;
}
.ticket-list__error {
  color: #dc3545;
  margin-top: 1rem;
  text-align: center;
}
</style>
