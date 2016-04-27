<?php
$url = 'https://www.sharein.com';

$xpaths = array(
	'heading' => '//*[@id="wrap"]/div[2]/div/div/div[1]/table/tr/td[1]/h1/a',
	'copy' => '//*[@class="grab"]',
	'target' => '//*[@id="ft"]',
	'total' => '//*[@id="pt"]',
	'funders' => '//*[@class="investors"]/td[2]/h1',
	'equity' => '//*[@class="pitchOverview"]/tr[2]/td[2]/h1',
	//'image' => '//*[@id="campaign_logo"]/img',
	'video_url' => '//*[@id="vid-container"]/iframe',
);

$html = file_get_contents($url.'/pitches/index');
//print $html; exit;

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    if( preg_match('/\/invest\/pitch\//', $value, $matches) ){
		//print $url.$value."\n";
		//exit;

    	//get image
    	if( $node->item($c)->firstChild->nextSibling ){
    		$image = $node->item($c)->firstChild->nextSibling->getAttribute('src');
    	}
    	//

    	//print var_dump($html);

    	//$item = $node->item($c)->firstChild->nextSibling->getAttribute('src');
    	//echo $doc->saveXML($item);

    	$html = file_get_contents($url.$value);
	    $doc = new DOMDocument();
	    $doc->loadHTML($html);
	    $xpath = new DOMXpath($doc);

    	//$item = $xpath->query($xpaths['equity'])->item(0);
    	//echo $doc->saveXML($item);

    	$project = array();
    	$project['url'] = $url.$value;
    	$project['image'] = $image;
    	$project['heading'] = $xpath->query($xpaths['heading'])->item(0)->nodeValue;
    	$project['copy'] = $xpath->query($xpaths['copy'])->item(0)->nodeValue;
    	$project['target'] = $xpath->query($xpaths['target'])->item(0)->nodeValue;
    	//$project['start_date'] = $url;
    	//$project['end_date'] = $url;
    	$project['equity'] = $xpath->query($xpaths['equity'])->item(0)->nodeValue;
    	$project['total'] = $xpath->query($xpaths['total'])->item(0)->nodeValue;
    	$project['funders'] = $xpath->query($xpaths['funders'])->item(0)->nodeValue;
    	if( $xpath->query($xpaths['video_url'])->item(0) ){
    		$project['video_url'] = $xpath->query($xpaths['video_url'])->item(0)->getAttribute('src');
    	}

    	//print_r($project); exit;

    	$projects[] = $project;
    }
}

?>