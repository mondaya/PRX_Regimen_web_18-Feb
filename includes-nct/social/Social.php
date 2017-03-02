<?php
/* -----------------------------------------------------------------------------------------
   IdiotMinds - http://idiotminds.com
   -----------------------------------------------------------------------------------------
*/
//For Facebook
require_once '../config-nct.php';
require_once 'lib/facebook/facebook.php';

unset($_COOKIE);
unset($_SESSION['facebook_login']);

class Social{
 function facebook(){
	 
	 $_SESSION['facebook_login'] = true;
     $facebook = new Facebook(array(
		'appId'		=>  FB_APP_ID,
		'secret'	=> FB_APP_SECRET,
		));
			//get the user facebook id		
			$user = $facebook->getUser();
			
			//echo $user;exit;
			if($user){
				try{
					//get the facebook user profile data
					$user_profile = $facebook->api('/me');
					//$params = array('next' => SOCIAL_BASE_URL.'logout.php');
					//logout url
				
					$logout =$facebook->getLogoutUrl();
					$_SESSION['User']=$user_profile;
					$_SESSION['facebook_logout']=$logout;
					//redirectPage(SITE_URL);
				}catch(FacebookApiException $e){
					error_log($e);
					$user = NULL;
				}		
			}
			if(empty($user)){
					  //login url	
					  $loginurl = $facebook->getLoginUrl(array(
							'scope'			=> 'email,read_stream,publish_actions,user_birthday,user_location, user_work_history, user_hometown, user_photos',
							'redirect_uri'	=> SOCIAL_BASE_URL.'login.php?facebook',
							'display'=>'popup'
							));
					  header('Location: '.$loginurl);
			}
  
  }
}

?>

