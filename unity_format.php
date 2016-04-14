<?php
$filesize = filesize($argv[1]);
$fp = fopen($argv[1], "r");
fseek($fp, $argv[3]|0);

$headerSize = min($argv[2], $filesize);
$header = fread($fp, $headerSize);
//$headerBytes = unpack("C*", $header);

$headerHex = unpack("H*", $header)[1];
$headerInt_32 = unpack("I*", $header);
$headerInt_16 = unpack("S*", $header);
$headerInt_08 = unpack("C*", $header);

$format = "Byte: %6d | %s | Int32: %10d | Int16: %5d %5d | Int08: %3d %3d %3d %3d | Char: %s%s%s%s\n";
for($i = 0; $i < ($headerSize/4); $i++) {
	$int32_i = $i + 1;
	$int16_i = $i * 2 + 1;
	$int08_i = $i * 4 + 1;
	$hex_i = $i * 8;
	printf($format,
		$int08_i - 1,
		substr($headerHex, $hex_i, 8),
		$headerInt_32[$int32_i], 
		$headerInt_16[$int16_i], 
		$headerInt_16[$int16_i + 1],
		$headerInt_08[$int08_i],
		$headerInt_08[$int08_i + 1],
		$headerInt_08[$int08_i + 2],
		$headerInt_08[$int08_i + 3],
		chr($headerInt_08[$int08_i]),
		chr($headerInt_08[$int08_i + 1]),
		chr($headerInt_08[$int08_i + 2]),
		chr($headerInt_08[$int08_i + 3])
	);
}
// 349552 / 16 = 21847
// 512 * 512 / 21847 = 12 
	
?>