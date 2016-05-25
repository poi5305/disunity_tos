<?php
$s = 4376;
$fn = "unity/51ef0d6f8cbcb77979fae770bb5d7907.unity3d";
$fp = fopen($fn, "r");
$filesize = filesize($fn);
fseek($fp, $s);

$i = 0;
while(!feof($fp)) {
	$num = unpack("I1num", fread($fp, 4))['num'];
	$blockSzie = unpack("I1size", fread($fp, 4))['size'];
	echo "$i";
	for ($b = 0; $b < $num; $b++) {
		$length = unpack("I1size", fread($fp, 4))['size'];
		if ($length == 0)
			continue;
		$str = unpack("Z{$length}string", fread($fp, $length))['string'];
		echo ", $str";
	}
	echo "\n";
	$i++;
}

	
?>