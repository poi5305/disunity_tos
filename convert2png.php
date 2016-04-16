<?php
// Author: Andy (Min-Te, Chou)

// global parameters
$BIN_DETEX = "detex/detex-convert";
$BIN_PVRTC = "decompress-pvrtc/decompress";
$OUT_PATH = "png2";
// unity file will move to DONE_PATH if convert to png success, make sure done path is created
$DONE_PATH = "done";

// ---------------Header------------------
// unity header format (to fetch unity version)
$unity3dFormat = "Z:*:format/m/C/Z:*:version/Z:*:version2/m/I:9/Z:*:bundleName";

// image header format (fetch image width, height, size and image format)
// different unity version has different format
$v3Format_p1 = "I:1:_size/z:*:path/m/I:13:f/I:1:_size/z:*:name/m/I:1:width/I:1:height/I:1:imageSize/I:1:imageType/I:9:s/I:1:checkSize";
$v3Format_p2 = "I:1:_size/z:*:path/m/I:14:f/I:1:_size/z:*:name/m/I:1:width/I:1:height/I:1:imageSize/I:1:imageType/I:9:s/I:1:checkSize";
$v52Format_p1 = "I:1:_size/z:*:path/m/I:14:f/I:1:_size/z:*:name/m/I:1:width/I:1:height/I:1:imageSize/I:1:imageType/I:10:s/I:1:checkSize";
$v52Format_p2 = "I:1:_size/z:*:path/m/I:15:f/I:1:_size/z:*:name/m/I:1:width/I:1:height/I:1:imageSize/I:1:imageType/I:10:s/I:1:checkSize";
$v53Format_p3 = "I:1:_size/z:*:name/m/I:1:width/I:1:height/I:1:imageSize/I:1:imageType/I:10:s/I:1:checkSize";

// ---------------Runner------------------

if ($argc < 2) {
	die("usage: php convert2png.php filename\n");
}

@mkdir($OUT_PATH);
@mkdir($DONE_PATH);

$fn = $argv[1];
if (!file_exists($fn)) {
	die("Error! unity file $fn not exist!\n");
}
$fp = fopen($fn, "r");

// to extract unity header for version
// 100 size is enough to extract unity version
$unity3dHeader = fread($fp, 100);
$d = unpack2($unity3dFormat, $unity3dHeader);
$version = $d['version'];
$bundleName = $d['bundleName'];

// parse image header with different unity version
// 4236, 4244, 4096 are magic numbers for image header position
// use binary_reader.php to find these image header position 
$tmps = array();
if ($version == '3.x.x') {
	fseek($fp, 4236);
	$tmps = unpack2fp($v3Format_p1, $fp);
	if ($tmps['imageSize'] != $tmps['checkSize']) {
		fseek($fp, 4236);
		$tmps = unpack2fp($v3Format_p2, $fp);
	}
} else if ($version == '5.x.x') {
	fseek($fp, 4244);
	$tmps = unpack2fp($v5Format_p1, $fp);
	if ($tmps['imageSize'] != $tmps['checkSize']) {
		fseek($fp, 4244);
		$tmps = unpack2fp($v5Format_p2, $fp);
	}
} else {
	// maybe version 5.3.1p4 or die
	$version = "5.3.1p4";
	fseek($fp, 4096);
	$tmps = unpack2fp($v5Format_p3, $fp);
}

if ($tmps['imageSize'] != $tmps['checkSize']) {
	die("Convert $fn error! version: $version, imageSize != checkSize\n");
}

// image data include unity image header
$data = fread($fp, filesize($fn) - ftell($fp));
fclose($fp);

convertToPNG($data, $tmps['imageType'], $tmps['width'], $tmps['height'], $tmps['imageSize'], $tmps['name']);

$name = $tmps['name'];
if (file_exists("$OUT_PATH/$name.png")) {
	rename($fn, "$DONE_PATH/$fn");
	echo "Convert success! $OUT_PATH/$name.png Unity file $fn move to done path\n";	
} else {
	echo "Convert failure! Unity file $fn can not convert to png\n";
}

exit();

// ---------------RunEnd------------------

// convert rawdata to png file with different types
// if type is RGBA4444 using makePng function. 
// if type is PVRTC_RGBA4 using decompress-pvrtc open source https://github.com/tlozoot/decompress-pvrtc
// other types are using detex open source https://github.com/hglm/detex
function convertToPNG(&$data, $type, $w, $h, $s, $name) {
	global $BIN_DETEX;
	global $BIN_PVRTC;
	global $OUT_PATH;
	
	$header = makeKtxHeader($type, $w, $h);
	$size = $header[17];
	echo "$type $w $h $s $size $name.ktx $name.png\n";
	if ($s < $size) {
		die("Error! Size. $s $size");
	}
	if ($type == 13) { // RGBA4444
		makePng($data, $w, $h, $size, "$OUT_PATH/$name.png");
	} else if ($type == 33) { // PVRTC_RGBA4
		// create pvrtc rgba4444 rawdata file and use decompress-pvrtc to convert to png file
		$fpKtx = fopen("$name.ktx", "w");
		fwrite($fpKtx, $data, $s);
		fclose($fpKtx);
		echo shell_exec("$BIN_PVRTC $w $h $name.ktx $OUT_PATH/$name.png");
	} else { // see makeKtxHeader()
		// create rawdata file with ktx header and use detex to convert to png file
		$headerLength = (count($header) - 1) * 4;
		$fpKtx = fopen("$name.ktx", "w");
		fwrite($fpKtx, array_pack($header), $headerLength);
		fwrite($fpKtx, $data, $size);
		fclose($fpKtx);
		if ($type == 1) { // only alpha value image file
			echo shell_exec("$BIN_DETEX -o A8 $name.ktx $OUT_PATH/$name.png");	
		} else {
			echo shell_exec("$BIN_DETEX -o RGBA8 $name.ktx $OUT_PATH/$name.png");
		}
		unlink("$name.ktx");
	}
}

