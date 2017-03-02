<?php
	$module = 'static-nct';
	require_once("../../includes-nct/config-nct.php");
	require_once("class.static-nct.php");
	
	
	$id = isset($_GET['id']) ? $_GET['id'] : 0;

	$module = 'static-nct';
	$objContent = new Content($module,$id);
	$getStaticPageContent=$objContent->getStaticPageContent();
	//echo $getStaticPageContent['id'];exit;
	if($id==0 || $getStaticPageContent['id']==0){
		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Invalid request'));
		redirectPage(SITE_URL);
	}
	
	$winTitle = $getStaticPageContent['page_title'].' '.SITE_NM;
    $headTitle = $getStaticPageContent['meta_keyword'];


    $metaTag = getMetaTags(array("description"=>$getStaticPageContent['meta_desc'],"keywords"=>$headTitle,"author"=>AUTHOR));
	
    $fields = array(
						'%MODULE%',
						'%PAGE_TITLE%',
						'%PAGE_DESCRIPTION%'
				   );

	$fields_replace = array(
						$module,
						$getStaticPageContent['page_title'],
						$getStaticPageContent['page_desc']
					);
	$pageContent = $objContent->getPageContent();
	
	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	require_once(DIR_TMPL."parsing-nct.tpl.php");
	
?>