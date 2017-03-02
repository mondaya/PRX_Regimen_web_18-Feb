<?php
	$page = new Templater(DIR_TMPL."main-nct.tpl.php");
	$head = new Templater(DIR_TMPL."head-nct.tpl.php");
	$header = new Templater(DIR_TMPL."header-nct.tpl.php");
	$footer =new Templater(DIR_TMPL."footer-nct.tpl.php");
	$page->body = '';
	$page->right ='';
	$page->head = '';
	$page->header = '';
	$page->footer = '';