<?php
/**
 * Include this file at the beginning of all pages.
 *
 * It sets environment variables, starts session, and contains utility functions such as
 * importing header and footer.
 */
if(strpos($_SERVER['HTTP_HOST'], "localhost") !== false) {
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']."/");
    define("HTTP_ROOT", "http://" . $_SERVER['HTTP_HOST']);
    define("DEBUG", true);
}
else if(strpos($_SERVER['HTTP_HOST'], "angstrom") !== false) {
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT'] . "/agbi/");
    define("HTTP_ROOT", "https://" . $_SERVER['HTTP_HOST'] . "/agbi/");
    define("DEBUG", true);
}
else {
    define("ROOT_PATH", $_SERVER['DOCUMENT_ROOT']."/data-explorer/");
    define("HTTP_ROOT", "https://" . $_SERVER['HTTP_HOST']."/data-explorer/");
    define("DEBUG", false);
}

define("PAGE_TITLE", "Adult Gambling Behaviors Survey Data Explorer");
if(DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
else {
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //error_reporting(E_ERROR);
}

function include_styles() {
    $root = HTTP_ROOT;
    echo "
    <link rel='stylesheet' href='$root/css/app.css'>
    <link rel='stylesheet' href='//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css'>
    <script src='//code.jquery.com/jquery-1.10.2.min.js'></script>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js'></script>
    <script src='//code.jquery.com/ui/1.11.4/jquery-ui.min.js'></script>
    ";

    if(!DEBUG) {
        echo "<!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src=\"https://www.googletagmanager.com/gtag/js?id=UA-68365029-2\"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
        
          gtag('config', 'UA-68365029-2');
        </script>";
    }
}
function include_js() {
    $root = HTTP_ROOT;
    echo "<script src='$root/js/amcharts3/amcharts.js'></script>
        <script src='$root/js/amcharts3/serial.js'></script>
        <script src='$root/js/amcharts3/plugins/export/export.min.js'></script>
        <link href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css' rel='stylesheet'/>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.full.js'></script>
        <script src='$root/js/variableSelector.js'></script>
        <script src='$root/js/graph.js'></script>
        <script src='$root/js/datatable.js'></script>";
}

function include_header() {
    include ROOT_PATH."inc/navbar.php";
}
function include_footer() {
    include ROOT_PATH."inc/footer.php";
}
function echo_self() {
    echo htmlspecialchars($_SERVER["PHP_SELF"]);
}
function getCurrentYear(): int
{
    return 2022;
}
function getAllYears(): array
{
    return [2022];
}

/**
 * Get input and convert unassigned and empty string to null
 * @param string $key
 * @return mixed
 */
function getInput(string $key): mixed
{
    if(($_GET[$key] ?? null) == null || $_GET[$key] === '')
        return null;
    return $_GET[$key];
}