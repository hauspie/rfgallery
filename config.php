<?

$PHOTOS_DIR = "photos";
$THUMBS_DIR = "thumbs";

$URL_BASE = preg_replace("/\?.*$/", "", $_SERVER['REQUEST_URI']);
$URL_BASE = preg_replace("/\/[^\/]*$/", "",$URL_BASE);

define(HOME_PAGE_NAME, "Photos");


?>