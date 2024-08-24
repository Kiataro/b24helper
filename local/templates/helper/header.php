<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Page\Asset::getInstance()->addCss('https://unpkg.com/element-ui/lib/theme-chalk/index.css');
\Bitrix\Main\Page\Asset::getInstance()->addCss('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

\Bitrix\Main\Page\Asset::getInstance()->addJs('https://unpkg.com/vue@2/dist/vue.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs('https://unpkg.com/element-ui/lib/index.js');


?>

<!DOCTYPE html>
<html>
<style>
    html, body, div, span, applet, object, iframe,
    h1, h2, h3, h4, h5, h6, p, blockquote, pre,
    a, abbr, acronym, address, big, cite, code,
    del, dfn, em, img, ins, kbd, q, s, samp,
    small, strike, strong, sub, sup, tt, var,
    b, u, i, center,
    dl, dt, dd, ol, ul, li,
    fieldset, form, label, legend,
    table, caption, tbody, tfoot, thead, tr, th, td,
    article, aside, canvas, details, embed,
    figure, figcaption, footer, header, hgroup,
    menu, nav, output, ruby, section, summary,
    time, mark, audio, video {
        margin: 0;
        padding: 0;
        border: 0;
        font-size: 100%;
        font: inherit;
        vertical-align: baseline;
    }
    /* HTML5 display-role reset for older browsers */
    article, aside, details, figcaption, figure,
    footer, header, hgroup, menu, nav, section {
        display: block;
    }
    body {
        line-height: 1;
    }
    ol, ul {
        list-style: none;
    }
    blockquote, q {
        quotes: none;
    }
    blockquote:before, blockquote:after,
    q:before, q:after {
        content: '';
        content: none;
    }
    table {
        border-collapse: collapse;
        border-spacing: 0;
    }
</style>
<head>

    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <meta
            name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    />
    <meta name="format-detection" content="telephone=no"/>
    <meta name="format-detection" content="address=no"/>

    <title><?php $APPLICATION->ShowTitle() ?></title>
    <?php $APPLICATION->ShowHead(); ?>

    <!-- defaults -->
    <script>
        window.modalDefaults = {};

        window.dropdownDefaults = {};

        window.selectDefaults = {};

    </script>

</head>
<body>
<div id="panel"><?php $APPLICATION->ShowPanel(); ?></div>

<div id="app">
    <template>
        <div class="hero-section">
            <el-container>
                <!-- Строка поиска в верхней части -->
                <el-header>
                    <el-select v-model="selectedCategory" clearable placeholder="Категория" class="category-filter">
                        <el-option
                                v-for="category in uniqueCategories"
                                :key="category"
                                :label="category"
                                :value="category"
                        ></el-option>
                    </el-select>
                    <el-input
                            placeholder="Введите запрос для поиска..."
                            v-model="searchQuery"
                            size="large"
                            class="search-input"
                            clearable
                            @keyup.enter="handleSearch"
                    >
                        <el-button slot="append" icon="el-icon-search" @click="handleSearch"></el-button>
                    </el-input>
                </el-header>

                <!-- Основной контент с карточками статей -->
                <el-main>
                    <div class="article-cards">
                        <el-row :gutter="20">
                            <el-col :span="6" v-for="(article, index) in filteredArticles" :key="index">
                                <!-- Используем @click.native для прослушивания событий клика -->
                                <el-card shadow="hover" :class="['article-card', getCategoryClass(article.category)]"  @click.native="openArticleModal(article)">
                                    <h3 class="article-title">{{ article.title }}</h3>
                                    <p class="article-description">{{ article.description }}</p>
                                    <div class="article-meta">
                                        <span class="article-category">{{ article.category }}</span>
                                        <span class="article-date">{{ article.date }}</span>
                                    </div>
                                </el-card>
                            </el-col>
                        </el-row>
                    </div>
                </el-main>

                <!-- Модальное окно для отображения деталей статьи -->
                <el-dialog
                        title="Детали статьи"
                        :visible.sync="isModalVisible"
                        width="50%"
                        @close="closeModal"
                >
                    <div v-if="selectedArticle">
                        <h2>{{ selectedArticle.title }}</h2>
                        <p>{{ selectedArticle.description }}</p>
                        <div class="article-modal-meta">
                            <span>Категория: {{ selectedArticle.category }}</span>
                            <span>Дата: {{ selectedArticle.date }}</span>
                        </div>
                    </div>
                    <span slot="footer" class="dialog-footer">
          <el-button @click="closeModal">Закрыть</el-button>
        </span>
                </el-dialog>
            </el-container>
        </div>

        <footer class="footer">
            <a :href="telegramLink" target="_blank" rel="noopener noreferrer" class="telegram-link">
                <i class="fab fa-telegram-plane"></i>
                <span class="username">{{ username }}</span>
            </a>
        </footer>

    </template>
</div>

<script>
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
</script>

<style scoped>

    @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap');

    html {
        font-family: "Roboto", sans-serif;
    }

    .hero-section {
        min-height: 100vh;
        background-color: #f5f5f5;
        text-align: center;
        padding: 20px;
    }

    .search-input {
        width: 60%;
        max-width: 700px;
        margin-bottom: 20px;
    }

    .category-filter {
        width: 135px;
        margin: 20px 0;
    }

    .article-cards {
        margin-top: 30px;
    }

    .article-card {
        border-radius: 8px;
        padding: 20px;
        cursor: pointer;
        margin: 10px 0;
    }

    .article-title {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: #333;
    }

    .article-description {
        color: #666;
        margin-bottom: 15px;
    }

    .article-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.875rem;
        color: #999;
    }

    .article-category {
        font-weight: bold;
    }

    .article-date {
        font-style: italic;
    }

    .article-modal-meta {
        margin-top: 10px;
        font-size: 0.875rem;
        color: #666;
        display: flex;
        justify-content: space-between;
    }

    .dialog-footer {
        text-align: right;
    }

    .footer {
        text-align: center;
        padding: 10px;
        background-color: #f1f1f1;
        position: fixed;
        bottom: 0;
        width: 100%;
        border-top: 1px solid #ddd;
    }

    .footer a {
        color: #333;
        text-decoration: none;
    }

    .footer a:hover {
        text-decoration: underline;
    }

    .category-blue {
        border-left: 3px solid cornflowerblue;
    }
    .category-red {
        border-left: 3px solid #ff404a;
    }
    .category-green {
        border-left: 3px solid #708c3c;
    }

</style>