// make png image from rawdata with format RGBA4444
function makePng(&$d, $w, $h, $s, $name) {
	$data = unpack("C*", $d);
	$image = imagecreatetruecolor($w, $h);
	imagealphablending($image, false);
    imagesavealpha($image, true);
	$i = 0;
	for ($c = 1; $c <= $s; $c+=2, $i++) {
		$x = $i % $w;
		$y = (int) ($i / $w);
	
		$c1 = $data[$c + 1];
		$c2 = $data[$c];
		$r = ($c1 >> 4) << 4;
		$g = ($c1 & 0xF) << 4;
		$b = ($c2 >> 4) << 4;
		$a = ($c2 & 0xF) << 4;
		$a = (256 - $a) / 2 - 1;
		
		$color = imagecolorallocatealpha($image , $r , $g , $b, $a);
		imagesetpixel($image , $x , $y , $color);
	}
	imagepng($image, $name);
	imagedestroy($image);
}

// make .ktx header for detex reading
// $t unity image format enum
// $w image width
// $h image height
// @see unity_image_format.txt for $t
// @see detex/file-info.c for $type4, $type6 and $type7
function makeKtxHeader($t, $w, $h) {
	$type4 = 0;
	$type6 = 0;
	$type7 = 0;
	$s = 0;
	switch($t) {
	case 1:
		$type4 = 0x1401; // type
		$type6 = 0x1906; // format
		$type7 = 0x1906; // internalFormat
		$s = $w * $h;
		break;
	case 4:
		$type4 = 0x1401; // type
		$type6 = 0x1908; // format
		$type7 = 0x1908; // internalFormat
		$s = $w * $h * 4;
		break;
	case 13: // RGBA4444
		$type4 = 0x1403; // type
		$type6 = 0x8227; // format
		$type7 = 0x805B; // internalFormat
		$s = $w * $h * 2;
		break;
	case 34: // ETC2
		$type4 = 0;
		$type6 = 0;
		$type7 = 37492;
		$s = $w * $h / 2;
		break;
	case 47: // ETC2_EAC
		$type4 = 0;
		$type6 = 0;
		$type7 = 37496;
		$s = $w * $h;
		break;
	}
	return array(
		"I*",
		1481919403,
		3140563232,
		169478669,
		67305985,
		$type4,
		1,
		$type6,
		$type7,
		0,
		$w,
		$h,
		0,
		0,
		1,
		1,
		0,
		$s
	);
}

// call pack() function with array as arguments
function array_pack(array $arr) {
	return call_user_func_array("pack", $arr);
}	

// Calculate length of unpack type with its length
function getPackTypeSize($t, $s) {
	switch($t) {
	case 'a': case 'A': case 'c': case 'C': 
	case 'x': case 'X': case 'Z': case 'z':
	case '@':
		return 1 * $s;
	case 's': case 'S': case 'n': case 'v':
		return 2 * $s;
	case 'i': case 'I': case 'l': case 'L':
	case 'N': case 'V': case 'f':
		return 4 * $s;
	case 'q': case 'Q': case 'J': case 'P':
	case 'd': 
		return 8 * $s;
	default:
		return 1;
	}
	return 1;
}

// Convince function for unpack2 from file resource
function unpack2fp($format, &$fp, $bufSize = 4096) {
	$oSeek = ftell($fp);
	fseek($fp, 0, SEEK_END);
	$bufSize = min(ftell($fp), $oSeek + $bufSize) - $oSeek;
	fseek($fp, $oSeek);
	$data = fread($fp, $bufSize);
	$result = unpack2($format, $data);
	fseek($fp, $oSeek + $result['_pos']);
	return $result;
}

// Customize unpack function for convince using
//
// example: I:1:_size/z:*:path/m/I:13:f/Z:*:name/M
// I:1:_size, get one int (4 bytes) with name _size. _size is used for 'z' with char length
// z:*:path, get multiple chars with length _size. Important! _size need be assigned before 
// m, align bytes to 4 bytes
// I:13:f, get 13 int (13 * 4 bytes) with name f
// Z:*:name, get multiple chars until \0 occures, with name name
// M, align bytes to 8 bytes
function unpack2($format, &$data) {
	$types = explode("/", $format);
	$pos = 0;
	$result = array();
	for($i = 0; $i < count($types); $i++) {
		@list($type, $size, $rename) = explode(":", $types[$i]);
		if ($type == NULL) continue;
		if ($size == NULL) $size = 1;
		if ($rename == NULL) $rename = "";
		if ($type == 'Z' && $size == '*') {
			$nullPos = strpos($data, "\0", $pos);
			if ($nullPos === false) {
				$nullPos = strlen($data);
				$size = $nullPos - $pos; // not include null char
			} else {
				$size = $nullPos - $pos + 1; // include null char
			}
		} else if ($type == 'z') {
			$type = 'Z';
			$size = $result['_size'];
		} else if ($type == 'm') {
			$pos = ceil($pos / 4) * 4;
			continue;
		} else if ($type == 'M') {
			$pos = ceil($pos / 8) * 8;
			continue;
		}
		$f = "$type$size$rename";
		$l = getPackTypeSize($type, $size);
		$d = substr($data, $pos, $l);
		$r = unpack($f, $d);
		$pos += $l;
		$result = array_merge($result, $r);
	}
	$result['_pos'] = $pos;
	return $result;
}

?>