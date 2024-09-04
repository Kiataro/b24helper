<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "Заметки web-разработчика");
?>

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
                                    <el-card shadow="hover"
                                             :class="['article-card', getCategoryClass(article.category)]"
                                             @click.native="openArticleModal(article)">
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

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>