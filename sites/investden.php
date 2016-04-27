<?php
$url = 'http://www.investden.com';
$pitches_url = $url.'/getAllPitchesFiltered';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $pitches_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($ch);
curl_close($ch);

$data = json_decode($json, true);
//print_r($data); exit;
foreach($data as $v) {
	$project = array();
	$project['url'] = 'http://www.investden.com/p/'.$v['pitch_short_url'];
	$project['image'] = $v['pitch_tombstone_image'];

	if(substr($project['image'], 0, 1)=='/') {
		$project['image'] = $url.$project['image'];
	}

	$project['heading'] = $v['pitch_name'];
	$project['summary'] = $v['pitch_summary'];
	$project['copy'] = $v['pitch_description'];
	$project['target'] = $v['pitch_target'];
	//$project['target_max'] = $v['targetMax'];
	//$project['start_date'] = $v['start_date'];
	$project['end_date'] = date('Y-m-d', strtotime($v['pitch_expiry']));
	$project['equity'] = $v['equity_offered'];
	$project['total'] = $v['current_amount'];
	$project['funders'] = $v['investors'];
	$project['eis'] = $v['eis'];
	$project['seis'] = $v['seis'];
	$project['crowdfunding_category'] = $v['pitch_industry'];
	$project['website_url'] = $v['website'];

	//$project['total_shares'] = $v['totalSharesOffered'];
	//$project['share_price'] = $v['sharePrice'];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url.'/getPitchDetail');
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
	curl_setopt($ch, CURLOPT_POST, 1);
    $post_data = json_encode($v, JSON_PRETTY_PRINT);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$json_detail = curl_exec($ch);
	curl_close($ch);

	$detail = json_decode($json_detail, true);
	//print_r($detail);

	//get video url
	$matches = array();
	preg_match('^\/\/player\.vimeo\.com\/video\/([0-9]*)^', $detail['pitch_video'], $matches);

	if($matches[0]) {
	    $project['video_url'] = 'http:'.$matches[0];
	}
	$project['twitter_url'] = $detail['twitter'];
	$project['facebook_url'] = $detail['facebook'];

	$projects[] = $project;
}

//print_r($projects);exit;