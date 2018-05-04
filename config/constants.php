<?php
date_default_timezone_set("Europe/Paris");
define("DS", DIRECTORY_SEPARATOR);
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
define("TEMPLATE", ROOT . "src" . DS . "Template" . DS);
