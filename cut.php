<?php
$fn = "runes.png";

$bg = "black2";
@mkdir("runes/$bg");

$fnRunes = array("water", "fire", "wood", "light", "dark", "heart", "unknown", "bomb");
$fnTypes = array("normal", "strong");

$h = 128;
$w = 128;
$image = imagecreatefrompng($fn);
//imagefilter($image, IMG_FILTER_NEGATE);
//imagefilter($image, IMG_FILTER_GRAYSCALE);
//imagefilter($image, IMG_FILTER_BRIGHTNESS, -50);
//imagefilter($image, IMG_FILTER_CONTRAST, -50);

$i = 0;
for ($y = 0; $y < 4; $y++) {
	for ($x = 0; $x < 4; $x++) {
		$im = imagecreatetruecolor($w, $h);
		$color = imagecolorallocatealpha($im , 56/2.55 ,33/2.55, 19/2.55, 0);
		imagealphablending($im, false);
		imagesavealpha($im, true);
		imagefill($im, 0, 0, $color);
		$rune_id = $i % 8;
		$type_id = $i / 8;
		$filename = "{$fnRunes[$rune_id]}_{$fnTypes[$type_id]}.png";
		echo "$i " . $filename."\n";
		imagecopymerge($im, $image, 0, 0, ($w * $x) + ($i/4)%2*2, ($h * $y), $w, $h, 100);
		imagefill($im, 0, 0, $color);
		imagefill($im, 4, 20, $color);
		
		imagepng($im, "runes/$bg/$filename", 0);
		$i++;
	}
}



	
?>