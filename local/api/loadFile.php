<?php
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NO_AGENT_CHECK', true);
define('PUBLIC_AJAX_MODE', true);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$helper = new App\Helper;
$fileData = $helper->loadFile($_FILES);

echo json_encode($fileData);
