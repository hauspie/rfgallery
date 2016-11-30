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

require_once("tools.php");
require_once("config.php");

PrintHead();

# Get current directory from GET variable
if (isset($_GET["Dir"]))
   $Dir = $_GET["Dir"];
else
   $Dir = "";
$Dir = preg_replace("/\/+/", "/", $Dir);
$DirUrl = $PHOTOS_DIR . "$Dir";


# Get the file list
$files = get_files($DirUrl);

if ($files == false)
  {
    error("Folder does not exist");
    PrintFoot();
    exit;
  }

?>
<div id='title'><?php
   echo dir_to_nav_links($Dir);
?>
</div>


<?php

if (count($files["dirs"]) > 0)
  {
    echo "<div class=\"dirs\">\n";
    $i = 0;
    foreach ($files["dirs"] as $d)
      {
	$thumb = get_thumbnail($d, $Dir);
	if ($thumb != false)
	  {
	    echo "<div class=\"thumb\">\n" . $thumb . "\n</div>\n";
	    $i++;
	  }
      }
    echo "</div>\n";
  }


if (count($files["files"]) > 0)
  {
    echo "<div class=\"yoxview\">\n";
    
    $i = 0;
    foreach ($files["files"] as $f)
      {
	$thumb = get_thumbnail($f, $Dir);
	if ($thumb != false)
	  {
	    echo "<div class=\"thumb\">\n" . $thumb  . "\n</div>\n";
	    $i++;
	  }
      }
    echo "</div>\n";
  }
?>

<?php
PrintFoot();
?>
