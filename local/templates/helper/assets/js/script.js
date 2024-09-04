// Script

Vue.config.devtools = false;
Vue.config.productionTip = false;

window.onload = function() {

    new Vue({
        el: '#app',
        data: function() {
            return {
                isAdmin: true,
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
                isArticleVisible: false,
                isAddVisible: false,
                selectedArticle: null,
                telegramLink: 'https://t.me/Zammensiny',
                username: '@Zammensiny',
                articleForm: {
                    elements: [],
                    title: '',
                    subtitle: '',
                },
                inputValue: '', // Значение инпута
                selectedText: 'Выберите или введите значение', // Изначальный текст кнопки
                popoverVisible: false, // Состояние видимости поповера
                selectedIcon: 'fa-solid fa-code',
                selectedColor: '',
                rules: {
                    title: [
                        { required: true, message: 'Заголовок не может быть пустым', trigger: 'blur' },
                    ],
                    subtitle: [
                        { required: true, message: 'Подзаголовок не может быть пустым', trigger: 'blur' },
                    ],
                }

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
                this.isArticleVisible = true;
            },
            openAddModal() {
                this.isAddVisible = true;
            },
            closeModal() {
                this.isArticleVisible = false;
                this.selectedArticle = null;
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        alert('submit!');

                        console.log(this.articleForm)

                    } else {
                        console.log('error submit!!');
                        return false;
                    }
                });
            },
            removeParagraph(item) {
                var index = this.articleForm.paragraphs.indexOf(item);
                if (index !== -1) {
                    this.articleForm.paragraphs.splice(index, 1);
                }
            },
            removeCode(item) {
                var index = this.articleForm.codes.indexOf(item);
                if (index !== -1) {
                    this.articleForm.codes.splice(index, 1);
                }
            },
            removeElement(index) {
                this.articleForm.elements.splice(index, 1);
            },
            addParagraph() {
                this.articleForm.elements.push({
                    type: 'paragraph',
                    key: Date.now(),
                    value: ''
                });
            },
            addCode() {
                this.articleForm.elements.push({
                    type: 'code',
                    key: Date.now(),
                    value: '',
                    select: '',
                });
            },
            handleLanguage(command, index) {

                this.articleForm.elements[index].select = command;
            },
            handleInput(value) {
                // Ваш метод для обработки ввода
                console.log(`Введено значение: ${value}`);
                this.selectedText = value || 'Выберите или введите значение';
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
