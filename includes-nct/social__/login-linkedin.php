<?php
require_once '../config-nct.php';
require_once 'linkedin/linkedin.php';//class
$api_key = LINK_APP_ID;
$api_secret = LINK_APP_SEC;
$callback_url = SITE_URL."social/linkedin";
$scope = array('r_basicprofile','r_emailaddress');

$config = array('api_key' => $api_key, 'api_secret' => $api_secret , 'callback_url' => $callback_url);
$connection = new LinkedIn($config);

if (isset($_REQUEST['code'])) {
    $code = $_REQUEST['code'];
	$access_token = $connection->getAccessToken($code);
    $connection->setAccessToken($access_token);
    $user = $connection->get("people/~:(id,first-name,last-name,email-address,headline,picture-url)");
   
   echo  $fnm = $user['firstName'];
   echo  $lnm = $user['lastName'];
  echo   $email = $user['emailAddress'];
    die();
    $check = $db->select("tbl_users",array("id"),array("email"=>$email));
    if($check->affectedRows() <= 0)
    {	
        $objPost->name = trim($fnm." ".$lnm);
		$objPost->email = $email;
		$objPost->createddate = date('Y-m-d H:i:s');
		$objPost->ipaddress = get_ip_address();
		$objPost->isactive = 'y';
		$password = genrateRandom(6);
		$objPost->password = md5($password);
		$uId = $db->insert("tbl_users",(array)$objPost)->getLastInsertId();
		
		 if($uId > 0)
		  {
			  
			 	$url = "https://www.linkedin.com/profile/view?id=".$user['id']; 
			  	$_SESSION["sessUserId"] = $uId;
				$db->insert("tbl_user_social",array("userid"=>$uId,"firstname"=>$fnm,"lastname"=>$lnm,"email"=>$email,"url"=>$url,"status"=>'y',"createddate"=>$objPost->createddate,"socialtype"=>2,"ipaddress"=>$objPost->ipaddress));
				
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
    if (isset($_REQUEST['error'])) 
    {
        redirectPage(SITE_URL."registration");
    }
    else
    {
        $authUrl = $connection->getLoginUrl($scope);
        redirectPage($authUrl);
    }
}
?>