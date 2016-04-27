<?php
$url = 'https://www.thehousecrowd.com/';

$html = file_get_contents($url);
//print $html; exit;

$doc = new DOMDocument();
$doc->loadHTML($html);

$xpath = new DOMXpath($doc);
$node = $xpath->query('//*[@class="home-project-outer"]');

for($c = 0; $c<$node->length; $c++){
    //echo $doc->saveXML($node->item($c));
    $imageNode = $node->item($c)->firstChild->nextSibling;

	$project = array();
	$project['image'] = $imageNode->getAttribute('src');

	$headingNode = $imageNode->nextSibling->firstChild->nextSibling->firstChild;

    $project['heading'] = $headingNode->nodeValue;

    $totalNode = $headingNode->nextSibling;

    $project['total'] = $totalNode->nodeValue;
	$project['total'] = preg_replace("/[^0-9.]/", "", $project['total']);

    $copyNode = $imageNode->nextSibling->firstChild->nextSibling->nextSibling->nextSibling;

    $project['copy'] = $copyNode->nodeValue;

    $targetNode = $copyNode->nextSibling->nextSibling->nextSibling->nextSibling->firstChild->nextSibling->firstChild;
    $project['target'] = $targetNode->nodeValue;
	$project['target'] = preg_replace("/[^0-9.]/", "", $project['target']);

    $urlNode = $copyNode->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling->nextSibling;

	if (!$urlNode) {
		continue;
	}

	$project['url'] = $urlNode->getAttribute('href');
	$project['crowdfunding_type'] = 'Housing and Property';
	$project['crowdfunding_types'] = 2;
	$project['crowdfunding_category'] = 'Housing and Property';
	$project['pitch_type'] = 'Property';

	if( $project['total']>=$project['target'] ){
	    $project['active'] = 0;
	}

	$projects[] = $project;
}

//print_r($projects); exit;


?>