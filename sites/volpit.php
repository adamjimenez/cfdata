<?php
$url = 'https://www.volpit.com';

$xpaths = array(
	'heading' => '//h1',
	'copy' => '//article/p[2]',
	'target' => '//*[@class="stat-item target"]/span[2]',
	'total' => '//*[@class="stat-item invested"]/span[2]',
	'equity' => '//*[@class="stat-item equity"]/span[2]',
	//'funders' => '//*[@id="tabbed_info"]/div[1]/ul/li[5]/a/span',
	'image' => '//*[@class="business-logo"]/img',
	'video_url' => '//*[@id="volpitPlayer"]',
);

$html = file_get_contents($url);
//print $html; exit;

$doc = new DOMDocument();
$doc->loadHTML($html);

$links = array();
$node = $doc->getElementsByTagName('a');
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    if( preg_match('/pitches\//', $value, $matches) ){
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

	//$project['funders'] = $xpath->query($xpaths['funders'])->item(0)->nodeValue;

	if( $xpath->query($xpaths['video_url'])->item(0) ){
		$project['video_url'] = $xpath->query($xpaths['video_url'])->item(0)->getAttribute('src');
	}
	if($xpath->query($xpaths['image'])->item(0)){
		$project['image'] = $xpath->query($xpaths['image'])->item(0)->getAttribute('src');
	}

	$projects[] = $project;
}

//print_r($projects);exit;
?>