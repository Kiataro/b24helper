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
                    fileId: '',
                    fileName: '',
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

            /*-- Получаем статьи при загрузке --*/

            this.fetchArticles();

        },
        computed: {

            /*-- Получить уникальные категории статей --*/

            uniqueCategories() {
                return [...new Set(this.articles.map(article => article.category))];
            },

            filteredArticles() {
                return this.articles.filter(article => {
                    const matchesSearchQuery = article.title.toLowerCase().includes(this.searchQuery.toLowerCase());
                    const matchesCategory = !this.selectedCategory || article.category === this.selectedCategory;
                    return matchesSearchQuery && matchesCategory;
                });
            }
        },
        methods: {

            /*-- Получить статьи --*/

            async fetchArticles() {
                try {
                    const response = await fetch('/local/api/getArticles.php');

                    const data = await response.json();
                    this.articles = data;

                } catch (error) {

                    this.$message.error('Произошла ошибка при получении данных', error);

                }
            },

            /*-- Детальная модалка статьи --*/

            openArticleModal(article) {
                this.selectedArticle = article;
                this.isArticleVisible = true;
            },

            /*-- Модалка добавления статьи --*/

            openAddModal() {
                this.isAddVisible = true;
            },

            /*-- Валидация для формы --*/

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

            /*-- Добавление статьи --*/

            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {

                        this.$message.success('Статья успешно добавлена');

                    } else {

                        this.$message.error('Произошла ошибка при сохранении');
                        return false;
                    }
                });
            },

            /*-- Удалить элемент из формы --*/

            removeElement(index) {

                /*-- Если тип инпута "Изображение" --*/

                if (this.articleForm.elements[index].type === 'image') {
                    const fileId = this.articleForm.elements[index].fileId;

                    fetch('/local/api/removeFile.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ fileId: fileId })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {

                                this.articleForm.elements.splice(index, 1);

                            }
                        })
                        .catch(error => {

                            this.$message.error('Произошла ошибка при запросе на удаление файла ', error);

                        });

                } else {

                    this.articleForm.elements.splice(index, 1);
                }

            },

            /*-- Добавить параграф --*/

            addParagraph() {
                this.articleForm.elements.push({
                    type: 'paragraph',
                    key: Date.now(),
                    value: '',
                    label: 'Параграф'
                });
            },

            /*-- Добавить код --*/

            addCode() {
                this.articleForm.elements.push({
                    type: 'code',
                    key: Date.now(),
                    value: '',
                    label: 'Код',
                    fileName: '',
                });
            },

            /*-- Добавить изображение --*/

            addImage() {
                this.articleForm.elements.push({
                    type: 'image',
                    key: Date.now(),
                    label: 'Изображение',
                    fileSrc: '',
                    fileId: '',
                });
            },

            /*-- Обработчик после загрузки изображения --*/

            handleAvatarSuccess(res, file, index) {

                if (res.status === 'success') {

                    this.articleForm.elements[index].fileSrc = res.fileSrc;
                    this.articleForm.elements[index].fileId = res.fileId;

                } else {

                    this.$message.error('Ошибка загрузки изображения');
                }

            },

            /*-- Обработчик перед загрузкой изображения --*/

            beforeAvatarUpload(file) {

                const isJpgOrPng = file.type === 'image/jpeg' || file.type === 'image/png';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJpgOrPng) {
                    this.$message.error('Можно загружать только файлы форматов JPG или PNG');
                    return false;
                }

                if (!isLt2M) {
                    this.$message.error('Размер изображения не должен превышать 2MB');
                    return false;
                }

                return true;

            },

            /*-- Обработчик на закрепление файла к статье --*/

            triggerFileInput() {
                this.$refs.fileInput.click();
            },

            /*-- Обработчик на удаление закрепленного файла к статье --*/

            removeFileInput() {

                const fileId = this.articleForm.fileId;

                fetch('/local/api/removeFile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ fileId: fileId })
                })
                    .then(response => response.json())
                    .then(data => {

                        if (data.status === 'success') {

                            this.$message.success('Файл успешно удален');

                            this.$refs.fileInput.value = '';
                            this.articleForm.fileSrc = '';
                            this.articleForm.fileId = '';
                            this.articleForm.fileName = '';

                        } else {

                            this.$message.error('Ошибка при удалении файла');

                        }
                    })
                    .catch(error => {

                        this.$message.error('Произошла ошибка при запросе на удаление файла: ', error);

                    });

            },

            /*-- Обработчик при выборе файла закрепленного к статье --*/

            handleFileChange(event) {
                const file = event.target.files[0];
                if (file) {
                    this.addFile(file);
                }
            },

            /*-- Обработчик добавления файла --*/

            addFile(file) {

                const allowedExtensions = /\.(rar|7z|zip)$/i;
                const isValidFile = allowedExtensions.test(file.name);

                if (!isValidFile) {
                    this.$message.error('Можно загружать только файлы формата .rar .7z или .zip');
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);

                fetch('/local/api/loadFile.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {

                            this.articleForm.fileSrc = data.fileSrc;
                            this.articleForm.fileId = data.fileId;
                            this.articleForm.fileName = file.name;

                            this.$message.success('Файл успешно загружен');

                        } else {

                            this.$message.error('Ошибка загрузки файла: ' + data.message);

                        }
                    })
                    .catch(error => {

                        this.$message.error('Ошибка при отправке файла');
                    });

            },
        },
    })

}
