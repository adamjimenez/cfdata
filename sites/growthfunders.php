<?php
$url = 'https://www.growthfunders.com';

$xpaths = array(
	'heading' => '//*[@id="public-pitch"]/header/div/div[2]/div[1]/h1',
	'copy' => '//*[@id="overview"]/div/div[1]/article/div[1]/div/p[1]',
	'target' => '//*[@id="total"]/div[1]/span[2]',
	'total' => '//*[@id="total"]/div[1]/span[1]',
	'funders' => '//*[@id="total"]/div[7]/div',
	'equity' => '//*[@id="total"]/div[1]/span[2]/b',
	//'image' => '//*[@id="campaign_logo"]/img',
	'video_url' => '//*[@id="public-pitch"]/header/div/div[2]/div[2]/div[1]/iframe',
);

$html = file_get_contents($url.'/investments/');
//print $html; exit;

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    if( preg_match('/\/pitches\/view\//', $value, $matches) ){
		//print $value."\n";
		//exit;

    	//get image
    	if( $node->item($c)->parentNode->firstChild->nextSibling->firstChild->nextSibling ){
    		$image = $node->item($c)->parentNode->firstChild->nextSibling->firstChild->nextSibling->getAttribute('src');
    	}
    	//

    	$html = file_get_contents($value);
	    $doc = new DOMDocument();
	    $doc->loadHTML($html);
	    $xpath = new DOMXpath($doc);

    	//print var_dump($html);
    	//$item = $xpath->query($xpaths['funders'])->item(0);	echo $doc->saveXML($item);

    	$project = array();
    	$project['url'] = $value;
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

    	$project['target'] = str_replace($project['equity'], '', $project['target']);

    	//print_r($project); exit;

    	$projects[] = $project;
    }
}
//print_r($project); exit;
?>