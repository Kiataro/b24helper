<?php
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json');

$helper = new App\Helper;

$phpInput = json_decode(file_get_contents('php://input'), true);

$articles = $helper->addArticle($phpInput['data']);

echo json_encode($articles);
exit();