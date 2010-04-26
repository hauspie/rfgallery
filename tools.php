<?

require_once("config.php");

$URL_BASE = preg_replace("/\?.*$/", "", $_SERVER['REQUEST_URI']);
$URL_BASE = preg_replace("/\/[^\/]*$/", "",$URL_BASE);



function PrintHead()
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
<head> 
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
  <script type="text/javascript" src="yoxview/yoxview-init.js"></script>
  <link rel="stylesheet" href="css/style.css" type="text/css" /> 
  <title><? echo HOME_PAGE_NAME ?></title>
  </head>
<body>
<div id='content'>
<?
}

function PrintFoot()
{
?>
</div>
</body>
</html>
<?
}


function debug($msg)
{
  echo "<p class=\"debug\">\n";
  echo $msg;
  echo "</p>\n";
}

function encode_filename($FileName)
{
   $enc = rawurlencode($FileName);
   $enc = preg_replace("/\/+/", "/", $enc);
   return preg_replace("/%2F/i", "/", $enc);
}

function get_files($Directory)
{
   $d = opendir($Directory);

   if (!$d)
      return false;

   $files = array();
   $files["dirs"] = array();
   $files["files"] = array();
   
   while (false !== ($file = readdir($d)))
   {
      if ($file[0] == '.')
	 continue;
      if (is_dir("$Directory/$file"))
	{
	  $files["dirs"][] = $file;
	}
      else if (strstr(strtolower($file), ".jpg"))
	$files["files"][] = $file;
   }
   sort($files["dirs"]);
   sort($files["files"]);
   closedir($d);
   return @$files;
}

function get_dir_thumbnail($dir)
{
  $d = opendir($dir);
  if ($d)
    {
      while (false !== ($f = readdir($d)))
	{
	  if (strstr(strtolower($f), ".jpg"))
	    return encode_filename("$dir/$f");
	  if (is_dir("$dir/$f") && $f[0] != ".")
	    return get_dir_thumbnail("$dir/$f");
	}
    }
  closedir($d);
  return nil;
}

function get_thumbnail($file, $dir)
{
   global $PHOTOS_DIR;
   global $URL_BASE;
   global $THUMBS_DIR;


   $thefile = encode_filename($file);
   $thedir = encode_filename($dir);


   if (strstr(strtolower($file), ".jpg"))
   {
      return "<a href=\"$URL_BASE/$PHOTOS_DIR/$thedir/$thefile\"><img alt=\"$file\" src=\"$URL_BASE/$THUMBS_DIR$thedir/$thefile\" /></a>";
   }
   else if (is_dir("$PHOTOS_DIR/$dir/$file"))
   {
     
     $dir_thumb =  get_dir_thumbnail("$THUMBS_DIR/$dir/$file");
     if ($dir_thumb != nil)
       $ret = "<p class=\"dir_thumb\"><a href=\"$URL_BASE/?Dir=$thedir/$thefile\"><img alt=\"$file\" src=\"" . $dir_thumb . "\" /></a></p>\n";
     else
       $ret = "";
   }
   
   $ret = $ret . "<p class=\"dir_link\"><a href=\"$URL_BASE/?Dir=$thedir/$thefile\">$file</a></p>";
   return $ret;
}

function dir_to_nav_links($dir)
{
  global $URL_BASE;
  
  $dirs = preg_replace("/\/\//", "/", $dirs);
  $dirs = preg_replace("/\/$/", "", $dirs);
  $dirs = preg_split("/\//", $dir);
  if ($dirs == nil)
    {
      return "<a href=\"$URL_BASE/\">Home</a>";
    }
  $link = "$URL_BASE/?Dir=/";
  $ret =  "<a href=\"$link\">" . HOME_PAGE_NAME . "</a>";
  foreach ($dirs as $dir)
    {
      $link = $link . encode_filename("$dir");
      if ($dir == "")
	continue;
      else
	$link = $link . "/";
      $ret = $ret . " / <a href=\"" . $link . "\">" . $dir . "</a>";
    }
  return $ret;
}

?>
