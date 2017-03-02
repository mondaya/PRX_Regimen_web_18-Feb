<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.referral-nct.php");
	
	$module = 'referral-nct';
	$table = 'tbl_orders';
	
	$winTitle = 'My Referrals -' .SITE_NM;
    $headTitle = 'My Referrals';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));

    if(isset($_POST['submitReferral']) && $_POST['submitReferral'] == 'Email'){

    	extract($_POST);
    	$email = explode(',',$email);
    	$userEmail = getTableValue('tbl_users','email',array('id'=>$sessUserId));

    	$i = 0;
    	foreach ($email as $key => $value) {
    		$alreadyExist = getTotalRows('tbl_referral_users',array('email'=>$value),'id');

            if(base64_decode($userEmail) == $value){
                $_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>'You can\'t refer yourself.'));
                redirectPage(SITE_URL.'referral');
            }

    		if($alreadyExist > 0){
    			
    			$_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>$value.' is already invited.'));
    			redirectPage(SITE_URL.'referral');

    		}else{
                if(filter_var($value,FILTER_VALIDATE_EMAIL))
                 {
    			$data = array();
    			$data['userId'] = $sessUserId;
    			$data['email'] = $value;
    			$db->insert('tbl_referral_users',$data);

    			//Email notification
    			$link = '<a href="'.SITE_URL.'referral/'.base64_encode($sessUserId).'">Click Here</a>';

	   			$contArray = array(
					"USER_NM"=>$sessUserName,
					"REFERRAL_LINK"=>$link
				);
				sendMail($value,"referral_mail",$contArray);
            }else
            {
                $_SESSION['msgType'] = disMessage(array('type'=>'err','var'=>''.' Please enter valid emails with comma seperated.'));
                redirectPage(SITE_URL.'referral');
            }

    		}

    		$i++;
    	}

    	$_SESSION['msgType'] = disMessage(array('type'=>'suc','var'=>$i.' users invited succesfully.'));
    	redirectPage(SITE_URL.'referral');
    }
    
		
	
	
	$mainObj = new referral($module);

	$pageContent = $mainObj->getPageContent();
 	
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>