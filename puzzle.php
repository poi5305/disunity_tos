<?php
	
$dir_runes = "runes/runes";
$runes = array(
	array("water_normal.png", "water_normal_bw.png"),     // 0
	array("fire_normal.png", "fire_normal_bw.png"),		  // 1
	array("wood_normal.png", "wood_normal_bw.png"),       // 2
	array("light_normal.png", "light_normal_bw.png"),     // 3
	array("dark_normal.png", "dark_normal_bw.png"),       // 4
	array("heart_normal.png", "heart_normal_bw.png"),     // 5
	array("unknown_normal.png", "unknown_normal_bw.png")  // 6
);

var_dump(puzzle_fill_cvec_from_file($argv[1]));

$runes_signature = array();
foreach($runes as $i => $rune) {
	$name1 = "$dir_runes/$rune[0]";
	$name2 = "$dir_runes/$rune[1]";
	$runes_signature[$i] = array(puzzle_fill_cvec_from_file($name1), puzzle_fill_cvec_from_file($name2));
	//var_dump(puzzle_fill_cvec_from_file($name1));
}
$d = puzzle_vector_normalized_distance($runes_signature[0][0], $runes_signature[0][1]);
echo "$d";
//var_dump($runes_signature[0]);
//$signature = puzzle_fill_cvec_from_file($filename);
//$d = puzzle_vector_normalized_distance($signature1, $signature2);
?>