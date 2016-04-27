<?php
$url = 'https://www.investingzone.com';
$pitches_url = $url.'/pitches';

$xpaths = array(
	//'heading' => '//*[@id="top"]/div[1]/div/div/h1',
	'copy' => '//*[@id="pitch"]',
	//'target' => '//*[@id="top"]/div[1]/div/div/div[3]/div[1]/div[1]/p[1]/strong[2]',
	//'total' => '//*[@id="top"]/div[1]/div/div/div[3]/div[1]/div[1]/p[1]/strong[1]',
	'funders' => '//*[@href="#investors"]/span',
	'image' => '//div[@class="pitch-img"]/img',
);

$html = file_get_contents($pitches_url);

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');
//print $html;
$href = $prefix.$start;

for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    $class = $node->item($c)->getAttribute('class');

    if( preg_match('/pitch\//', $value, $matches) and !preg_match('/coming\-soon\//', $value, $matches) and $class == 'pitch-img' ){
    	//print $value."\n";

    	//get image
    	$image = '';
    	/*
    	if( $node->item($c)->firstChild->nextSibling ){
    	    $image = $url.($node->item($c)->firstChild->nextSibling->getAttribute('src'));
    	}*/
    	//print_r($image);

    	$heading = trim($node->item($c)->nextSibling->nextSibling->firstChild->nextSibling->textContent);
    	//print_r($name);

    	//get target
    	$target = $node->item($c)->nextSibling->nextSibling->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->firstChild->nextSibling->firstChild->nextSibling->firstChild->nextSibling->nextSibling->nodeValue;
	    $target = preg_replace("/[^0-9.]/", "", $target);
    	//print_r($target);

    	//get total
    	$total = $node->item($c)->nextSibling->nextSibling->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->firstChild->nextSibling->firstChild->nextSibling->firstChild->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nodeValue;
	    $total = preg_replace("/[^0-9.]/", "", $total);
		//print_r($total);

		//print $value."\n";
    	$html = file_get_contents($url.$value);

	    $doc = new DOMDocument();
	    $doc->loadHTML($html);

	    $xpath = new DOMXpath($doc);

	    $funders = $xpath->query($xpaths['funders'])->item(0)->nodeValue;

	    $copy = $xpath->query($xpaths['copy'])->item(0)->nodeValue;
		$copy = trim(str_replace('Business Description', '', $copy));

    	$project = array();
    	$project['url'] = $url.$value;
    	$project['heading'] = $heading;
    	$project['copy'] = $copy;
    	$project['target'] = $target;
    	//$project['start_date'] = $url;
    	//$project['end_date'] = $url;
    	$project['equity'] = $equity;
    	$project['total'] = $total;
    	$project['funders'] = $funders;


    	if($xpath->query($xpaths['image'])->item(0)){
    	    $image = $xpath->query($xpaths['image'])->item(0)->getAttribute('src');

    		$project['image'] = $url.$image;
    	}

    	//print_r($project); exit;

    	$projects[] = $project;

    	//exit;
    }
}
?>