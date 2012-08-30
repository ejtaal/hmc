#!/usr/bin/env php
<?

$filetoread = "all-restaurants.csv.geocoded.csv";
$records = file("$filetoread");
$json_out = fopen("$filetoread".".json", 'w');

$data = Array();


foreach( $records as $record) {
  $record = chop($record);
	print "-> $record\n";

	$fields = explode('|',$record);
	#var_dump( $fields);

	$hmc_lng = $fields[0];
	$hmc_lat = $fields[1];
	$hmc_prc = $fields[2];
	$hmc_type = $fields[3];
	$hmc_name = $fields[4];
	$hmc_tel  = $fields[5];
	$hmc_add  = $fields[6];
	$data['markers'][] = array(
		"latitude"  => $hmc_lat,
		"longitude" => $hmc_lng,
		"type"      => $hmc_type,
		"title"     => $hmc_name,
		"content"   => 
			"<b>$hmc_name</b> ($hmc_type)<br>"
		 ."Tel: <a href='tel:$hmc_tel'>$hmc_tel</a><br>"
		 ."SatNav: <a href='geo:0,0?q=$hmc_add'>$hmc_add</a>"
	);
	//$hmc_namenadd = "$hmc_name, $hmc_add, UK";
}

#var_dump( $data);
//echo json_encode( $data);
fwrite( $json_out, json_encode( $data));
?>
