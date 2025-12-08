<?php
if (array_key_exists('directory', $_GET))
{
    $directory = $_GET['directory'];
}
else
{
    $directory = "2021 - 02- Luchon";
}
$rootdir = "images/phocagallery";
$parent = "";

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta charset='utf-8' />
</head>
<?php require "../../admin/helpers/jogallery.php" ;
     require "../../admin/helpers/jdirectory.php" ;



/*echo JOGalleryHelper::display(array("dir" => $directory,
                                   "rootdir" => $rootdir),
                                   "parent" => $parent));*/
?>