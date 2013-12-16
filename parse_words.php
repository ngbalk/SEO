<?php
//This class is used for parsing Chinese characters into individual words
ini_set('max_execution_time', 10000000);
include_once 'smart-character-segmenter/Dictionary.php';
$Parse = new Parse();
$Parse->runParser();
echo "Succesfully parsed all words";

Class Parse {
	public $lowerBound;
	public $upperBound;
	public function __construct(){
		include_once "information.php";
		$this->databaseConnect($hostname, $username, $password, $database);
		$max = $this->getMax();
		$this->lowerBound = ($responsibility[0]/100)*$max;
		$this->upperBound = ($responsibility[1]/100)*$max;
	}
	public function runParser(){
		$Dictionary = new Dictionary;
		$dicExists = $Dictionary->loadDictionary("smart-character-segmenter/cwordict_fast.dic");
		if(!$dicExists){
			die("Dictionary file does not exist");
		}
		$counter = 0;
		for ($x=$this->lowerBound; $x<$this->upperBound; $x++){
			$sql = "SELECT `parent_id`, `word`, `frequency` FROM `words` WHERE `parent_id` = $x;";
			$result = mysql_query($sql);
			if(!$result){
				die ("Could not query database" . mysql_error());
			}
			echo "Currently Parsing " . $x;
			$row = mysql_fetch_row($result);
			$freqList = array();
			$counter++;
			while($row) {
				$counter ++;
				$freqList = $this->doParse($row, $Dictionary, $counter, $freqList);	
				$row = mysql_fetch_row($result);
			}
			$this->doInsert($counter, $freqList);
		}

	}
	public function databaseConnect($hostname, $username, $password,$database){
		$dbHandle = mysql_connect($hostname, $username, $password)
			or die("Unable to connect to MySQL host");
		$selected = mysql_select_db($database, $dbHandle)
			or die("Could not select db");
		mysql_set_charset('utf8');
		mysql_query("SET NAMES utf8");
	}
	public function getMax(){
		$selectmax = "SELECT max(parent_id) FROM words;";
		$result = mysql_query($selectmax);
		if(!$result){
			die ("Could not select maximum parent id" . mysql_error());
		}
		$max = mysql_fetch_array($result)[0];
		return $max;
	}
	public function doParse($row, $Dictionary, $counter, $freqList){
		$string = $row[1];
		$frequency = $row[2];
		$parentId = $row[0];
		$Dictionary->parseString($string);
		$wordsCn = $Dictionary->chineseWords;
		$wordsEn = $Dictionary->englishWords;
		$words = array_merge($wordsCn, $wordsEn);
		foreach ($words as $word) {
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
	public function doInsert($counter, $freqList){
		foreach ($freqList as $key => $value) {
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
}

