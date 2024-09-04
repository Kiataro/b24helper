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

                        <el-button v-if="isAdmin" icon="el-icon-plus" @click="openAddModal"></el-button>

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
                            :visible.sync="isArticleVisible"
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

                    <!-- Модальное окно для добавления статьи -->

                    <el-dialog
                            title="Добавить заметку"
                            width="33%"
                            :visible.sync="isAddVisible">

                        <el-form
                                :rules="rules"
                                :model="articleForm"
                                ref="articleForm"
                                label-width="120px"
                                >
                            <!-- Заголовок -->

                            <el-form-item
                                    prop="title"
                                    label="Заголовок"

                            >
                                <el-input v-model="articleForm.title" clearable></el-input>
                            </el-form-item>

                            <!-- Подзаголовок -->

                            <el-form-item
                                    prop="subtitle"
                                    label="Подзаголовок"
                            >
                                <el-input v-model="articleForm.subtitle" clearable></el-input>
                            </el-form-item>

                            <!-- Динамические элементы -->

                            <el-form-item
                                    v-for="(element, index) in articleForm.elements"
                                    :label="element.type === 'paragraph' ? 'Параграф ' + (index + 1) : 'Код ' + (index + 1) + ' ' + element.select"
                                    :key="element.key"
                                    :prop="'elements.' + index + '.value'"
                                    :rules="{
        required: true,
        message: element.type === 'paragraph' ? 'Параграф не может быть пустым' : 'Код не может быть пустым',
        trigger: 'blur'
    }"
                            >
                                <el-input v-model="element.value" type="textarea"></el-input>

                                <el-dropdown @command="handleLanguage($event, index)" trigger="click" class="code-dropdown" v-if="element.type === 'code'">

                                    <el-button class="utility">
                                        <i class="fa-solid fa-code"></i>
                                    </el-button>

                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item command="php">PHP</el-dropdown-item>
                                        <el-dropdown-item command="js">JavaScript</el-dropdown-item>
                                        <el-dropdown-item command="html">HTML</el-dropdown-item>
                                        <el-dropdown-item command="css">CSS</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>

                                <el-popover
                                        v-if="element.type === 'code'"
                                        placement="bottom"
                                        width="200"
                                        trigger="click"

                                >
                                    <el-input
                                            v-model="inputValue"
                                            @input="handleInput"
                                            placeholder="Введите текст"
                                    ></el-input>
                                    <el-button slot="reference">
                                        <i class="el-icon-search"></i> <!-- Иконка для кнопки -->
                                        {{ selectedText }}
                                        <i class="el-icon-arrow-down"></i> <!-- Стрелка вниз -->
                                    </el-button>
                                </el-popover>

                                <el-button
                                        icon="el-icon-delete"
                                        @click.prevent="removeElement(index)"
                                        class="delete-button utility"
                                ></el-button>
                            </el-form-item>

                            <!-- Навигация -->

                            <el-form-item>
                                <el-button icon="el-icon-plus" @click="addParagraph">Параграф</el-button>
                                <el-button icon="el-icon-plus" @click="addCode">Код</el-button>
                                <el-button type="success" @click="submitForm('articleForm')">Сохранить</el-button>
                            </el-form-item>
                        </el-form>

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