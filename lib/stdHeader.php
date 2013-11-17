<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(-1);

date_default_timezone_set('GMT');

function autoloader($classname) {
    $classname = str_replace("\\", "/", $classname);
    $dirPath = explode("_", $classname);
    $baseDir = dirname(__FILE__)."/classes/";
    $pathToFile = '';
    foreach ($dirPath as $dir) {
        $pathToFile .= "/".$dir;
    }
    $pathToFile .= ".php";
    if (file_exists($baseDir.$pathToFile))
        include_once($baseDir.$pathToFile);
}
spl_autoload_register("autoloader");

?>