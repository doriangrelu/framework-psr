<?php
date_default_timezone_set("Europe/Paris");
$root = dirname(__DIR__) . DS;
trim($root, DS);
if (DS == "\\") {
    $root = trim($root);
}
define('ROOT', $root);
$webRoot = str_replace("/index.php", "", $_SERVER["SCRIPT_NAME"]);
$webRoot = str_replace("public", "", $webRoot);
define("WEB_ROOT", $webRoot);
define("BUNDLE", ROOT . "src" . DS . "Bundle" . DS);
define("CONFIG", ROOT . "config" . DS);
define("LOGS", ROOT . "Logs" . DS);
define("TEMPLATE", ROOT . "src" . DS . "Template" . DS);
define("PATH_ENTITY", ROOT . "src" . DS . "Database" . DS . "Entity" . DS);
