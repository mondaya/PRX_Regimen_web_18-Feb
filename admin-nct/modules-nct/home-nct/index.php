<?php

	

	$reqAuth=true;

	$analytic_total_views=0;

	require_once("../../../includes-nct/config-nct.php");



	$module = "home-nct";

	$page_name = "home";

	$winTitle = 'Welcome to Admin Panel - '.SITE_NM;

	$headTitle = 'Welcome to Admin Panel';

	$metaTag = getMetaTags(array("description"=>"Admin Panel",

		"keywords"=>'Admin Panel',

		'author'=>AUTHOR));

	$breadcrumb = array("Dashboard");	

	$mainObj = new Home();

	$mainContent = $mainObj->index();

	$pageContent = $mainObj->getPageContent();

	require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");

	//require_once(DIR_ADMIN_THEME."default.nct");