# How to use

## Simple call

This is the basic code to use the template.

```php
<?php

use Erebox\PhpSimpleTemplate\Template;

$mypage = new Template('home.tpl.html');
$mytag = [
    'URL' => 'http://localhost/home',
    'YEAR' => date('Y');
];
$tpl->tag($mytag);
$tpl->render();

```