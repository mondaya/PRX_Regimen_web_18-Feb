<?php
require_once '../config.php';
//include google api files
require_once 'gmail/Google_Client.php';
require_once 'gmail/contrib/Google_Oauth2Service.php';
//gmail config
$google_client_id 		= '560701954653-iut375o504c4ks76i93klo5kofm8b0ua@developer.gserviceaccount.com';
$google_client_secret 	= 'gOWEHBhHT_fvOIfEPdKpbIcY';
$google_redirect_url 	= SITE_URL.'login-google-plus/'; //path to your script
$google_developer_id 	= 'AIzaSyCOTz4mxNE-9WU13ULaNQuertJjiFtaB5w';

$gClient = new Google_Client();
$gClient->setApplicationName(LOGIN_WITH_GOOGLE);
$gClient->setClientId($google_client_id);
$gClient->setClientSecret($google_client_secret);
$gClient->setRedirectUri($google_redirect_url);
$gClient->setDeveloperKey($google_developer_id);

$google_oauthV2 = new Google_Oauth2Service($gClient);

//If user wish to log out, we just unset Session variable
if (isset($_REQUEST['reset'])) 
{
  unset($_SESSION['token']);
  $gClient->revokeToken();
  redirectPage(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
}

//If code is empty, redirect user to google authentication page for code.
//Code is required to aquire Access Token from google
//Once we have access token, assign token to session variable
//and we can redirect user back to page and login.
if (isset($_GET['code'])) 
{ 
	$gClient->authenticate($_GET['code']);
	$_SESSION['token'] = $gClient->getAccessToken();
	redirectPage(filter_var($google_redirect_url, FILTER_SANITIZE_URL));
	return;
}


if (isset($_SESSION['token'])) 
{ 
	$gClient->setAccessToken($_SESSION['token']);
}
if($gClient->getAccessToken()) 
{
	  //For logged in user, get details from google using access token
	  $user 				= $google_oauthV2->userinfo->get();
	  $user_id 				= $user['id'];
	  $username 			= filter_var($user['name'], FILTER_SANITIZE_SPECIAL_CHARS);
	  $email 				= filter_var($user['email'], FILTER_SANITIZE_EMAIL);
	  $url 			= filter_var($user['link'], FILTER_VALIDATE_URL);
	  //$profile_image_url 	= filter_var($user['picture'], FILTER_VALIDATE_URL);
	  //$personMarkup 		= "$email<div><img src='$profile_image_url?sz=50'></div>";
	  $_SESSION['token'] 	= $gClient->getAccessToken();
	  
	  $check = $db->select("tbl_users",array("id"),array("email"=>$email));
	  if($check->affectedRows() == 0)
	  {	  
		  $objPost->name = $username;
		  $objPost->email = $email;
		  $objPost->createddate = date('Y-m-d H:i:s');
		  $objPost->ipaddress = get_ip_address();
		  $objPost->isactive = 'y';
		  $password = genrateRandom(6);
		  $objPost->password = md5($password);
		  $uId = $db->insert("tbl_users",(array)$objPost)->getLastInsertId();
		  if($uId > 0)
		  {
				$_SESSION["sessUserId"] = $uId;
				$db->insert("tbl_user_social",array("userid"=>$uId,"firstname"=>$username,"lastname"=>$username,"email"=>$email,"url"=>$url,"status"=>'y',"createddate"=>$objPost->createddate,"socialtype"=>3,"ipaddress"=>$objPost->ipaddress));
				
				$to = $objPost->email;
				$greetings = $objPost->name;
				$subject = 'Thank you for the sign up with '.SITE_NM;	
				$key = array('{{GREETING}}','{{EMAIL}}','{{PASSWORD}}','{{ACTIVATION_LINK}}');
				$val = array($objPost->name,$objPost->email,$password,"");
				$message = generateEmailTemplate(1, $key, $val);
			 	sendEmailAddress($to, $subject, $message);
								
		  }
		  else
		  {
			redirectPage(SITE_URL."registration");
		  }
            
          	redirectPage(SITE_URL."profile/".$uId);	
	}
	  else
	  {
		  $fetchUser = $check->result();
		  $_SESSION["sessUserId"] = $fetchUser['id'];
		  redirectPage(SITE_URL."profile/".$fetchUser['id']);
	  }
	  
	  
}
else 
{
	//For Guest user, get google login url
	$authUrl = $gClient->createAuthUrl();
	redirectPage($authUrl);
}
 

?>

