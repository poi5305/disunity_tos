<?php
/*
adb shell su -c "cp /data/data/com.madhead.tos.zh/cache/com.android.opengl.shaders_cache /storage/emulated/legacy/Download/"
adb shell su -c "cp /data/data/com.madhead.tos.zh/shared_prefs/com.madhead.tos.zh.v2.playerprefs.xml /storage/emulated/legacy/Download/"
adb pull /storage/emulated/legacy/Download/com.android.opengl.shaders_cache
adb pull /storage/emulated/legacy/Download/com.madhead.tos.zh.v2.playerprefs.xml
*/

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
// PLACE 1水 2火 3木 4光 5暗 6塔
//MH_CACHE_API_DATA_STAGE_JSON 2, 3|2|4|1|1||0|||冰之亡靈塔 |0|0|0|0|0||0
// id|id2|id3|place|?|?|?|?|?|name

// MH_CACHE_API_DATA_FLOOR_JSON 3, 17|5|3|90|5|5||石岩之魔塔|0|0|0|0|||0|0||0||0|0|0|0|0|0|0||0|0|
// id|stage|order|monster_id|energy|battle_number|boss|name|

// MH_CACHE_RUNTIME_DATA_CURRENT_FLOOR_WAVES 現在中的戰鬥

// MH_CACHE_GAMEPLAY_DATA_CURRENT_WAVE 戰鬥中的關卡位置

foreach($xml->int as $string) {
	$result[(string) $string['name']] = (int) $string['value'];
}	
/*

"characteristic": 0,
"child": null,
"effectType": 0,
"extras": null,
"guildWarAttack": -1,
"guildWarAttackDuration": -1,
"guildWarDefense": -1,
"guildWarHp": -1,
"isRareAppear": 0,
"level": 40,
"lootItem": null,
"monsterId": 67,
"nextEnemy": null,
"offsetX": 0,
"offsetY": 0,
"positionType": 0,
"skillList": []

"67|5|2|3|1|3|3|4|2.5|2.5|50|2|2|21|35|800|1100|200|100|24|3|4000|165|556|87|293|137|484|1691|564|108|22|61|0|334|241|241|246|251|256|10|-10|3|1.00|0|0|||0|0||||0|0|0|0|66",
23687
*/

//print_r($result);
?>