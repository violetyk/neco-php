<?php

require_once 'simple_html_dom.php';
//mb_internal_encoding('utf-8');

$functions  = get_defined_functions();
$constants  = get_defined_constants();
$interfaces = get_declared_interfaces();
$classes    = get_declared_classes();


// functions
$output = 'functions.dict';

// functions-url templates,English is default
$_URL_ = 'http://php.net/manual/en/function.%s.php';
// Japanese : 'http://jp1.php.net/manual/jp/function.%s.php';
// Chinese  : 'http://cn2.php.net/manual/zh/function.%s.php';

$fp = fopen($output, 'w');
foreach ($functions['internal'] as $func) {
  $url = sprintf(
    $_URL_,
    str_replace('_', '-', $func)
  );

  $html = file_get_html($url);
  if( !$html ){
      continue;
  }

  $title = '';
  foreach ($html->find('span.dc-title') as $element) {
    $title = trim(htmlspecialchars_decode($element->plaintext, ENT_QUOTES));
  }

  $description = '';
  foreach ($html->find('div.dc-description') as $element) {
    $description = trim(htmlspecialchars_decode($element->plaintext, ENT_QUOTES));
    $description = preg_replace('/\s{2,}/', ' ', $description);
    $description = preg_replace('/(\w+)\s,/', '${1},', $description);
  }

  $comment = '';
  foreach ($html->find('p.rdfs-comment') as $element) {
    $comment = trim(htmlspecialchars_decode($element->plaintext, ENT_QUOTES));
    $comment = preg_replace('/\s{2,}/', ' ', $comment);
  }

  $line = sprintf("%s\t;\t%s\t;\t%s\t;\t%s\n",
    $func,
    $title,
    $description,
    $comment
  );
  fwrite($fp, $line);
  echo $line;
  $html->clear();
  unset($html);
}
fclose($fp);
