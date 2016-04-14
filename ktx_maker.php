<?php

$fn = "unity/c70f89d573f43cc6aaac9f35e0e7a409.unity3d"; 
$fp = fopen($fn, "r");
$filesize = filesize($fn);
$headerSize = 4456;
$contentSize = $filesize - $headerSize;
$header = fread($fp, $headerSize);
$data = fread($fp, $contentSize);
fclose($fp);


$w = 2048;
$h = 2048;
$size = 2097152;
$mipCount = 10;

$headerInt = array(
	"I*",
	1481919403,
	3140563232,
	169478669,
	67305985,
	0,
	1,
	0,
	37492,
	0,
	$w,
	$h,
	0,
	0,
	1,
	1,
	0,
	$size
);

$fileLength = (count($headerInt) - 1) * 4;

echo $fileLength;

$fp = fopen("a.ktx", "w");
fwrite($fp, array_pack($headerInt), $fileLength);
fwrite($fp, $data, $contentSize);
fclose($fp);


function array_pack(array $arr) {
	return call_user_func_array("pack", $arr);
}	
?>