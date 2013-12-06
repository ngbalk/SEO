<?php
$stoparray = array();
$handle = fopen("stop_words.txt", "r");
fgets($handle);
while(!feof($handle)){
	$word = trim(fgets($handle));
	if(!array_search($word, $stoparray)){
		array_push($stoparray, $word);
	}
}

arsort($stoparray);
foreach ($stoparray as $thing) {
	echo $thing . "<br>";
}
?>