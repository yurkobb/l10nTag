<?php
$babelparser = $modx->getService('babelparser', 'BabelParser', $modx->getOption('core_path') . 'components/l10ntag/model/babelparser/');

if (!($babelparser instanceof BabelParser)) return;

//$output = $babelparser->parseString($modx->documentOutput);
$output = &$modx->documentOutput;
$result = $babelparser->parseString($output);

//$modx->log(modX::LOG_LEVEL_DEBUG, '[l10nTag] input = ' . $content);
//$modx->log(modX::LOG_LEVEL_DEBUG, '[l10nTag] output = ' . $output);

$modx->documentOutput = $result;