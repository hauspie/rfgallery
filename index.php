<?
require_once("tools.php");
require_once("config.php");

PrintHead();

# Get current directory from GET variable
$Dir = $_GET["Dir"];
$Dir = preg_replace("/\/+/", "/", $Dir);
$DirUrl = $PHOTOS_DIR . "$Dir";


# Get the file list
$files = get_files($DirUrl);



?>
<div id='title'><?
   echo dir_to_nav_links($Dir);
?>
</div>


<?

if (count($files["dirs"]) > 0)
  {
    echo "<div class=\"dirs\">\n";
    $i = 0;
    foreach ($files["dirs"] as $d)
      {
	$thumb = get_thumbnail($d, $Dir);
	if ($thumb != nil)
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
	if ($thumb != nil)
	  {
	    echo "<div class=\"thumb\">\n" . $thumb  . "\n</div>\n";
	    $i++;
	  }
      }
    echo "</div>\n";
  }
?>

<?
PrintFoot();
?>
