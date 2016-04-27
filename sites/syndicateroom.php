<?php
$url = 'https://www.syndicateroom.com';
$pitches_url = $url.'/public-available-investments.aspx';

$xpaths = array(
	'heading' => '/html/head/title',
	'copy' => '//*[@class="entrepeneurDescriptionT"]',
	'copy2' => '//*[@id="tabs-left-1"]/p[2]',
	'target' => '//*[@id="leftInvestment"]/div[1]/div[3]/div[4]/div[2]',
	'total' => '//*[@id="leftInvestment"]/div[1]/div[4]/div[2]/div[2]',
	'funders' => '//*[@id="leftInvestment"]/div[1]/div[2]/div[1]/div[3]/a[1]',
	'image' => '//*[@class="entrepreneurLogo"]/div/img',
	'video' => '//*[@id="tabs-left-1"]//iframe',
	'equity' => '//*[@id="leftInvestment"]/div[1]/div[3]/div[3]/div[2]',
	'days' => '//*[@id="countDown"]',
);

$html = file_get_contents($pitches_url);

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');

$href = $prefix.$start;

$links = array();
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    $class = $node->item($c)->getAttribute('class');

    if( $class == 'lnkDetails' ){
    	$pos = strpos($value, '?');
    	if($pos){
    		$value = substr($value, 0, $pos);
    	}

    	$links[] = $value;
    }
}

$links = array_unique($links);

foreach($links as $value){
    //print $url.$value."\n";

	$html = file_get_contents($url.$value);

	//print $html; die;

    $doc = new DOMDocument();
    $doc->loadHTML($html);

    $xpath = new DOMXpath($doc);

    $heading = $xpath->query($xpaths['heading'])->item(0)->nodeValue;
    $heading = str_replace('- NEW', '', $heading);
    //print_r($heading);exit;

    $copy = $xpath->query($xpaths['copy'])->item(0);
    $copy = $doc->saveXML($copy);

    if( stristr($copy, 'Liner') ){
        $copy = $xpath->query($xpaths['copy2'])->item(0);
        $copy = $doc->saveXML($copy);
    }


    //print_r($copy); exit;

    $target = $xpath->query($xpaths['target'])->item(0)->nodeValue;
    $target = preg_replace("/[^0-9.]/", "", $target);
    //echo $doc->saveXML($target);
    //print_r($target);exit;

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
        $image = $url.$image;
    }
    //echo $doc->saveXML($image);
    //print_r($image); exit;

    $video = '';
	if($xpath->query($xpaths['video'])->item(0)){
	    $video = trim($xpath->query($xpaths['video'])->item(0)->getAttribute('src'));
	}

    $days = $xpath->query($xpaths['days'])->item(0)->nodeValue;
    $days = preg_replace("/[^0-9.]/", "", $days);

    //we don't have the start date yet
    //$popularity = $funders / $days;
    $popularity = $total / $target;

    //print_r($image);
    $image = $image;

	$project = array();
	$project['url'] = $url.$value;
	$project['image'] = $image;
	$project['heading'] = $heading;
	$project['copy'] = $copy;
	//$project['copy'] = 'Please watch the video for more information';
	$project['target'] = $target;
	//$project['start_date'] = $url;
	//$project['end_date'] = $url;
	$project['days'] = $days;
	$project['equity'] = $equity;
	$project['total'] = $total;
	$project['funders'] = $funders;
	$project['video_url'] = $video;

	//print_r($project); exit;

	$projects[] = $project;
}

/*
print_r($projects);
exit;
*/
?>