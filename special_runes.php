<?php
$fnRunes = array("water", "fire", "wood", "light", "dark", "heart", "unknown", "bomb");
$fnTypes = array("normal"/*, "strong"*/);
$specials = array("bombgem_lv2", "line03_green", "line03_green2", "stone_freeze_lv0", "stone_lock_lv1", "stone_lock_lv2", "stone_lock_lv3", "burn", "lightning");
foreach($fnRunes as $rune) {
	foreach($fnTypes as $type) {
		$rfilename = "runes/black/{$rune}_{$type}.png";
		foreach($specials as $special) {
			$rimage = imagecreatefrompng($rfilename);
			imagealphablending($rimage, true);
			imagesavealpha($rimage, true);
			$sfilename = "runes/special/$special.png";
			$simage_t = imagecreatefrompng($sfilename);
			$simage = imagescale($simage_t, 128, 128);
			imagecopy($rimage, $simage, 0, 0, 0, 0, 128, 128);
			$ofilename = "runes/type/{$rune}_{$type}_{$special}.png";
			imagepng($rimage, $ofilename, 0);
		}
	}
}

$fns = array("stone_absorb_water_T128_RGBA4", "stone_absorb_fire_T128_RGBA4", "stone_absorb_grass_T128_RGBA4", "stone_absorb_light_T128_RGBA4", "stone_absorb_dark_T128_RGBA4", "stone_absorb_ver4_T128_RGBA4");
foreach($fns as $i => $fn) {
	$rfilename = "runes/special/$fn.png";
	$rimage = imagecreatefrompng($rfilename);
	$im = imagecreatetruecolor(128, 128);
	$color = imagecolorallocatealpha($im , 56/2.55 ,33/2.55, 19/2.55, 0);
	imagealphablending($im, true);
	imagesavealpha($im, true);
	imagefill($im, 0, 0, $color);
	imagecopy($im, $rimage, 0, 0, 0, 0, 128, 128);
	$ofilename = "runes/absorb/{$fnRunes[$i]}_absorb.png";
	imagepng($im, $ofilename, 0);
}
	
?>