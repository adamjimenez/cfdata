<?php
$url = 'http://www.venturefounders.co.uk';
$pitches_url = $url.'/opportunities';

$xpaths = array(
	'heading' => '//h1',
	'copy' => '//*[@id="Overview"]/div/p[1]',
	'target' => '//*[@id="investmentStats"]/div/ul/li[2]/strong',
	'total' => '//*[@id="investmentStats"]/div/ul/li[1]/strong',
	'funders' => '//*[@class="chart-data"]/strong',
	'image' => '/html/body/main/article/header/div/div[1]/figure/img',
	'video' => '//*[@id="Overview"]/div/div/iframe',
	'equity' => '//*[@id="investmentStats"]/div/ul/li[3]/strong',
	'days' => '//time/span/strong',
);

$html = file_get_contents($pitches_url);

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');

$href = $prefix.$start;

$links = array();
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');

    if( strstr($value, '/opportunities/') ){
    	$links[] = $value;
    }
}

$links = array_unique($links);

//$links = array('/opportunities/rockabox');

//print_r($links);exit;

foreach($links as $value){
    //print $url.$value."\n";

	$html = file_get_contents($url.$value);

	//print $html; die;

    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $xpath = new DOMXpath($doc);

    $heading = $xpath->query($xpaths['heading'])->item(0)->nodeValue;

    if(!$heading){
        continue;
    }

	$project = array();
	
    //print_r($heading);exit;

    $copy = $xpath->query($xpaths['copy'])->item(0);
    $copy = $doc->saveXML($copy);

    //$copy = utf8_decode($copy);
    //print_r($copy); exit;

    $target = $xpath->query($xpaths['target'])->item(0)->nodeValue;
    $target = preg_replace("/[^0-9.]/", "", $target);
    //echo $doc->saveXML($target);
    //print_r($target);exit;


    //print $html; exit;

	if($xpath->query($xpaths['days']) ) {
    	$days = $xpath->query($xpaths['days'])->item(0)->nodeValue;
    	
    	if($days) {
        	$end_date = date('Y-m-d', strtotime("+$days days"));
    	}
	}
	
	if (!$days) {
		$project['finished'] = true;
	}

    //$days = $xpath->query($xpaths['days'])->item(0)->nodeValue;

    $total = $xpath->query($xpaths['total'])->item(0)->nodeValue;
    $total = preg_replace("/[^0-9.]/", "", $total);
    //echo $doc->saveXML($total);
    //print_r($total);exit;

    $equity = $xpath->query($xpaths['equity'])->item(0)->nodeValue;
    $equity = preg_replace("/[^0-9.]/", "", $equity);

    $funders = $xpath->query($xpaths['funders'])->item(0)->nodeValue;
    $funders = preg_replace("/[^0-9.]/", "", $funders);
    //echo $doc->saveXML($funders);
    //print_r($funders); exit;

    $image = '';
    if($xpath->query($xpaths['image'])->item(0)){
        $image = $xpath->query($xpaths['image'])->item(0)->getAttribute('src');
        //$image = $url.$image;
    }
    //echo $doc->saveXML($image);
    //print_r($image); exit;

    $video = '';
	if($xpath->query($xpaths['video'])->item(0)){
	    $video = trim($xpath->query($xpaths['video'])->item(0)->getAttribute('src'));
	}

    //we don't have the start date yet
    //$popularity = $funders / $days;
    $popularity = $total / $target;

    //print_r($image);
    $image = $image;

	$project['url'] = $url.$value;
	$project['image'] = $image;
	$project['heading'] = $heading;
	$project['copy'] = $copy;
	$project['target'] = $target;
	//$project['start_date'] = $url;
	$project['end_date'] = $end_date;
	$project['equity'] = $equity;
	$project['total'] = $total;
	$project['funders'] = $funders;
	$project['video_url'] = $video;

	//print_r($project); exit;

	$projects[] = $project;
}

//print_r($projects); exit;
?>