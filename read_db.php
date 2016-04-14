<?php
$fn = $argv[1];
$result = array();
$xml = new SimpleXMLElement(file_get_contents($fn));

foreach($xml->string as $string) {
	$result[(string) $string['name']] = urldecode((string) $string[0]);
	
	if ((string) $string['name'] == "MH_CACHE_RUNTIME_DATA_CURRENT_FLOOR_ENTER_DATA") {
		$d = substr(urldecode((string) $string[0]), 449);
		//echo $d;
		echo (string) $string['name']."\n";
		print_r(json_decode($d));
	} else {
		$d = substr(urldecode((string) $string[0]), 32);
		echo (string) $string['name']."\n";
		print_r(json_decode($d));
	}
	
	
}

foreach($xml->int as $string) {
	$result[(string) $string['name']] = (int) $string['value'];
}	

//print_r($result);
?>