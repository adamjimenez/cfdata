<?php
$crowdfunding_site = 22;

$url = 'https://www.squareknot.co.uk';
$pitches_url = $url.'/pitches';

$xpaths = array(
	'heading' => '//*[@id="pitch"]/div[2]/h1',
	'copy' => '//*[@id="pitch_left"]/div[2]',
	'target' => '//*[@id="pitch_right"]/div[2]/div[2]/div[2]/div[2]',
	'total' => '//*[@id="pitch_right"]/div[2]/div[2]/div[3]/div[2]',
	'funders' => '//*[@id="pitch_right"]/div[2]/div[2]/div[4]/div[2]',
	'equity' => '//*[@id="pitch_right"]/div[2]/div[2]/div[1]/div[2]',
	'image' => '//*[@id="pitch_picture"]/img',
	'video_url' => '//*[@id="share"]/div[1]/div/div',
);

$html = file_get_contents($pitches_url);
//print var_dump($html); exit;
$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');

$href = $prefix.$start;

$links = array();
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');

	//print $value."\n";

    if( preg_match('/pitch\//', $value, $matches) ){
    	$links[] = $value;
    }
}

$links = array_unique($links);

foreach($links as $value){
    //print $url.$value."\n";

	$html = file_get_contents($url.$value);
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $xpath = new DOMXpath($doc);

	//print $html; exit;

	//echo $doc->saveXML($xpath->query($xpaths['target'])->item(0));

	$project = array();
	$project['url'] = $url.$value;
	$project['heading'] = $xpath->query($xpaths['heading'])->item(0)->nodeValue;
	$project['copy'] = $xpath->query($xpaths['copy'])->item(0)->nodeValue;
	$project['target'] = $xpath->query($xpaths['target'])->item(0)->nodeValue;
	//$project['start_date'] = $url;
	//$project['end_date'] = $url;
	$project['equity'] = $xpath->query($xpaths['equity'])->item(0)->nodeValue;

	$project['total'] = $xpath->query($xpaths['total'])->item(0)->nodeValue;
	preg_match('/£[^\s]*/', $project['total'], $matches);
	$project['total'] = $matches[0];

	$project['funders'] = $xpath->query($xpaths['funders'])->item(0)->nodeValue;

	if( $xpath->query($xpaths['video_url'])->item(0) ){
		$project['video_url'] = $xpath->query($xpaths['video_url'])->item(0)->getAttribute('data-video');
	}
	if($xpath->query($xpaths['image'])->item(0)){
		$project['image'] = $url.str_replace(' ', '%20', $xpath->query($xpaths['image'])->item(0)->getAttribute('src'));
	}

	if(strstr($html, 'No more investments')){
		$project['finished'] = true;
	}

	$projects[] = $project;
}
?>