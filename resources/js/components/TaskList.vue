<template>
    <div class="relative px-4 sm:px-6">
        <div
            v-if="loading"
            class="absolute inset-0 z-10 flex items-start justify-center rounded-2xl bg-white/70 pt-16 backdrop-blur-sm"
        >
            <div class="inline-flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-2 text-sm text-gray-600 shadow-sm">
                <i class="fa fa-spinner fa-spin text-blue-600"></i>
                Загружаю задачи...
            </div>
        </div>

        <div v-if="filteredTasks.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="task in filteredTasks"
                :key="task.id"
                class="group bg-white p-4 border border-gray-200 rounded-2xl shadow-sm hover:-translate-y-1 hover:border-blue-200 hover:shadow-xl cursor-pointer flex flex-col justify-between transition duration-200"
                @click="openTaskModal(task.id)"
            >
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 group-hover:text-blue-700 transition">
                        {{ task.title }}
                    </h3>
                    <p class="text-gray-600 mb-2">{{ task.subtitle }}</p>

                    <div v-if="task.categories && task.categories.length" class="mb-3 flex flex-wrap gap-1.5">
                        <span
                            v-for="category in task.categories"
                            :key="category.id"
                            class="text-xs font-medium px-2.5 py-0.5 rounded-full inline-flex items-center gap-1"
                            :class="{
                                'bg-red-100 text-red-800 dark:bg-red-700': category.color === 'red',
                                'bg-blue-100 text-blue-800 dark:bg-blue-700': category.color === 'blue',
                                'bg-green-100 text-green-800 dark:bg-green-700': category.color === 'green',
                                'bg-gray-100 text-gray-800 dark:bg-gray-700': category.color === 'gray',
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-700': category.color === 'yellow',
                                'bg-indigo-100 text-indigo-800 dark:bg-indigo-700': category.color === 'indigo',
                                'bg-purple-100 text-purple-800 dark:bg-purple-700': category.color === 'purple',
                                'bg-pink-100 text-pink-800 dark:bg-pink-700': category.color === 'pink',
                            }"
                        >
                            <img v-if="category.icon" :src="category.icon" alt="" class="w-3 h-3 mr-1 rounded" />
                            {{ category.value }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-start text-xs text-gray-500 mt-3">
                    {{ formatDate(task.created_at) }}
                </div>
            </div>
        </div>

        <div v-else-if="!loading" class="rounded-2xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center shadow-sm">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                <i class="fa fa-magnifying-glass"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">Ничего не найдено</h3>
            <p class="mt-1 text-sm text-gray-500">Попробуй изменить поисковый запрос или очистить поиск.</p>
        </div>

        <div
            v-if="showPagination"
            class="mt-8 mb-4 flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="text-sm text-gray-500">
                Показано
                <span class="font-semibold text-gray-800">{{ pagination.from }}</span>
                –
                <span class="font-semibold text-gray-800">{{ pagination.to }}</span>
                из
                <span class="font-semibold text-gray-800">{{ pagination.total }}</span>
            </div>

            <nav class="flex items-center justify-center gap-1" aria-label="Пагинация">
                <button
                    type="button"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-gray-200 px-3 text-sm font-medium text-gray-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-gray-200 disabled:hover:bg-white disabled:hover:text-gray-600"
                    :disabled="isFirstPage || loading"
                    @click="goToPage(pagination.current_page - 1)"
                >
                    <i class="fa fa-chevron-left text-xs"></i>
                </button>

                <button
                    v-for="(page, index) in visiblePages"
                    :key="`${page}-${index}`"
                    type="button"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl px-3 text-sm font-semibold transition"
                    :class="page === pagination.current_page
                        ? 'bg-blue-700 text-white shadow-md shadow-blue-200'
                        : page === '...'
                            ? 'cursor-default text-gray-400'
                            : 'border border-gray-200 text-gray-600 hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700'"
                    :disabled="page === '...' || loading"
                    @click="page !== '...' && goToPage(page)"
                >
                    {{ page }}
                </button>

                <button
                    type="button"
                    class="inline-flex h-10 min-w-10 items-center justify-center rounded-xl border border-gray-200 px-3 text-sm font-medium text-gray-600 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700 disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:border-gray-200 disabled:hover:bg-white disabled:hover:text-gray-600"
                    :disabled="isLastPage || loading"
                    @click="goToPage(pagination.current_page + 1)"
                >
                    <i class="fa fa-chevron-right text-xs"></i>
                </button>
            </nav>
        </div>
    </div>

    <TaskModal
        :taskId="selectedTaskId"
        :isOpen="isModalOpen"
        :is-admin="isAdmin"
        @close="closeTaskModal"
        @deleted="handleDeletedTask"
        @saved="handleSavedTask"
    />
</template>

<script>
import TaskModal from './TaskModal.vue';

export default {
    components: { TaskModal },
    props: {
        tasks: { type: Array, default: () => [] },
        pagination: {
            type: Object,
            default: () => ({
                current_page: 1,
                last_page: 1,
                per_page: 20,
                total: 0,
                from: 0,
                to: 0,
            }),
        },
        loading: { type: Boolean, default: false },
        isAdmin: { type: Boolean, required: true },
        showHidden: { type: Boolean, default: false },
    },
    emits: ['update-tasks', 'page-change', 'refresh-tasks'],
    data() {
        return {
            isModalOpen: false,
            selectedTaskId: null,
            secretEntered: false,
        };
    },
    computed: {
        filteredTasks() {
            if (!this.showHidden) {
                return this.tasks.filter(task => !task.hidden);
            }

            return this.tasks;
        },
        showPagination() {
            return Number(this.pagination.last_page || 1) > 1;
        },
        isFirstPage() {
            return Number(this.pagination.current_page || 1) <= 1;
        },
        isLastPage() {
            return Number(this.pagination.current_page || 1) >= Number(this.pagination.last_page || 1);
        },
        visiblePages() {
            const current = Number(this.pagination.current_page || 1);
            const last = Number(this.pagination.last_page || 1);

            if (last <= 7) {
                return Array.from({ length: last }, (_, index) => index + 1);
            }

            const pages = [1];
            const start = Math.max(2, current - 1);
            const end = Math.min(last - 1, current + 1);

            if (start > 2) {
                pages.push('...');
            }

            for (let page = start; page <= end; page++) {
                pages.push(page);
            }

            if (end < last - 1) {
                pages.push('...');
            }

            pages.push(last);

            return pages;
        },
    },
    methods: {
        unlockHidden() {
            this.secretEntered = true;
        },
        goToPage(page) {
            this.$emit('page-change', page);
        },
        openTaskModal(taskId) {
            this.selectedTaskId = taskId;
            this.isModalOpen = true;
        },
        closeTaskModal() {
            this.isModalOpen = false;
            this.selectedTaskId = null;
        },
        handleSavedTask(savedTask) {
            const index = this.tasks.findIndex(t => t.id === savedTask.id);

            if (index !== -1) {
                const updatedTasks = [...this.tasks];
                updatedTasks[index] = savedTask;
                this.$emit('update-tasks', updatedTasks);
                return;
            }

            this.$emit('refresh-tasks', 1);
        },
        handleDeletedTask(taskId) {
            this.$emit('update-tasks', this.tasks.filter(task => task.id !== taskId));
            this.$emit('refresh-tasks');
        },
        formatDate(dateString) {
            if (!dateString) return "";
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, "0");
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const year = date.getFullYear();
            return `${day}.${month}.${year}`;
        }
    },
};
</script>
