<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

\Bitrix\Main\Page\Asset::getInstance()->addCss(SITE_TEMPLATE_PATH. '/assets/css/style.css');

\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH. '/assets/js/libs/vue/index.js');
\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH. '/assets/js/libs/element-ui/index.js');

\Bitrix\Main\Page\Asset::getInstance()->addJs(SITE_TEMPLATE_PATH. '/assets/js/script.js');

?>

<!DOCTYPE html>
<html>
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