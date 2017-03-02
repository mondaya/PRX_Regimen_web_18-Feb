<?php
	require_once("../../includes-nct/config-nct.php");
	require_once("class.contactUs-nct.php");
	
	$module = 'contactUs-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Contact Us -' .SITE_NM;
    $headTitle = 'My Contact Us';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));


    if(isset($_POST['submitContact']) && $_POST['submitContact'] == 'Send'){
    	extract($_POST);
    	$data = array();

    	$data['first_name'] = $first_name;
    	$data['last_name'] = $last_name;
    	$data['email'] = base64_encode($email);
    	$data['country_id'] = $country;
    	$data['state_id'] = $state;
    	$data['city_id'] = $city;
    	$data['subject'] = $subject;
    	$data['message'] = $message;
    	$db->insert('tbl_contactus',$data);

    	//Send mail to admin
    	$countryName = getTableValue('tbl_country','countryName',array('id'=>$country));
    	$stateName = getTableValue('tbl_state','stateName',array('id'=>$state));
    	$cityName = getTableValue('tbl_city','cityName',array('id'=>$city));

    	$arrayCont = array(
    		'USER_NM'=>$first_name.' '.$last_name,
    		'EMAIL'=>$email,
    		'COUNTRY'=>$countryName,
    		'STATE'=>$stateName,
    		'CITY'=>$cityName,
    		'SUBJECT'=>$subject,
    		'MESSAGE'=>$message
    	);
		sendMail(ADMIN_EMAIL, 'contact_us_message', $arrayCont);

    	$msgType = disMessage(array('type'=>'suc','var'=>"Message sent successfully"));

    }
		
	
	
	$mainObj = new contactUs($module);

	$pageContent = $mainObj->getPageContent();
 	
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>