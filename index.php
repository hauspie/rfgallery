<?
require_once("tools.php");

PrintHead();

# Get current directory from GET variable
$Dir = $_GET["Dir"];
$Dir = preg_replace("/\/+/", "/", $Dir);
$DirUrl = "photos" . "$Dir";


# Get the file list
$files = get_files($DirUrl);


?>
<div id='content'>
<div id='title'><?
   echo dir_to_nav_links($Dir);
?></div>
<table class="dirs"><tr>
<?
$i = 0;
foreach ($files as $f)
{
    if (($i % 3) == 0 && $i != 0)
       echo "</tr><tr>\n";
    $thumb = get_thumbnail($f, $Dir, true);
    if ($thumb != nil)
    {
       echo "<td>" . $thumb . "</td>\n";
       $i++;
    }
}
?>
</tr>
</table>

<table class="yoxview"><tr>
<?
$files = get_files($DirUrl);
$i = 0;
foreach ($files as $f)
{
    if (($i % 3) == 0 && $i != 0)
       echo "</tr><tr>\n";
    $thumb = get_thumbnail($f, $Dir, false);
    if ($thumb != nil)
    {
       echo "<td>" . $thumb  . "</td>\n";
       $i++;
    }
}
?>
</tr>
</table>

</div>
<?
PrintFoot();
?>
