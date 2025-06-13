<template>
    <div class="tickets">
        <div class="tickets__header">
            <h1 class="tickets__title">Tickets</h1>
            <router-link to="/tickets/create" class="tickets__button">New Ticket</router-link>
        </div>

        <div class="tickets__filters">
            <div class="tickets__search">
                <input
                    type="text"
                    v-model="search"
                    placeholder="Search tickets..."
                    class="tickets__search-input"
                    @input="debouncedSearch"
                >
            </div>

            <div class="tickets__filter">
                <select v-model="categoryFilter" class="tickets__select" @change="fetchTickets">
                    <option value="">All Categories</option>
                    <option value="Unclassified">Unclassified</option>
                    <option value="Billing">Billing</option>
                    <option value="Bug">Bug</option>
                    <option value="Feature Request">Feature Request</option>
                    <option value="General">General</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="tickets__loading">
            Loading tickets...
        </div>

        <div v-else-if="error" class="alert alert--error">
            {{ error }}
        </div>

        <div v-else-if="tickets.data.length === 0" class="tickets__empty">
            No tickets found
        </div>

        <div v-else class="tickets__table-wrapper">
            <table class="tickets__table">
                <thead>
                    <tr>
                        <th @click="sortBy('subject')" class="tickets__sortable">
                            Subject
                            <span v-if="sortField === 'subject'" class="tickets__sort-indicator">
                                {{ sortDirection === 'asc' ? '↑' : '↓' }}
                            </span>
                        </th>
                        <th @click="sortBy('category')" class="tickets__sortable">
                            Category
                            <span v-if="sortField === 'category'" class="tickets__sort-indicator">
                                {{ sortDirection === 'asc' ? '↑' : '↓' }}
                            </span>
                        </th>
                        <th @click="sortBy('status')" class="tickets__sortable">
                            Status
                            <span v-if="sortField === 'status'" class="tickets__sort-indicator">
                                {{ sortDirection === 'asc' ? '↑' : '↓' }}
                            </span>
                        </th>
                        <th @click="sortBy('created_at')" class="tickets__sortable">
                            Created
                            <span v-if="sortField === 'created_at'" class="tickets__sort-indicator">
                                {{ sortDirection === 'asc' ? '↑' : '↓' }}
                            </span>
                        </th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="ticket in tickets.data" :key="ticket.id" class="tickets__row">
                        <td>{{ ticket.subject }}</td>
                        <td>
                            <span class="tickets__category" :class="'tickets__category--' + (ticket.category?.name?.toLowerCase() || '')">
                                {{ ticket.category?.name || 'Unclassified' }}
                            </span>
                        </td>
                        <td>
                            <span class="tickets__status" :class="'tickets__status--' + (ticket.status?.toLowerCase() || '')">
                                {{ ticket.status || 'Open' }}
                            </span>
                        </td>
                        <td>{{ formatDate(ticket.created_at) }}</td>
                        <td>
                            <button
                                v-if="!ticket.category"
                                @click="classifyTicket(ticket.id)"
                                class="tickets__action"
                                :disabled="ticket.classifying"
                            >
                                {{ ticket.classifying ? 'Classifying...' : 'Classify' }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="tickets__pagination">
                <button 
                    :disabled="!tickets.prev_page_url"
                    @click="changePage(tickets.current_page - 1)"
                    class="tickets__pagination-button"
                >
                    Previous
                </button>
                <span class="tickets__pagination-info">
                    Page {{ tickets.current_page }} of {{ tickets.last_page }}
                </span>
                <button 
                    :disabled="!tickets.next_page_url"
                    @click="changePage(tickets.current_page + 1)"
                    class="tickets__pagination-button"
                >
                    Next
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const tickets = ref({ data: [] });
const loading = ref(true);
const error = ref('');
const search = ref('');
const categoryFilter = ref('');
const sortField = ref('created_at');
const sortDirection = ref('desc');

// Custom debounce function
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const fetchTickets = async () => {
    loading.value = true;
    error.value = '';

    try {
        const params = {
            page: tickets.value.current_page || 1,
            search: search.value,
            category: categoryFilter.value || '',
            sort_field: sortField.value,
            sort_direction: sortDirection.value
        };

        console.log('Fetching tickets with params:', params);

        const response = await axios.get('/api/tickets', { params });
        tickets.value = {
            data: response.data.data,
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            next_page_url: response.data.next_page_url,
            prev_page_url: response.data.prev_page_url
        };
    } catch (err) {
        if (err.response?.status === 401) {
            router.push('/login');
        } else {
            error.value = 'Failed to load tickets';
            console.error(err);
        }
    } finally {
        loading.value = false;
    }
};

const debouncedSearch = debounce(() => {
    fetchTickets();
}, 300);

const changePage = (page) => {
    if (page < 1 || page > tickets.value.last_page) return;
    tickets.value.current_page = page;
    fetchTickets();
};

const sortBy = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    fetchTickets();
};

