<?php
$url = 'https://www.fundingtree.co.uk';

/*
$fields = array(
	'_method' => 'POST',
	'per-page' => 12,
	'data' => array(
		'Pitch'	=> array(
			'search' => '',
			'filter' => '',
			'investment_type' => array(
				0 => 0,
				1 => 0,
			),
			'investment_required_min' => 0,
			'investment_required_max' => 375000,
			'investment_raised_min' => 0,
			'investment_raised_max' => 40000,
			'investors_min' => 0,
			'investors_max' => 200,
		)
	)

);*/

//$page = '/pitches/get_pitches/0/12.json?_method=POST&pe%E2%80%A6BBusinessSector%5D%5Bf3d9feda-e96a-11e2-bc2d-b8763f7237b2%5D=0&per-page=12';

$page = '/pitches/get_pitches/0/12.json?_method=POST&per-page=12&data%5BPitch%5D%5Bsearch%5D=&data%5BPitch%5D%5Bfilter%5D=&data%5BPitch%5D%5Binvestment_type%5D%5B0%5D=0&data%5BPitch%5D%5Binvestment_type%5D%5B1%5D=0&data%5BPitch%5D%5Binvestment_required_min%5D=0&data%5BPitch%5D%5Binvestment_required_max%5D=450000&data%5BPitch%5D%5Binvestment_raised_min%5D=0&data%5BPitch%5D%5Binvestment_raised_max%5D=125000&data%5BPitch%5D%5Binvestors_min%5D=0&data%5BPitch%5D%5Binvestors_max%5D=200';

$html = file_get_contents($url.$page);
//die($url.'/pitches/get_pitches/0/12');
//print $html; exit;

$data = json_decode($html, true);

//print_r($data);
foreach($data['pitches'] as $pitch){
	$project = array();
	$project['url'] = $url.'/pitches/view_pitch/'.strtolower($pitch['Pitch']['upn']);
	$project['heading'] = $pitch['Pitch']['company_name'];
	$project['copy'] = $pitch['Pitch']['company_profile'];
	$project['target'] = $pitch['Pitch']['required_funding'];
	$project['start_date'] = substr($pitch['Pitch']['created'], 0, 10);
	$project['end_date'] = $pitch['Pitch']['expiry_date'];
	$project['equity'] = $pitch['EquityPitch']['equity_offered'];
	$project['total'] = $pitch['Pitch']['invested']['current_amount'];
	$project['funders'] = $pitch['Pitch']['investors'];
	$project['image'] = $pitch['Pitch']['logosrc'];

	//exclude loans
	if( $pitch['investment_type_text']==='Loan' ){
	    continue;
	}

    //print_r($project);exit;

	$projects[] = $project;
}
?>