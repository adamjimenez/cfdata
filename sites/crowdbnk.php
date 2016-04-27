<?php
$url = 'https://www.crowdbnk.com';

$xpaths = array(
	'heading' => '//h1',
	'copy' => '//*[@class="emphasis"]',
	'target' => '//*[@class="highlights"]/table/tr[3]/td[2]',
	'total' => '//*[@class="highlights"]/table/tr[1]/td[2]/span',
	'funders' => '//*[@id="investors"]',
	'equity' => '//*[@class="highlights"]/table/tr[5]/td[2]',
	'image' => '/html/body/div[3]/div[1]/section[1]/img',
	//'video_url' => '//*[@id="video-preview"]',
);

$html = file_get_contents($url.'/portfolio');
//print $html; exit;

$doc = new DOMDocument();
$doc->loadHTML($html);

$node = $doc->getElementsByTagName('a');
for($c = 0; $c<$node->length; $c++){
    $value = $node->item($c)->getAttribute('href');
    //print $value."\n";

    if( preg_match('/p\//', $value, $matches) ){
		//print $url.$value."\n";
		//exit;

    	$project = array();

    	//get image
    	$style = $node->item($c)->nextSibling->nextSibling->nextSibling->nextSibling->getAttribute('style');
    	//echo $doc->saveXML($image);

    	//print $style;
    	preg_match('/"(.*)"/', $style, $matches);

    	$project['image'] = $url.$matches[1];
//$value = '/p/spiral';

    	$html = file_get_contents($url.$value);
	    $doc = new DOMDocument();
	    $doc->loadHTML($html);
	    $xpath = new DOMXpath($doc);

	    //remove script tags
	    $script = $doc->getElementsByTagName('script');

        $remove = [];
        foreach($script as $item)
        {
          $remove[] = $item;
        }

        foreach ($remove as $item)
        {
          $item->parentNode->removeChild($item);
        }

    	//get data table
    	$data = array();
    	$trs = $xpath->query('//*[@class="highlights"]/table/tr');
    	for($d = 0; $d<$trs->length; $d++){
    		$tr = $trs->item($d);

    		$key = trim($tr->childNodes->item(0)->nodeValue);
    		$val = trim($tr->childNodes->item(2)->nodeValue);

    		if(strstr($key, 'Investors')) {
    			$val = preg_replace("/[^0-9.]/", "", $key);
    			$key = 'Investors';
    		}

    		$data[$key] = $val;
    	}

    	//print $html; exit;

    	//echo $doc->saveXML($xpath->query($xpaths['target'])->item(0));

    	$project['url'] = $url.$value;
    	$project['heading'] = $xpath->query($xpaths['heading'])->item(0)->nodeValue;
    	$project['copy'] = trim(strip_tags($xpath->query($xpaths['copy'])->item(0)->nodeValue));
    	$project['target'] = $data['Target'];
    	//$project['start_date'] = $url;
    	//$project['end_date'] = $url;
    	$project['equity'] = $data['Equity on Offer'];
    	$project['total'] = $data['Raised'];
    	$project['funders'] = $data['Investors'];
    	$project['days'] = $data['Days to go'] ?: 0;
    	
    	$pos = strpos($project['days'], 'on');
    	if($pos){
    		$project['end_date'] = date('Y-m-d', strtotime(substr($project['days'], $pos+3)));
    		unset($project['days']);
    	}

    	//get video url
    	$matches = array();
    	preg_match('^\/\/www\.youtube\.com\/watch\?v=([0-9a-zA-Z_]*)^', $html, $matches);

    	if($matches[0]) {
    	    $project['video_url'] = 'http:'.$matches[0];
    	}

    	/*
    	if( $xpath->query($xpaths['video_url'])->item(0) ){
    		$project['video_url'] = $xpath->query($xpaths['video_url'])->item(0)->getAttribute('src');
    	}
    	*/
    	/*
    	if($xpath->query($xpaths['image'])->item(0)){
    		$project['image'] = $url.$xpath->query($xpaths['image'])->item(0)->getAttribute('src');
    	}*/

    	//print_r($project); exit;

    	$projects[] = $project;
    }
}

//print_r($projects);exit;
?>