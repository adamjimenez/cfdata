<?php
$url = 'https://www.crowd2fund.com/';
$pitches_url = $url.'/investment-opportunities';

$xpaths = array(
	'heading' => '//*[@class="c2f-cam-ban-title"]/h1',
	'copy' => '//*[@class="lead"]',
	'target' => '//*[@class="c2f-cam-ban-stats"]/div/span/strong',
	'total' => '//*[@class="c2f-cam-ban-stats"]/div/div',
	'funders' => '//*[@class="c2f-cam-ban-stats"]/div[3]/strong',
	'image' => '//*[@class="c2f-cam-ban-img"]',
	'days' => '//*[@class="c2f-cam-ban-stats"]/div[2]/strong',
	'video_url' => '//iframe',
);

$html = file_get_contents($pitches_url);

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');

$href = $prefix.$start;

$links = array();
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    if( preg_match('/campaign\//', $value, $matches) ){
    	$links[] = $value;
    }
}

$links = array_unique($links);

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

    //echo $doc->saveXML($target);

    $total = $xpath->query($xpaths['total'])->item(0)->nodeValue;
    $target = $xpath->query($xpaths['target'])->item(0)->nodeValue;
    $funders = $xpath->query($xpaths['funders'])->item(0)->nodeValue;
    $target = $xpath->query($xpaths['target'])->item(0)->nodeValue;
    $days = $xpath->query($xpaths['days'])->item(0)->nodeValue;

    $end_date = date('Y-m-d', strtotime("+$days days"));

    //we don't have the start date yet
    //$popularity = $funders / $days;
    $popularity = $total / $target;

    //get image
    $image = '';
    if ($xpath->query($xpaths['image'])->item(0)) {
        $image = $xpath->query($xpaths['image'])->item(0)->getAttribute('style');

        $start = strpos($image, '(')+1;
        $length = strpos($image, ')')-$start;
        $image = substr($image, $start, $length);
    }

	$project = array();
	$project['url'] = $value;
	$project['image'] = $image;
	$project['heading'] = $heading;
	$project['copy'] = $copy;
	$project['target'] = $target;
	//$project['start_date'] = $url;
	$project['end_date'] = $end_date;
	$project['equity'] = $equity;
	$project['total'] = $total;
	$project['funders'] = $funders;

	if( $xpath->query($xpaths['video_url'])->item(0) ){
		$project['video_url'] = $xpath->query($xpaths['video_url'])->item(0)->getAttribute('src');
	}

	if(!$project['video_url']){
	    //$project['disabled'] = 1;
	}

	//print_r($project); exit;

	$projects[] = $project;
}
?>