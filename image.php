<?php
//get images
if ($_GET['u'] and substr($_GET['u'], 0, 4)==='http') {
	header("Content-type: image");
	print file_get_contents(str_replace(' ', '%20', $_GET['u']));
}else{
	die('not found');
}
?>