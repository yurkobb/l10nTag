<?php

class BabelParser {
	const TAGSTART = '[@';
	const TAGEND = ']';

	public function getTagStart ($input, $offset) {
		return strpos($input, self::TAGSTART, $offset);
	}

	public function getTagEnd ($input, $offset) {
		$maybeTagEnd = strpos($input, self::TAGEND, $offset) + $this->tagEndLen;
		while ($maybeTagEnd && substr($input, $maybeTagEnd + 1, 1) == ']') {
			$maybeTagEnd = strpos($input, self::TAGEND, $maybeTagEnd) + $this->tagEndLen;
		}
		$tagEnd = $maybeTagEnd;
		return $tagEnd;
	}

	public function __construct (&$modx, $options) {
		$this->xpdo = $modx;
		$this->tagStartLen = strlen(self::TAGSTART);
		$this->tagEndLen = strlen(self::TAGEND);
		// We currently suppose cultureKey is always 2 bytes long
		$this->cultureKey = $this->xpdo->getOption('cultureKey');
		$this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, '[BabelParser] Running with cultureKey = '.$this->cultureKey);
	}

	public function parseString ($input) {
		$inputLen = strlen($input);
		$tagStart = strpos($input, self::TAGSTART);
		$tagEnd = 0;
		$tag = '';
		$output = '';

		while ($tagStart !== false) {
			$output .= substr($input, $tagEnd, $tagStart - $tagEnd);
			
			$tagEnd = $this->getTagEnd($input, $tagStart + $this->tagStartLen);
			/* $this->xpdo->setLogLevel(xPDO::LOG_LEVEL_DEBUG); */
			/* $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, 'tagStart = '.$tagStart); */
			/* $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, 'tagEnd = '.$tagEnd); */
			if (!$tagEnd) {
				/* $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,  */
				/* 				 '[L10nParser]: Couldn\'t find closig tag started at index ' . $tagStart); */
				return $output;
			}
			$tag = substr($input, $tagStart, $tagEnd - $tagStart);
			
			$output .= $this->parseTag($tag);

			$tagStart = $this->getTagStart($input, $tagEnd + $this->tagEndLen);
		}
		if (!$output) return $input;
		else return $output;
	}
	
	public function parseTag ($tag) {
		/* $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, 'parseTag() ran with this tag: ' . $tag); */
		$tagCulture = substr($tag, $this->tagStartLen, 2);
		/* $this->xpdo->log(xPDO::LOG_LEVEL_DEBUG, 'Found tag cultureKey: ' . $tagCulture); */
		if ($tagCulture == $this->cultureKey) {
			$text = substr($tag, $this->tagStartLen + 3, $this->tagEndLen * -1);
			return $text;
		} else {
			return '';
		}
	}
}