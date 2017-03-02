<?php 

$page = new Templater(DIR_ADMIN_TMPL."main-nct.tpl.php");

$head = new Templater(DIR_ADMIN_TMPL."head-nct.tpl.php");

$site_header= new Templater(DIR_ADMIN_TMPL."header-nct.tpl.php");

$footer=new Templater(DIR_ADMIN_TMPL."footer-nct.tpl.php");

$page->body= '';
$page->right='';
$page->head='';
$page->header='';
$page->footer='';

