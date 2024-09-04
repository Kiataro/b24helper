// Script

Vue.config.devtools = false;
Vue.config.productionTip = false;

window.onload = function() {

    new Vue({
        el: '#app',
        data: function() {
            return {
                searchQuery: '',
                selectedCategory: '',
                articles: [
                    {
                        title: 'Как использовать Element UI в ваших проектах',
                        description: 'Подробное руководство по интеграции и использованию Element UI в веб-проектах.',
                        category: 'Технологии',
                        date: '2024-08-01'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Преимущества минимализма в дизайне',
                        description: 'Изучите основные принципы минимализма и узнайте, почему они важны в веб-дизайне.',
                        category: 'Дизайн',
                        date: '2024-07-15'
                    },
                    {
                        title: 'Vue.js и его экосистема: что нового?',
                        description: 'Последние новости и обновления в экосистеме Vue.js, включая новые релизы и пакеты.',
                        category: 'Разработка',
                        date: '2024-06-25'
                    },
                    {
                        title: 'Vue2.js и его экосистема: что нового?',
                        description: 'Последние новости и обновления в экосистеме Vue.js, включая новые релизы и пакеты.',
                        category: 'Разработка',
                        date: '2024-06-25'
                    },
                    // Добавьте больше статей по необходимости
                ],
                isModalVisible: false,
                selectedArticle: null,
                telegramLink: 'https://t.me/Zammensiny',
                username: '@Zammensiny'
            };
        },
        computed: {
            uniqueCategories() {
                // Создает массив уникальных категорий из статей
                return [...new Set(this.articles.map(article => article.category))];
            },
            filteredArticles() {
                return this.articles.filter(article => {
                    const matchesSearchQuery = article.title.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesCategory = !this.selectedCategory || article.category === this.selectedCategory;
                    return matchesSearchQuery && matchesCategory;
                });
            },
        },
        methods: {
            handleSearch() {
                console.log('Поиск запроса:', this.searchQuery);
            },
            openArticleModal(article) {
                this.selectedArticle = article;
                this.isModalVisible = true;
            },
            closeModal() {
                this.isModalVisible = false;
                this.selectedArticle = null;
            },
            getCategoryClass(category) {
                switch (category) {
                    case 'Дизайн':
                        return 'category-red';
                    case 'Технологии':
                        return 'category-blue';
                    case 'Разработка':
                        return 'category-green';
                    default:
                        return '';
                }
            }
        },
    })

}
