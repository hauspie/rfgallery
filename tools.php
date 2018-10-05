<?php
/* This file is part of rfGallery.
 * 
 * rfGallery is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version. 
 *  
 * rfGallery is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details. 
 *  
 * You should have received a copy of the GNU General Public License 
 * along with rfGallery.  If not, see <http://www.gnu.org/licenses/>. 
 *
 * Author: Michael Hauspie <mickey AT fairy-project DOT org>
 */

require_once("config.php");

$URL_BASE = preg_replace("/\?.*$/", "", $_SERVER['REQUEST_URI']);
$URL_BASE = preg_replace("/\/[^\/]*$/", "",$URL_BASE);

define('DEFAULT_ALBUM_THUMBNAIL', "pics/folder-photo2.png");

function PrintHead()
{
   ?>
   <html> 
   <head> 
   <meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
   <meta name="Generator" content="rfGallery. https://github.com/hauspie/rfgallery" />
   <script type="text/javascript" src="yoxview/yoxview-init.js"></script>
   <link rel="stylesheet" href="css/style.css" type="text/css" /> 
   <title><?php echo HOME_PAGE_NAME ?></title>
   </head>
   <body>
   <div id='content'>
<?php
}

function PrintFoot()
{
?>
<p id="copyright">
    <?php echo FOOTER_MESSAGE?>
</p>
</div>
</body>
</html>
<?php
}


function debug($msg)
{
  echo "<p class=\"debug\">\n";
  echo $msg;
  echo "\n</p>\n";
}

function error($msg)
{
  echo "<p class=\"error\">\n";
  echo "Software Failure. $msg <br />";
  echo "Guru meditation\n";
  echo "\n</p>\n";
}


function encode_filename($FileName)
{
   $enc = rawurlencode($FileName);
   $enc = preg_replace("/\/+/", "/", $enc);
   return preg_replace("/%2F/i", "/", $enc);
}

function is_image($FileName)
{
   if (strstr(strtolower($FileName), ".jpg"))
      return true;
   return false;
}

function is_video($FileName)
{
   if (strstr(strtolower($FileName), ".mp4"))
      return true;
   return false;
}


function is_accepted_file($FileName)
{
   return is_image($FileName) || is_video($FileName);
}

function get_files($Directory)
{
  
  if (!is_dir($Directory))
    return false;

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
      else if (is_accepted_file($file))
         $files["files"][] = $file;
   }
   rsort($files["dirs"]);
   sort($files["files"]);
   closedir($d);
   return @$files;
}


function get_dir_thumbnail_recursive($dir, $depth)
{
  global $ALBUM_THUMBNAIL_MAX_DEPTH;

  if ($ALBUM_THUMBNAIL_MAX_DEPTH - $depth <= -1)
    return DEFAULT_ALBUM_THUMBNAIL;

  $d = opendir($dir);
  if ($d)
  {
     while (false !== ($f = readdir($d)))
     {
        if (strstr(strtolower($f), ".jpg"))
           return encode_filename("$dir/$f");
        if (is_dir("$dir/$f") && $f[0] != ".")
	    {
           if ( $ALBUM_THUMBNAIL_MAX_DEPTH - $depth > -1)
              return get_dir_thumbnail_recursive("$dir/$f",$depth + 1);
	    }
     }
  }
  closedir($d);
  return DEFAULT_ALBUM_THUMBNAIL;
}

function get_dir_thumbnail($dir)
{
  return get_dir_thumbnail_recursive($dir, 1);
}

function create_folders($path)
{
   /* Creates all needed folders so that the folder $path exists */
   return mkdir($path, 0777, TRUE);
}

function check_and_generate_thumbnail($file, $dir)
{
   global $PHOTOS_DIR;
   global $THUMBS_DIR;
   global $AUTO_THUMB_HEIGHT;
   global $AUTO_THUMB_WIDTH;
   global $AUTO_THUMB_FILTER;

   /* This function checks if $file exists and if not, generate a
    * thumbnail using imagick */
   $thumb_dir  = "$THUMBS_DIR/$dir";
   $thumb_path = "$thumb_dir/$file";
   /* First create folders if needed */
   if (!is_dir($thumb_dir))
   {
      if (!create_folders($thumb_dir))
         return;
   }
   if (is_file($thumb_path))
      return;
   /* file does not exists (or at least is not a regular file) generate thumbnail */
   $thumb = new Imagick("$PHOTOS_DIR/$dir/$file");
   if (!$thumb)
      return;

   $width = $thumb->getImageWidth();
   $height = $thumb->getImageHeight();
   $ratio = $width / $height;

   
   $new_width = $new_height = 1;
   // Landscape
   if ($ratio > 1)
   {
      $new_width = $AUTO_THUMB_WIDTH;
      $new_height = $new_width / $ratio;
      // If ratio is not adapted to the defined thumbnail bounding box, we have to adapt
      if ($new_height > $AUTO_THUMB_HEIGHT)
      {
         $new_height = $AUTO_THUMB_HEIGHT;
         $new_width =  $ratio * $new_height;
      }
   }
   // Portrait 
   if ($ratio < 1)
   {
      $new_height = $AUTO_THUMB_HEIGHT;
      $new_width = $new_height * $ratio;
      // If ratio is not adapted to the defined thumbnail bounding box, we have to adapt
      if ($new_width > $AUTO_THUMB_WIDTH)
      {
         $new_width = $AUTO_THUMB_WIDTH;
         $new_height = $new_width / $ratio;
      }
   }
   $thumb->resizeImage($new_width,$new_height, $AUTO_THUMB_FILTER,1);
      
   $thumb->writeImage($thumb_path);
   $thumb->destroy();
}

function get_thumbnail($file, $dir)
{
   global $PHOTOS_DIR;
   global $URL_BASE;
   global $THUMBS_DIR;

   $thefile = encode_filename($file);
   $thedir = encode_filename($dir);

   $ret = "";

   
   if (is_image($file))
   {
      check_and_generate_thumbnail($file, $dir);
      return "<a href=\"$URL_BASE/$PHOTOS_DIR/$thedir/$thefile\"><img alt=\"$file\" src=\"$URL_BASE/$THUMBS_DIR$thedir/$thefile\" /></a>";
   }
   else if (is_video($file))
   {
      return "<video width=\"380\" controls> <source src=\"$URL_BASE/$PHOTOS_DIR/$thedir/$thefile\" type=\"video/mp4\" /> </video>";
   }
   else if (is_dir("$PHOTOS_DIR/$dir/$file"))
   {
     
     $dir_thumb =  get_dir_thumbnail("$THUMBS_DIR/$dir/$file");
     if ($dir_thumb != false)
       $ret = "<p class=\"dir_thumb\"><a href=\"$URL_BASE/?Dir=$thedir/$thefile\"><img alt=\"$file\" src=\"" . $dir_thumb . "\" /></a></p>\n";
     else
       $ret = "nirf";
   }
   $ret = $ret . "<p class=\"dir_link\"><a href=\"$URL_BASE/?Dir=$thedir/$thefile\">$file</a></p>";
   return $ret;
}

function dir_to_nav_links($dir)
{
  global $URL_BASE;
  
  $dirs = preg_replace("/\/\//", "/", $dir);
  $dirs = preg_replace("/\/$/", "", $dirs);
  $dirs = preg_split("/\//", $dirs);
  if ($dirs == false)
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
