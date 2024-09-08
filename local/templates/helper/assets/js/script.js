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
                articles: [],
                isArticleVisible: false,
                isAddVisible: false,
                selectedArticle: null,
                telegramLink: 'https://t.me/Zammensiny',
                username: '@Zammensiny',
                articleForm: {
                    elements: [],
                    title: '',
                    subtitle: '',
                    category: '',
                    fileSrc: '',
                },
                imageUrl: '',
                rules: {
                    title: [
                        { required: true, message: 'Заголовок не может быть пустым', trigger: 'blur' },
                    ],
                    subtitle: [
                        { required: true, message: 'Подзаголовок не может быть пустым', trigger: 'blur' },
                    ],
                    category: [
                        { required: true, message: 'Категория не может быть пустой', trigger: 'blur' },
                    ],
                }

            };
        },
        created() {
            this.fetchArticles(); // Вызываем метод при создании компонента
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
            hasFile() {
                // Проверяем, есть ли выбранный файл
                return this.articleForm.fileSrc !== '';
            },
        },
        methods: {
            async fetchArticles() {
                try {
                    const response = await fetch('/local/api/getArticles.php'); // Отправляем GET-запрос
                    if (!response.ok) {
                        throw new Error('Ошибка при получении данных'); // Обработка HTTP ошибок
                    }
                    const data = await response.json(); // Парсим ответ как JSON
                    this.articles = data; // Подставляем полученные данные в селект
                } catch (error) {
                    console.error('Ошибка при получении категорий:', error);
                }
            },
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
            getRules(type) {
                switch (type) {
                    case 'paragraph':
                        return [
                            { required: true, message: 'Параграф не может быть пустым', trigger: 'blur' }
                        ];
                    case 'code':
                        return [
                            { required: true, message: 'Код не может быть пустым', trigger: 'blur' }
                        ];
                    case 'image':
                        return [
                            { required: false }
                        ];
                    default:
                        return [
                            { required: true, message: 'Поле не может быть пустым', trigger: 'blur' }
                        ];
                }
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
                    value: '',
                    label: 'Параграф'
                });
            },
            addCode() {
                this.articleForm.elements.push({
                    type: 'code',
                    key: Date.now(),
                    value: '',
                    label: 'Код',
                    fileName: '',
                });
            },
            addImage() {
                this.articleForm.elements.push({
                    type: 'image',
                    key: Date.now(),
                    label: 'Изображение',
                    fileSrc: '',
                });
            },
            handleAvatarSuccess(res, file, index) {
                this.articleForm.elements[index].fileSrc = URL.createObjectURL(file.raw);
            },
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG) {
                    this.$message.error('Avatar picture must be JPG format!');
                }
                if (!isLt2M) {
                    this.$message.error('Avatar picture size can not exceed 2MB!');
                }
                return isJPG && isLt2M;
            },
            triggerFileInput() {
                this.$refs.fileInput.click();
            },
            removeFileInput() {
                this.$refs.fileInput.value = '';
                this.articleForm.fileSrc = '';

            },
            // Метод, который срабатывает при выборе файла
            handleFileChange(event) {
                const file = event.target.files[0]; // Получаем выбранный файл
                if (file) {
                    this.addFile(file); // Передаем файл в метод addFile
                }
            },
            addFile(file) {
                this.articleForm.fileSrc = file;
            },
        },
    })

}
