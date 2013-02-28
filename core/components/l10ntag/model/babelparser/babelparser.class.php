<?php

class BabelParser {
	public $TAGSTART = "[@";
	public $TAGEND = "]";
	public $cultureKeyLength = 2;
	public function getTagStart ($input, $offset) {
		return strpos($input, $this->TAGSTART, $offset);
	}

	public function getTagEnd ($input, $offset) {
		$tagEnd = strpos($input, $this->TAGEND, $offset) + $this->tagEndLen;
		while ($tagEnd && $input[$tagEnd+1] == $this->TAGEND) {
			$tagEnd++;
		}
		return $tagEnd;
	}

	public function __construct (&$modx, $options) {
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
				$var=substr($var,$tagStart);
				break;
			}
			$tag = substr($var,$tagStart,$tagEnd - $tagStart);
			$newTag = $this->parseTag($tag);
			$var = str_replace($tag, $newTag , $var);
			$tagStart = $this->getTagStart($var,$tagEnd-(strlen($tag)-strlen($newTag)));
		}
		return $var;
	}
	public function parseTag($tag){
		$tagCulture = substr($tag,$this->tagStartLen,$this->cultureKeyLength);
		if(substr($tag,0,$this->tagStartLen)==$this->TAGSTART && $tagCulture == $this->cultureKey){
			return substr($tag,4,strlen($tag)-$this->tagStartLen - $this->tagEndLen-2);
		}
		else if(substr($tag,0,$this->tagStartLen)!=$this->TAGSTART){
			return $tag;
		}
		else{
			return "";
		};
	}
}