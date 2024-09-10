<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');
$APPLICATION->SetPageProperty("TITLE", "Заметки web-разработчика");
?>

    <div id="app">
        <template>
            <div class="hero-section">
                <el-container>
                    <el-header>

                        <!-- Категории -->

                        <el-select v-model="selectedCategory" clearable placeholder="Категория" class="category-filter">
                            <el-option
                                    v-for="category in uniqueCategories"
                                    :key="category"
                                    :label="category"
                                    :value="category"
                            ></el-option>
                        </el-select>

                        <!-- Строка поиска -->

                        <el-input
                                placeholder="Введите запрос для поиска..."
                                v-model="searchQuery"
                                size="large"
                                class="search-input"
                                clearable
                        >
                            <el-button slot="append" icon="el-icon-search"></el-button>

                        </el-input>

                        <!-- Добавление статьи -->

                        <el-button v-if="isAdmin" icon="el-icon-plus" @click="openAddModal"></el-button>

                    </el-header>

                    <!-- Основной контент с карточками статей -->

                    <el-main>
                        <div class="article-cards">
                            <el-row :gutter="20">
                                <el-col :span="6" v-for="(article, index) in filteredArticles" :key="index">
                                    <el-card shadow="hover"
                                             :class="['article-card']"
                                             :style="{ borderLeft: '3px solid ' + article.categoryColor }"
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

                    <el-dialog :visible.sync="isArticleVisible" width="50%">

                        <div v-if="selectedArticle">
                            <h2>{{ selectedArticle.title }}</h2>
                            <p>{{ selectedArticle.description }}</p>
                            <div class="article-modal-meta">
                                <span>Категория: {{ selectedArticle.category }}</span>
                                <span>Дата: {{ selectedArticle.date }}</span>
                            </div>
                        </div>

                        <span slot="footer" class="dialog-footer"></span>

                    </el-dialog>

                    <!-- Модальное окно для добавления статьи -->

                    <el-dialog
                            title="Добавить заметку"
                            width="35%"
                            :visible.sync="isAddVisible">

                        <el-form
                                :rules="rules"
                                :model="articleForm"
                                ref="articleForm"
                                label-width="120px">

                            <!-- Заголовок -->

                            <el-form-item
                                    prop="title"
                                    label="Заголовок">

                                <el-input v-model="articleForm.title" clearable></el-input>

                            </el-form-item>

                            <!-- Подзаголовок -->

                            <el-form-item
                                    prop="subtitle"
                                    label="Подзаголовок">

                                <el-input v-model="articleForm.subtitle" clearable></el-input>

                            </el-form-item>

                            <!-- Категория -->

                            <el-form-item
                                    prop="category"
                                    label="Категория">

                                <el-select v-model="articleForm.category" clearable placeholder="" class="add-filter">
                                    <el-option
                                            v-for="category in uniqueCategories"
                                            :key="category"
                                            :label="category"
                                            :value="category"
                                    ></el-option>
                                </el-select>

                            </el-form-item>

                            <!-- Динамические элементы -->

                            <el-form-item
                                    v-for="(element, index) in articleForm.elements"
                                    :key="element.key"
                                    :prop="'elements.' + index + '.value'"
                                    :rules="getRules(element.type)">

                                <template #label>
                                    <div class="custom_label">
                                        <div>
                                            <span>{{ element.label }}</span>
                                        </div>
                                        <div>
                                            <span class="bold" v-if="element.fileName">{{ element.fileName }}</span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Если параграф -->

                                <template v-if="element.type === 'paragraph'">

                                    <el-input v-model="element.value" autosize type="textarea"></el-input>

                                </template>

                                <!-- Если код -->

                                <template v-else-if="element.type === 'code'">

                                    <el-input v-model="element.value" autosize type="textarea"></el-input>

                                    <el-popover
                                            placement="bottom"
                                            width="200"
                                            trigger="click"
                                            class="delete-button">

                                        <el-input
                                                v-model="element.fileName"
                                                placeholder="Имя файла">
                                        </el-input>

                                        <el-button slot="reference" class="utility">
                                            <i class="el-icon-document"></i>
                                        </el-button>

                                    </el-popover>

                                </template>

                                <!-- Если изображение -->

                                <template v-else-if="element.type === 'image'">

                                    <el-upload
                                            class="avatar-uploader"
                                            action="/local/api/loadFile.php"
                                            :show-file-list="false"
                                            :on-success="(res, file) => handleAvatarSuccess(res, file, index)"
                                            :before-upload="beforeAvatarUpload">
                                        <img v-if="element.fileSrc" :src="element.fileSrc" class="avatar">
                                        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
                                    </el-upload>

                                </template>

                                <!-- Кнопка удаления -->

                                <el-button
                                        icon="el-icon-delete"
                                        @click.prevent="removeElement(index)"
                                        class="delete-button utility">
                                </el-button>

                            </el-form-item>

                            <!-- Навигация -->

                            <div class="nav">

                                <el-button icon="el-icon-plus" @click="addParagraph">Параграф</el-button>

                                <el-button icon="el-icon-plus" @click="addCode">Код</el-button>

                                <el-button icon="el-icon-plus" @click="addImage">Изображение</el-button>

                                <el-button icon="el-icon-folder-add" @click="triggerFileInput"></el-button>

                                <el-tooltip v-if="articleForm.fileSrc" class="item" effect="dark" :content="articleForm.fileName" placement="top-start">
                                    <el-button icon="el-icon-folder-remove" @click="removeFileInput"></el-button>
                                </el-tooltip>

                                <el-button type="success" @click="submitForm('articleForm')">Сохранить</el-button>

                                <input type="file" ref="fileInput" style="display: none" @change="handleFileChange"/>

                            </div>

                        </el-form>

                    </el-dialog>

                </el-container>
            </div>

            <!-- Подвал -->

            <footer class="footer">
                <a :href="telegramLink" target="_blank" rel="noopener noreferrer" class="telegram-link">
                    <i class="fab fa-telegram-plane"></i>
                    <span class="username">{{ username }}</span>
                </a>
            </footer>

        </template>
    </div>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>