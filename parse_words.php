<?php
ini_set('max_execution_time', 1000000);
require_once "information.php";
include_once 'smart-character-segmenter/Dictionary.php';
databaseConnect($hostname, $username, $password,$database);
$max = getMax();
$Dictionary = new Dictionary;
$dicExists = $Dictionary->loadDictionary("smart-character-segmenter/cwordict_fast.dic");
if(!$dicExists){
	die("Dictionary file does not exist");
}
$counter = 0;
for ($x=0; $x<=$max; $x++){
	$sql = "SELECT `parent_id`, `word`, `frequency` FROM `words` WHERE `parent_id` = $x;";
	$result = mysql_query($sql);
	if(!$result){
		die ("Could not query database" . mysql_error());
	}
	echo "Currently Parsing All Words on Page " . $x;
	$row = mysql_fetch_row($result);
	$freqList = array();
	$counter++;
	while($row) {
		$counter ++;
		$freqList = doParse($row, $Dictionary, $counter, $freqList);	
		$row = mysql_fetch_row($result);
	}
	doInsert($counter, $freqList);
}
function databaseConnect($hostname, $username, $password,$database){
	$dbHandle = mysql_connect($hostname, $username, $password)
		or die("Unable to connect to MySQL host");
	$selected = mysql_select_db($database, $dbHandle)
		or die("Could not select db");
	mysql_set_charset('utf8');
	mysql_query("SET NAMES utf8");
}
function getMax(){
	$selectmax = "SELECT max(parent_id) FROM words;";
	$result = mysql_query($selectmax);
	if(!$result){
		die ("Could not select maximum parent id" . mysql_error());
	}
	$max = mysql_fetch_array($result)[0];
	return $max;
}
function doParse($row, $Dictionary, $counter, $freqList){
	$string = $row[1];
	$frequency = $row[2];
	$parentId = $row[0];
	$Dictionary->parseString($string);
	$wordsCn = $Dictionary->chineseWords;
	$wordsEn = $Dictionary->englishWords;
	$words = array_merge($wordsCn, $wordsEn);
	foreach ($words as $word) {
		if($counter%10==0){
				echo "parsing... " . $counter;
		}
		if(ctype_space($word) || $word == " " || $word == ""){
			continue;
		}
		$key = new Key($word, $parentId);
		if(!array_key_exists($key->toString(), $freqList)){
			$freqList[$key->toString()] = $frequency;
				}
		else{
			$freqList[$key->toString()] += $frequency;
		}
	}
	return $freqList;
}
function doInsert($counter, $freqList){
	foreach ($freqList as $key => $value) {
		if($counter%10==0){
			echo "inserting... " . $counter;
		}
		$split = explode(" ", $key);
		$parent_id = $split[1];
		$word = $split[0];
		$frequency = $value;
		$sql = "INSERT INTO words_parsed (word, parent_id, frequency) VALUES ('$word', '$parent_id', $frequency);";
		$result = mysql_query($sql);
		if(!$result){
			die ("Unable to insert into database" . mysql_error());
		}
	}
}
echo "succesfully parsed all words and inserted";

?>