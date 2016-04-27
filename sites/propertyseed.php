<?
//$crowdfunding_site = 2;

$url = 'http://www.propertyseed.co.uk/';
$data = json_decode(file_get_contents($url.'discover/all.json'), true);


foreach($data as $v) {

    //print_r($v);exit;

    if($v['category']==='demo'){
        continue;
    }

	$project = array();
	$project['url'] = 'http://'.$v['url'];
	$project['image'] = $v['video_image'];
	$project['heading'] = $v['name'];
	$project['summary'] = $v['headline'];
	$project['address'] = $v['location_data']['name'];
	$project['copy'] = $v['about_html'];
	$project['target'] = $v['goal'];
	//$project['start_date'] = $v['start_date'];
	if(!$project['never_ending']){
	    $project['end_date'] = $v['expires_at'];
	}
	//$project['equity'] = $v['equity_offered'];
	$project['total'] = $v['pledged'];
	//$project['funders'] = $v['funders'];
	$project['location'] = $v['location_data']['lat'].' '.$v['location_data']['lng'];
	//$project['yield'] = $v['yield'];
	$project['crowdfunding_type'] = 'Housing and Property';
	$project['crowdfunding_types'] = 2;
	$project['crowdfunding_category'] = 'Housing and Property';
	$project['pitch_type'] = 'Property';
	$project['video_url'] = $v['video_url'];
	$project['currency'] = 'units';

	//print_r($project); exit;

	$projects[] = $project;
}
?>