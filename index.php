<?
$files = scandir(dirname(__FILE__).'/sites/');

if($_GET['p']) {
	if (in_array($_GET['p'], $files)) {
        header("Content-Type: text/plain");
		$projects = array();
		require(dirname(__FILE__).'/sites/'.$_GET['p']);
        print json_encode($projects);
	} else {
		print 'File not found';
	}
} else {
?>
<h1>Crowdfunding data</h1>
<ul>
    <?
    foreach($files as $file) {
        if ($file === '.' or $file === '..') {
            continue;
        }
    ?>
        <li><a href="?p=<?=$file;?>"><?=$file;?></a></li>
    <?
    }
    ?>
</ul>
<?
}
?>