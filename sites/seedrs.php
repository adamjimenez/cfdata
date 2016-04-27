<?php
$url = 'https://www.seedrs.com';
$pitches_url = $url.'/invest';

$xpaths = array(
	'heading' => '//*[@class="public-page-content"]//h1',
	'copy' => '//*[@class="summary"]',
	'target' => '//*[@class="investment_sought"]/dd',
	'total' => '//*[@class="investment_already_funded"]/dd',
	'funders' => '//*[@id="tabbed_info"]/div[1]/ul/li[6]/a/span',
	'image' => '//*[@id="campaign_logo"]/img',
	'video_url' => '//*[@id="video-preview"]',
	'equity' => '//*[@class="equity_offered right"]/dd',
	'end_date' => '//*[@class="CampaignFundedStamp-date"]',
);

$html = file_get_contents($pitches_url);
$urls = array();

$doc = new DOMDocument();
$doc->loadHTML($html);
$xpath = new DOMXpath($doc);

$node = $doc->getElementsByTagName('article');

$days_arr;
for($c = 0; $c<$node->length; $c++){
    if ($xpath->query('.//*[@class="Card-link"]', $node->item($c))->item(0)) {
    	$page = $xpath->query('.//*[@class="Card-link"]', $node->item($c))->item(0)->getAttribute('href');
	    $progress = $xpath->query('.//*[@class="CampaignCard-progressMessage"]', $node->item($c))->item(0)->nodeValue;
	    
    	$urls[] = $url.$page;
	    preg_match('/([0-9]+) days/', $progress, $matches);
	    if ($matches) {
	    	$days_arr[$url.$page] = $matches[1];
	    }
    }
}

$xmlstr = file_get_contents($url.'/sitemap.xml');

$sitemap = new SimpleXMLElement($xmlstr);

foreach($sitemap->url as $sitemap_url) {
    $url = (string)$sitemap_url->loc;

    if( $sitemap_url->priority == 0.9 and strtotime($sitemap_url->lastmod)>strtotime('-1 month') ){
    	$urls[] = $url;
    }
}

$projects = array();
foreach($urls as $url) {
	$html = file_get_contents($url);
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $xpath = new DOMXpath($doc);

	$project = array();
	$project['url'] = $url;
	$project['heading'] = $xpath->query($xpaths['heading'])->item(0)->nodeValue;
	$project['copy'] = $xpath->query($xpaths['copy'])->item(0)->nodeValue;
	$project['target'] = $xpath->query($xpaths['target'])->item(0)->nodeValue;
	$project['equity'] = $xpath->query($xpaths['equity'])->item(0)->nodeValue;
	$project['total'] = $xpath->query($xpaths['total'])->item(0)->nodeValue;
	
	$pos = strpos($project['total'], 'for');
	if ($pos) {
		$project['total'] = substr($project['total'], 0, $pos);
	}
	
	$project['funders'] = $xpath->query($xpaths['funders'])->item(0)->nodeValue;
	if( $xpath->query($xpaths['video_url'])->item(0) ){
		$project['video_url'] = $xpath->query($xpaths['video_url'])->item(0)->getAttribute('src');
	}

	if ($xpath->query($xpaths['end_date'])->item(0)->nodeValue) {
	    $project['end_date'] = date('Y-m-d', strtotime($xpath->query($xpaths['end_date'])->item(0)->nodeValue));
    }

	if($xpath->query($xpaths['image'])->item(0)){
	    $project['image'] = $xpath->query($xpaths['image'])->item(0)->getAttribute('src');
	}

    if(!$project['target']){
        $project['active'] = 0;
    }
    
    if ($days_arr[$project['url']]) {
    	$project['days'] = $days_arr[$project['url']];
    }

	$projects[] = $project;
}
//print_r($projects); exit;
?>