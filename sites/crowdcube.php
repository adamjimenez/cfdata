<?php
$url = 'https://www.crowdcube.com/investments?sort_by=7&sort_by=0&q=&joined=3&i1=0&i2=0&i3=0&i4=0';

$xpaths = array(
	'heading' => '/html/body/div[1]/main/div[1]/div[1]/div[2]/div[1]/div[2]/div/h2',
	'copy' => '//*[@id="top"]/div[2]/div/p',
	'target' => '//*[@class="cc-pitch__col2a"]/div/dl[1]/dd[1]',
	'total' => '/html/body/div[1]/main/div[1]/div[1]/div[2]/div[2]/div[1]/div[2]/b',
	'funders' => '/html/body/div[1]/main/div[1]/div[1]/div[2]/div[2]/div[2]/div/div[1]/div/dl[2]/dd[1]',
	'data' => '//*[@id="top"]/div[3]/section/div/div[1]/ul',
	'image' => '/html/body/div[1]/main/div[1]/div[1]/div[2]/div[1]/div[1]/div/img',
	'days' => '//*[@class="cc-pitch__col2a"]/div/dl[1]/dd[3]',
	'equity' => '/html/body/div[1]/main/div[1]/div[1]/div[2]/div[2]/div[2]/div/div[1]/div/dl[1]/dd[2]',
);

$html = file_get_contents($url);

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');

$href = $prefix.$start;

$links = array();
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    if( preg_match('/investment\//', $value, $matches) ){
    	$links[] = $value;
    }
}

$links = array_unique($links);

//$links = array('https://www.crowdcube.com/investment/square-pie-bond-19666?ba=3604');

foreach($links as $value){
	//print $value."\n";

	$html = file_get_contents($value);

    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $xpath = new DOMXpath($doc);

    //$total = $xpath->query($xpaths['total'])->item(0)->nodeValue;
    //$total = preg_replace("/[^0-9.]/", "", $total);

    //$funders = $xpath->query($xpaths['funders'])->item(0)->nodeValue;
    //$funders = preg_replace("/[^0-9.]/", "", $funders);

    $heading = $xpath->query($xpaths['heading'])->item(0)->nodeValue;

    $copy = $xpath->query($xpaths['copy'])->item(0)->nodeValue;

    $copy = utf8_decode($copy);

    if( substr($copy, -4) == 'More' ){
    	$copy = substr($copy, 0, -4);
    }

    //echo $doc->saveXML($target);

    $children = $xpath->query($xpaths['data'])->item(0)->childNodes;
	foreach ($children as $child) {
        $values = explode(':', $child->nodeValue);

        $key = trim($values[0]);
        $val = trim($values[1]);

        if($key){
        	$data[$key] = $val;
        }
    }

    $days = $xpath->query($xpaths['days'])->item(0)->nodeValue;
    $days = preg_replace("/[^0-9.]/", "", $days);
    $end_date = date('Y-m-d', strtotime("+$days days"));

    $target = $xpath->query($xpaths['target'])->item(0)->nodeValue;
    $target = preg_replace("/[^0-9.]/", "", $target);

    $total = $xpath->query($xpaths['total'])->item(0)->nodeValue;
    $total = preg_replace("/[^0-9.]/", "", $total);

    $funders = $xpath->query($xpaths['funders'])->item(0)->nodeValue;
    $funders = preg_replace("/[^0-9.]/", "", $funders);

    //we don't have the start date yet
    //$popularity = $funders / $days;
    $popularity = $total / $target;

    //get image
    $image = '';
    if ($xpath->query($xpaths['image'])->item(0)) {
        $image = $xpath->query($xpaths['image'])->item(0)->getAttribute('src');
    }

    //print_r($image);

	$project = array();
	$project['url'] = $value;
	$project['image'] = 'http://'.$_SERVER['HTTP_HOST'].'/image.php?u='.rawurlencode($image);
    $pos = strrpos($project['image'], 'https:');
    $project['image'] = substr($project['image'], $pos);

	$project['heading'] = $heading;
	$project['copy'] = $copy;
	//$project['copy'] = 'Please watch the video for more information';

	$project['target'] = $target;
   	$project['equity'] = $xpath->query($xpaths['equity'])->item(0)->nodeValue;
	//$project['start_date'] = $url;
	$project['end_date'] = $end_date;
	$project['total'] = $total;
	$project['funders'] = $funders;

	//print_r($project); exit;

	$projects[] = $project;
	//exit;
}

//print_r($projects); exit;
?>