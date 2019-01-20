<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Erebox\PhpSimpleTemplate\Template;

$tpl_name = __DIR__."/home.tpl.html";
$tpl = new Template($tpl_name);

$mytag = [
    'URL' => 'http://localhost/home',
    'YEAR' => date('Y')
];
$tpl->tag($mytag);
$tpl->render();
