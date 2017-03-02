<?php
	$main->set("module", $module);
	require_once(DIR_THEME.'theme.template.php');
	$head->title = $winTitle;
	$head->metaTag = $metaTag;

	$fields = array('%METATAG%','%TITLE%', '%EXTRA_CSS%');
	$fields_replace = array($metaTag, $winTitle, load_css($styles));
	$head_content = str_replace($fields, $fields_replace, $head->parse());
	$head_content = preg_replace('/\{([A-Z_]+)\}/e', "$1", $head_content);


	$fields = array("%EMAIL%");

	$containerAfterLogin = $containerBeforeLogin = '';
	if($sessUserId > 0){
		$contentClass = 'main-content inner-page';
		$containerAfterLogin = '<div class="container">';
		$fullHeader = 'full-header';
	}else{
		$contentClass = 'main-content';
		$containerBeforeLogin = '<div class="container">';
		$fullHeader = '';
	}

	$footerDivStart = $footerDivEnd = ''; 
	if($module == 'static-nct'){
		$footerDivStart = '<div class="page-wrap">';
		$footerDivEnd = '</div>';
	}	

	$curr_array = $objHome->getHeaderCurrency();
	$fields = array("%CUR_CURRENCY%", "%CURRENCY_DROPDOWN%", "%LOGIN_USER%", "%LOGIN_LOGOUT_BTN%",'%CONTENT_CLASS%','%CONTAINER_BEFORE_LOGIN%','%CONTAINER_AFTER_LOGIN%','%FULL_HEADER%','%FOOTER_DIV_START%','%FOOTER_DIV_END%');
	if(empty($sessUserId)) {
		$btn = '<a href="javascript:void(0);" id="btn_login" title="Login"><i class="fa fa-sign-in"></i> Login</a>';
	} else {
		$btn = '<a href="'.get_link('logout').'" title="Logout"><i class="fa fa-sign-out"></i> Logout</a>';
	}
	$fields_replace = array($curr_array['curr_currency'], $curr_array['currency_html'], $objHome->getHeaderContent(), $btn,$contentClass,$containerBeforeLogin,$containerAfterLogin,$fullHeader,$footerDivStart,$footerDivEnd);
	$header_content = str_replace($fields, $fields_replace, $header->parse());
	$header_content = preg_replace('/\{([A-Z_]+)\}/e', "$1", $header_content);

	$footer_content = str_replace($fields, $fields_replace, $footer->parse());
	$footer_content = preg_replace('/\{([A-Z_]+)\}/e', "$1", $footer_content);

	$supportData = $objHome->getSupportData();
	$helpData = $objHome->getHelpData();
	$walletData = $objHome->getWalletData();

	$fields1 = array("%SUPPORT_DATA%",'%HELP_SUPPORT%','%WALLET%');
	$fields_replace1 = array($supportData,$helpData,$walletData);
	$footer_content=str_replace($fields1,$fields_replace1,$footer_content);

	$page->set("head", $head_content);
	$page->set("site_header", $objHome->getHeaderContent());
	$page->set("body", $pageContent);
	$page->set("footer",$footer_content);

    /* Outputting the data to the end user */
	$fields = array('%HEAD%','%SITE_HEADER%','%BODY%','%FOOTER%','%MESSAGE_TYPE%','%SESSUSERID%','%EXTRA_JS%');
	$fields_replace = array($head_content, $header_content, $pageContent, $footer_content, (isset($msgType) && !empty($msgType)?$msgType:''), $sessUserId, load_js($scripts));
	$page_content=str_replace($fields,$fields_replace,$page->parse());
	$page_content = preg_replace('/\{([A-Z_]+)\}/e', "$1", $page_content);
    echo $page_content;
	exit;