import { createApp } from 'vue'
import TaskSearch from './components/TaskSearch.vue'
import TaskList from './components/TaskList.vue'
import '@fortawesome/fontawesome-free/css/all.min.css';
import hljs from "highlight.js/lib/core";
import javascript from "highlight.js/lib/languages/javascript";
import php from "highlight.js/lib/languages/php";
import "highlight.js/styles/github.css";

window.hljs = hljs;

hljs.registerLanguage("javascript", javascript);
hljs.registerLanguage("php", php);

const app = createApp({
    data() {
        return {
            tasks: [],
            pagination: {
                current_page: 1,
                last_page: 1,
                per_page: 20,
                total: 0,
                from: 0,
                to: 0,
            },
            searchQuery: '',
            isLoading: false,
            fetchError: '',
            isAdmin: window.Laravel.isAdmin,
            isAuthenticated: window.Laravel.isAuthenticated,
            showHidden: false,
        }
    },
    methods: {
        async fetchTasks(options = {}) {
            const page = Number(options.page ?? this.pagination.current_page ?? 1);
            const query = String(options.query ?? this.searchQuery ?? '').trim();

            this.searchQuery = query;
            this.isLoading = true;
            this.fetchError = '';

            const params = new URLSearchParams();
            params.set('page', String(page));
            params.set('per_page', '20');

            if (query) {
                params.set('query', query);
            }

            if (this.showHidden && window.Laravel.taskSecret) {
                params.set('secret', window.Laravel.taskSecret);
            }

            try {
                const response = await fetch(`/api/tasks?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }

                const payload = await response.json();

                // Небольшая страховка: если бэк еще возвращает простой массив,
                // список не упадет, просто пагинация будет скрыта.
                if (Array.isArray(payload)) {
                    this.tasks = payload;
                    this.pagination = {
                        current_page: 1,
                        last_page: 1,
                        per_page: payload.length,
                        total: payload.length,
                        from: payload.length ? 1 : 0,
                        to: payload.length,
                    };
                    this.syncUrl();
                    return;
                }

                this.tasks = payload.data || [];
                this.pagination = {
                    current_page: Number(payload.current_page || 1),
                    last_page: Number(payload.last_page || 1),
                    per_page: Number(payload.per_page || 20),
                    total: Number(payload.total || 0),
                    from: Number(payload.from || 0),
                    to: Number(payload.to || 0),
                };

                this.syncUrl();
            } catch (error) {
                console.error(error);
                this.fetchError = 'Не удалось загрузить задачи. Попробуйте обновить страницу.';
            } finally {
                this.isLoading = false;
            }
        },
        syncUrl() {
            const params = new URLSearchParams();

            if (this.searchQuery) {
                params.set('query', this.searchQuery);
            }

            if (this.pagination.current_page > 1) {
                params.set('page', String(this.pagination.current_page));
            }

            const queryString = params.toString();
            const url = queryString ? `${window.location.pathname}?${queryString}` : window.location.pathname;

            window.history.replaceState({}, '', url);
        },
        handleSearch(query) {
            this.fetchTasks({ query, page: 1 });
        },
        changePage(page) {
            const nextPage = Number(page);

            if (
                !nextPage ||
                nextPage < 1 ||
                nextPage > this.pagination.last_page ||
                nextPage === this.pagination.current_page ||
                this.isLoading
            ) {
                return;
            }

            this.fetchTasks({ page: nextPage });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        updateTasks(tasks) {
            this.tasks = tasks;
        },
        refreshTasks(page = this.pagination.current_page) {
            this.fetchTasks({ page });
        },
        unlockHidden() {
            this.showHidden = true;
            this.fetchTasks({ query: '', page: 1 });
        },
        openCreateModal() {
            this.$refs.taskList.openTaskModal(null);
        }
    },
    mounted() {
        const params = new URLSearchParams(window.location.search);
        const page = Number(params.get('page') || 1);
        const query = params.get('query') || '';

        this.searchQuery = query;
        this.fetchTasks({ page, query });
    },
    template: `
        <div class="container mx-auto">
        <task-search
            :is-authenticated="isAuthenticated"
            :is-admin="isAdmin"
            :initial-query="searchQuery"
            @search="handleSearch"
            @open-create-modal="openCreateModal"
            @unlock-hidden="unlockHidden"
        />

        <div v-if="fetchError" class="mx-4 sm:mx-6 mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ fetchError }}
        </div>

        <task-list
            ref="taskList"
            :tasks="tasks"
            :pagination="pagination"
            :loading="isLoading"
            :is-admin="isAdmin"
            :show-hidden="showHidden"
            @update-tasks="updateTasks"
            @refresh-tasks="refreshTasks"
            @page-change="changePage"
        />
        </div>
    `
});

app.component('task-search', TaskSearch)
app.component('task-list', TaskList)

app.mount('#app')
