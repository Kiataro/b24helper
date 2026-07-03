<template>
    <div class="w-full flex items-center space-x-4 px-4 sm:px-6">
        <div class="flex-grow relative">
            <input
                type="text"
                v-model="searchQuery"
                @input="onInput"
                placeholder="Поиск задач..."
                class="border border-gray-400 rounded-lg p-3 outline-none w-full my-4 sm:my-6 pr-10 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 transition"
            />
            <span
                class="absolute right-5 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer hover:text-gray-700 transition"
                @click="clearSearch"
            >
                <i :class="searchQuery ? 'fa fa-xmark' : 'fa fa-search'"></i>
            </span>
        </div>

        <div v-if="isAuthenticated && isAdmin" class="flex-shrink-0">
            <button
                type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-4 text-center inline-flex items-center transition"
                @click="addTask"
            >
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        isAuthenticated: { type: Boolean, required: true },
        isAdmin: { type: Boolean, required: true },
        initialQuery: { type: String, default: '' },
    },
    emits: ['search', 'open-create-modal', 'unlock-hidden'],
    data() {
        return {
            searchQuery: this.initialQuery,
            searchTimeout: null,
        };
    },
    watch: {
        initialQuery(value) {
            if (value !== this.searchQuery) {
                this.searchQuery = value;
            }
        },
    },
    beforeUnmount() {
        clearTimeout(this.searchTimeout);
    },
    methods: {
        onInput() {
            clearTimeout(this.searchTimeout);

            this.searchTimeout = setTimeout(() => {
                const query = this.searchQuery.trim();

                if (query === window.Laravel.taskSecret) {
                    this.searchQuery = '';
                    this.$emit('unlock-hidden');
                    return;
                }

                this.$emit('search', query);
            }, 500);
        },

        clearSearch() {
            clearTimeout(this.searchTimeout);

            if (!this.searchQuery) {
                return;
            }

            this.searchQuery = '';
            this.$emit('search', '');
        },

        addTask() {
            if (!this.isAdmin) {
                alert('У вас нет прав для добавления задачи');
                return;
            }

            this.$emit('open-create-modal');
        },
    },
};
</script>
