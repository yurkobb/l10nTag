<?php

class BabelParser {
	public function getTagStart ($input, $offset) {
		return strpos($input, $this->TAGSTART, $offset);
	}

	public function getTagEnd ($input, $offset) {
		$tagEnd = strpos($input, $this->TAGEND, $offset) + $this->tagEndLen;
		while ($tagEnd && $input[$tagEnd+1] == ']') {
			$tagEnd++;
		}
		return $tagEnd;
	}

	public function __construct (&$modx, $options) {
		$this->TAGSTART="[@";
		$this->TAGEND="]";
		$this->xpdo = $modx;
		$this->tagStartLen = strlen($this->TAGSTART);
		$this->tagEndLen = strlen($this->TAGEND);
		$this->cultureKey = $this->xpdo->getOption('cultureKey');
//		$this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, '[BabelParser] Running with cultureKey = '.$this->cultureKey);
	}
	public function parseString($input){
		$var = &$input;
		$tagStart = strpos($var,$this->TAGSTART);
		$tagEnd=0;
		$tag='';
		while($tagStart!=false){
			$tagEnd=$this->getTagEnd($var,$tagStart);
			if(!$tagEnd){
				$output.=substr($var,$tagStart);
			}
			$tag = substr($var,$tagStart,$tagEnd - $tagStart);
			$newTag = $this->parseTag($tag);
			$var = str_replace($tag, $newTag , $var);
			$tagStart = $this->getTagStart($var,$tagEnd + 1);
		}
		return $var;
	}
	public function parseTag($tag){
		$tagCulture = substr($tag,2,2);
		if($tagCulture == $this->cultureKey){
//		if("ru" == "ru"){
			return substr($tag,4,strlen($tag)-$this->tagStartLen - $this->tagEndLen-2);
		}
		else{
			return "";
		};
	}
}