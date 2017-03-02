<?php
	require_once('../../config-nct.php');
	//require_once(DIR_MOD.'nct-login/class.nct-login.php');
	echo "<pre>";
	echo "test";
	print_r($_REQUEST);
	exit;	
	//$loginObj = new Login($db,$module);		
	
	
	unset($_SESSION['google_login']);
	unset($_SESSION['facebook_login']);
	unset($_SESSION['linkedin_login']);
	
	
	$config['base_url']         = 		LINKEDIN_BASE_URL;
    $config['callback_url']         =   LINKEDIN_CALLBACK_URL;
    $config['linkedin_access']      =   LINKEDIN_ACCESS;
    $config['linkedin_secret']      =   LINKEDIN_SECRET;

    
   include_once("linkedin.php");
    
    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
    //$linkedin->debug = true;
	/*echo "<pre>";
	print_r($_REQUEST);
	exit;	*/
   if (isset($_REQUEST['oauth_verifier'])){
		
        $_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];

        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);

        $_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
        header("Location: " . $config['callback_url']);
        exit;
   }
   else{
        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
        $linkedin->access_token     =   unserialize($_SESSION['oauth_access_token']);
   }

	

    # You now have a $linkedin->access_token and can make calls on behalf of the current member
    $xml_response = $linkedin->getProfile("~:(id,email-address,date-of-birth,phone-numbers,first-name,last-name,location:(name),headline,picture-url,site-standard-profile-request)");
	$a=simplexml_load_string($xml_response);
    $xml_response_for_picture = $linkedin->getProfile("~/picture-urls::(original)");
	$b=simplexml_load_string($xml_response_for_picture);

	$_SESSION['linkedin_id']=$a->id;
	$email=$a->{'email-address'};
	$firstname=$a->{'first-name'};
	$lastname=$a->{'last-name'};	
	$picture_url=$b->{'picture-url'};	
	$birth_day=$a->{'date-of-birth'};	
	print_r($_SESSION);
	exit;
	
	// linkedin login code start
	if(isset($email) && $email!='' && isset($_SESSION['linkedin_id']) && $_SESSION['linkedin_id']!=''){
		
		//getting variable for check that reffer from login page or from account page - for verified linkedin 		
		$ses_pagename = parse_url($_SESSION["ses_pagename"], PHP_URL_PATH);	

		
/*		echo $_SESSION['linkedin_id'];
		echo "<pre>";
			print_r($_SESSION); 
		echo "</pre>";
		exit;*/
		
		if($ses_pagename == "/user/account" or $ses_pagename == "/user/account/")
		{	
			$objPost = new stdClass();
			$objPost->linkedin_login = 'y';
			$objPost->linkedin_id = $_SESSION['linkedin_id'];
			$objPost->updatedDate = date('Y-m-d H:i:s');
			$db->update("tbl_users", $objPost, 'id', $_SESSION["sessUserId"]);
			
			// unset facebook session variable
			unset($_SESSION['linkedin_id']);
			unset($_SESSION['ses_pagename']);
			$email = '';
			unset($email);			
			
			$msgType = $_SESSION["msgType"] = array('type'=>'err','var'=>'linkedinVerified');
			redirectPage(SITE_URL.'user/account');
		}
		else
		{
			
			$check_email=getTotalRows($db,'tbl_users','email=\''.$email.'\'','*');
					
			$qrysel = $db->select("tbl_users","id,email,firstName,isActive","linkedin_id='".$_SESSION['linkedin_id']."'");
			$fetchUser = mysql_fetch_assoc($qrysel);
					
			// if email not found then registration		
			if($check_email<1){
					$objPost = new stdClass();
					$objPost->firstName = isset($firstname) ? $db->filtering($firstname,'input','string','') : '';
					$objPost->lastName = isset($lastname) ? $db->filtering($lastname,'input','string','') : '';
					$objPost->email = isset($email) ? $db->filtering($email,'input','string','') : '';
					$password=generatePassword();
					$objPost->password = md5($password);
					$objPost->country_code = '';
					$objPost->phone = '';
					$objPost->randomNumber = base64_encode(time());
					$objPost->linkedin_login =($objPost->phone!='')?'y':'n';
					$objPost->linkedin_id =isset($_SESSION['linkedin_id']) ? $db->filtering($_SESSION['linkedin_id'],'input','string','') : 0;
					$objPost->isActive=($objPost->phone!='')?'y':'n';
					$objPost->createdDate =date('Y-m-d H:i:s');
					$objPost->updatedDate =date('Y-m-d H:i:s');
					$objPost->lastLogin =date('Y-m-d H:i:s');			
					$db->insert('tbl_users',$objPost);
					$last_id=mysql_insert_id();				
					
					/*gosengi*/
					$th_arr[0]=array('width'=>'220','height'=>'220');	 // user view profile	
					$th_arr[1]=array('width'=>'35','height'=>'35');		 //service detail page
					$th_arr[2]=array('width'=>'225','height'=>'225');	 // Photo & videos big thumb
					$th_arr[3]=array('width'=>'85','height'=>'85');		 // Photo & videos slide show thumb
					$th_arr[4]=array('width'=>'100','height'=>'100');		 // for confirm booking page [th5_]
					$th_arr[5]=array('width'=>'50','height'=>'50');		 // Inbox detail page user thumb [th6_]
					$th_arr[6]=array('width'=>'56','height'=>'56');		 // Dashboard Message user [th7_]
					/*gosengi*/
					
					if($picture_url!=''){
						$filename=imageUploadNew($db,'user',$last_id,'user',$th_arr,'','i','','','',$picture_url,'');
					}
					
					if($objPost->phone!=''){
					
						$karr=array('%TO_NAME%','%SITE_NM%','%EMAIL%','%PASSWORD%');
						$varr=array($objPost->firstName,SITE_NM,$objPost->email,$password);
								
						UsersendMail($db,stripslashes($objPost->email),FROM_NM,ADMIN_EMAIL,"social_login",$karr,$varr);
						$msgType = $_SESSION["msgType"] = array('type'=>'suc','var'=>'SocialSignupSuccess');
						
					}else{
						 redirectPage(SITE_URL.'social-signup/'.base64_encode($last_id).'/'.$objPost->randomNumber.'/'.base64_encode('linkedin').'/');
					}
					
			}else{
				
				$qrysel = $db->select("tbl_users","id,email,firstName,gender,birthdate,phone,isActive","email='".$email."'");
				$fetchUser = mysql_fetch_assoc($qrysel);	
				
				$total_records=getTotalRows($db,'tbl_users','linkedin_id=\''.$_SESSION['linkedin_id'].'\' and linkedin_login=\'y\'','*');
				// if email found then update likedin verify status with it's app id
				if($total_records<1){
					$objPost = new stdClass();
					
					if($fetchUser['phone']==''){
						$objPost->randomNumber = base64_encode(time());
						$db->update('tbl_users',$objPost,'email=\''.$email.'\'');
						redirectPage(SITE_URL.'social-signup/'.base64_encode($fetchUser['id']).'/'.$objPost->randomNumber.'/'.base64_encode('linkedin').'/');
					}
					
					$objPost->linkedin_login ='y';
					$objPost->linkedin_id =$_SESSION['linkedin_id'];
					$objPost->isActive ='y';
					$objPost->updatedDate =date('Y-m-d H:i:s');
					$objPost->lastLogin =date('Y-m-d H:i:s');			
					$db->update('tbl_users',$objPost,'email=\''.$email.'\'');
				}else{
					// if email and likedin app id found then direct login with updated status
					$objPost->isActive ='y';
					$objPost->updatedDate =date('Y-m-d H:i:s');
					$objPost->lastLogin =date('Y-m-d H:i:s');			
					$db->update('tbl_users',$objPost,'email=\''.$email.'\'');
				}
			}
	
			$_SESSION['linkedin_login']=true;
			//$loginObj->doSocialLogin($_SESSION['linkedin_id'],'linkedin');
			
			
		} /// pagename check else brackets
	}	
	else{
		  redirectPage(SITE_URL.'login/');
	}
	// linkedin login code end
?>