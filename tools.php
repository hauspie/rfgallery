<?

$PHOTOS_DIR = "photos";


function PrintHead()
{
?>
<!DOCTYPE 
  HTML PUBLIC 
  "-//W3C//DTD HTML 4.01 Transitional//EN" 
  "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
      <title>Photos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
<script type="text/javascript" src="yoxview/yoxview-init.js"></script>
 <link rel="stylesheet" href="css/style.css" type="text/css" /> 
    </head>
<body>
<?
}

function PrintFoot()
{
?>
</body>
</html>
<?
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
   
   while (false !== ($file = readdir($d)))
   {
      if ($file[0] == '.')
	 continue;
      if (!is_dir("$Directory/$file"))
	 if (!strstr(strtolower($file), ".jpg"))
	    continue;
	 
      $files[] = $file;
   }
   sort($files);
   closedir($d);
   return @$files;
}

function get_thumbnail($file, $dir, $wish_dir)
{
   global $PHOTOS_DIR;

   $thefile = encode_filename($file);
   $thedir = encode_filename($dir);

   if (strstr(strtolower($file), ".jpg"))
   {
      if ($wish_dir)
	 return nil;
      return "<a href=\"/$PHOTOS_DIR$thedir/$thefile\"><img alt=\"$file\" src=\"/thumbs$thedir/$thefile\"></a>";
   }
   else if ($wish_dir)
   {
      $d = opendir("$PHOTOS_DIR/$dir/$file");
      if ($d)
      {
	 while (false !== ($f = readdir($d)))
	 {
	    if (strstr(strtolower($f), ".jpg"))
	    {
	       
	       $thef = encode_filename($f);
/* 	       $thef = preg_replace("/&/i", "%26", $f); */
/* 	       $thef = preg_replace("/#/i", "%23", $thef); */
/* 	       $thef = preg_replace("/\+/i", "%2B", $thef); */

	       $ret = "<a href=\"/?Dir=$thedir/$thefile\"><div class=\"folder\"><img alt=\"$file\" src=\"/thumbs$thedir/$thefile/$thef\"></a>";
	       break;
	    }
	 }
      }
   }
   else
      return nil;
   
   $ret = $ret . "<br /> <a href=\"/?Dir=$thedir/$thefile\">$file</a></div>";
   return $ret;
}

function dir_to_nav_links($dir)
{
   $dirs = preg_replace("/\/\//", "/", $dirs);
   $dirs = preg_replace("/\/$/", "", $dirs);
   $dirs = preg_split("/\//", $dir);
   if ($dirs == nil || count($dirs) <= 2)
   {
      return "<a href=\"/\">Home</a>";
   }
   $ret = "";
   $link = "/?Dir=/";
   foreach ($dirs as $dir)
   {
      $link = $link . encode_filename("$dir");
      if ($dir == "")
	 $dir = "Home";
      else
	 $link = $link . "/";
      $ret = $ret . " <a href=\"" . $link . "\">" . $dir . "</a> / ";
   }
   return $ret;
}

?>