const classifyTicket = async (ticketId) => {
    const ticket = tickets.value.data.find(t => t.id === ticketId);
    if (!ticket) return;

    ticket.classifying = true;

    try {
        const response = await axios.post(`/api/tickets/${ticketId}/classify`);
        ticket.category = response.data.category;
        ticket.confidence = response.data.confidence;
    } catch (err) {
        error.value = 'Failed to classify ticket';
        console.error(err);
    } finally {
        ticket.classifying = false;
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

onMounted(fetchTickets);
</script>

<style scoped>
.tickets {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.tickets__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.tickets__title {
    color: #2c3e50;
    font-size: 1.5rem;
    margin: 0;
}

.tickets__button {
    background-color: #4299e1;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.2s;
}

.tickets__button:hover {
    background-color: #3182ce;
}

.tickets__filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.tickets__search {
    flex: 1;
}

.tickets__search-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 1rem;
}

.tickets__select {
    padding: 0.75rem;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    font-size: 1rem;
    min-width: 200px;
}

.tickets__table-wrapper {
    overflow-x: auto;
}

.tickets__table {
    width: 100%;
    border-collapse: collapse;
}

.tickets__table th,
.tickets__table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.tickets__table th {
    font-weight: 600;
    color: #4a5568;
    background-color: #f7fafc;
}

.tickets__category,
.tickets__status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
}

.tickets__category--billing {
    background-color: #ebf8ff;
    color: #2b6cb0;
}

.tickets__category--bug {
    background-color: #fed7d7;
    color: #c53030;
}

.tickets__category--feature request {
    background-color: #c6f6d5;
    color: #2f855a;
}

.tickets__category--general {
    background-color: #e2e8f0;
    color: #4a5568;
}

.tickets__status--open {
    background-color: #ebf8ff;
    color: #2b6cb0;
}

.tickets__status--closed {
    background-color: #e2e8f0;
    color: #4a5568;
}

.tickets__action {
    background-color: #4299e1;
    color: #fff;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background-color 0.2s;
}

.tickets__action:hover:not(:disabled) {
    background-color: #3182ce;
}

.tickets__action:disabled {
    background-color: #e2e8f0;
    cursor: not-allowed;
}

.tickets__loading,
.tickets__empty {
    text-align: center;
    padding: 2rem;
    color: #4a5568;
}

.tickets__sortable {
    cursor: pointer;
    user-select: none;
}

.tickets__sortable:hover {
    background-color: #edf2f7;
}

.tickets__sort-indicator {
    margin-left: 0.5rem;
    color: #4a5568;
}

.tickets__pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
    padding: 1rem;
}

.tickets__pagination-button {
    background-color: #4299e1;
    color: #fff;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.tickets__pagination-button:hover:not(:disabled) {
    background-color: #3182ce;
}

.tickets__pagination-button:disabled {
    background-color: #e2e8f0;
    cursor: not-allowed;
}

.tickets__pagination-info {
    color: #4a5568;
    font-size: 0.875rem;
}
</style> 