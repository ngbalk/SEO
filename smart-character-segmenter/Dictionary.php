<?php

Class Dictionary {
	public $chineseWords;
	public $englishWords;
	public $dic;
	function loadDictionary($dict_file){
        if (!file_exists($dict_file)) {
            return FALSE;
        }
        $this->dic = array();
        $handle = fopen($dict_file, "r");
        fgets($handle);
        while(!feof($handle)){
        	$word = trim(fgets($handle));
        	array_push($this->dic, $word);
        }
        return TRUE;
	}
	function parseString($string){
		$string = preg_replace("/([\!\"\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\\\]\^\_\`\{\|\}\~\t\f]+)/", " ", $string);
        $string = preg_replace("/([\!\"\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\\\]\^\_\`\{\|\}\~\t\f])([\!\"\#\$\%\&\'\(\)\*\+\,\-\.\/\:\;\<\=\>\?\@\[\\\\\]\^\_\`\{\|\}\~\t\f])/", " ", $string);
        $english = preg_replace(array('/[\p{Han}？]/u', '/(\s)+/'), array('', '$1'), $string);
        $english = preg_replace('/\PL/u', ' ', $english);
		$chinese = preg_replace(array('/[^\p{Han}？]/u', '/(\s)+/'), array('', '$1'), $string);
        preg_match_all('/./u', $chinese, $matches);
        $words = $matches[0];
        $tempWord = "";
        $foundWords = array();
        foreach ($words as $word) {
        	if(!array_search($tempWord.$word, $this->dic)){
        		echo $tempWord . "<br>";
        		array_push($foundWords, $tempWord);
        		$tempWord = $word;
        	}
        	else{
        	$tempWord.=$word;
        	}
        }
        array_push($foundWords, $tempWord); 
        $this->chineseWords = $foundWords;
        $this->englishWords = explode(" ", $english);
        return true;
	}
}
Class Key {
    public $word;
    public $parentId;
    public function __construct($word, $parentId){
        $this->word = $word;
        $this->parentId = $parentId;
    }
    public function toString(){
        return $this->word . " " . $this->parentId;
    }
}


