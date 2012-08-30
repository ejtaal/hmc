#!/usr/bin/env php
<?

$filetoread = "all-restaurants.csv";
$addresses = file("$filetoread");
$fp = fopen("$filetoread".".geocoded.csv", 'w');
// First line needs to be something else than data for http://tomtom.gps-data-team.com/conversion.php to work
fwrite($fp, "long,lat,precision,type,name,tel,address\n");

foreach( $addresses as $record) {
  $record = chop($record);
	print "-> $record\n";

	$fields = explode('|',$record);
	//var_dump( $fields);
	$hmc_type = $fields[0];
	$hmc_name = $fields[1];
	$hmc_tel  = $fields[2];
	$hmc_add  = $fields[3];
	$hmc_namenadd = "$hmc_name, $hmc_add, UK";


	if ( $hmc_add == "") {
		print "| no address found in record\n";
		continue;
	}

	$data = googlemaps_geocode( $hmc_add); 
	//Check our Response code to ensure success
	if (substr($data,0,3) == "200") {
		$data = explode(",",$data);
		 
		$precision = $data[1];
		$latitude = $data[2];
		$longitude = $data[3];
		//echo "   Data: p: $precision, latlong: $latitude,$longitude\n";
		echo "CSV string: ";
		$csv_string = "$longitude|$latitude|$precision|";
		if ( $precision < 8 ) {
			print "LOOKUP INACCURATE, trying alternative: $hmc_namenadd\n";
			$alt_data = googlemaps_geocode( $hmc_namenadd);
			if (substr($alt_data,0,3) == "200") {
				$alt_data = explode(",",$alt_data);
				$alt_precision = $alt_data[1];
				$alt_latitude = $alt_data[2];
				$alt_longitude = $alt_data[3];
				if ( $alt_precision > $precision ) {
					print "SUCCESS, we found a better match for: $hmc_namenadd\n";
					//echo "   Data: p: $alt_precision, latlong: $alt_latitude,$alt_longitude\n";
					$csv_string = "$alt_longitude|$alt_latitude|$alt_precision|";
					if ( $alt_precision < 8 ) {
						print "STILL NOT accurate enough though, oh well...\n";
					}
				}
				else {
					print "FAILURE, we didn't find a better match for: $hmc_namenadd\n";
					//echo "   Data: p: $alt_precision, latlong: $alt_latitude,$alt_longitude\n";
				}
			} else {
				echo "   Error in geocoding! Http error ".substr($data,0,3);
				//echo "   Data: $data";
			}
		}
		$csv_string .= "$record";
		echo "$csv_string\n";
		fwrite($fp, "$csv_string\n");
	} else {
		echo "   Error in geocoding! Http error ".substr($data,0,3);
		//echo "   Data: $data";
	}
}
fclose($fp);

function googlemaps_geocode( $address_string) {

	//Three parts to the querystring: q is address, output is the format, key is the GAPI key
	$key = "ABQIAAAA3k-CV9VCzthlZ4sgB1fJURRqIj7gHhTKlMoqze-mQGgrP4Bt6hQb8UBWLLsl_XHAsIzNI9CdAK0A4A";
	//To avoid GMaps blocking us:
	sleep(1);

	$address = urlencode( "$address_string");
	 
	//If you want an extended data set, change the output to "xml" instead of csv
	$url = "http://maps.google.com/maps/geo?q=".$address."&output=csv&key=".$key;
	//Set up a CURL request, telling it not to spit back headers, and to throw out a user agent.
	$ch = curl_init();
	 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.5; rv:15.0) Gecko/20100101 Firefox/15.0");
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
	
}
?>
