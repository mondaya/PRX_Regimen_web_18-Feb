<?php
require_once '../config.php';
require_once 'facebook/facebook.php';
$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
            ));

$user = $facebook->getUser();

if ($user) 
{
  try 
  {  
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
    
  }
  catch (FacebookApiException $e) 
  {
    error_log($e);
	echo $e;
    $user = null;
  }
	//if information fetch from facebook
    if (!empty($user_profile )) 
	{
		# User info ok? Let's print it (Here we will be adding the login and registering routines)
        $username = $user_profile['name'];
		$fnm=$user_profile['first_name'];
		$lnm=$user_profile['last_name'];
		$uid = $user_profile['id'];
		$email = $user_profile['email'];
		$url = $user_profile['link'];
       
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
				$db->insert("tbl_user_social",array("userid"=>$uId,"firstname"=>$fnm,"lastname"=>$lnm,"email"=>$email,"url"=>$url,"status"=>'y',"createddate"=>$objPost->createddate,"socialtype"=>1,"ipaddress"=>$objPost->ipaddress));
				
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
	else #if information not fatched from facebook means any typoe of problem regrading login
	{
        # For testing purposes, if there was an error, let's kill the script
        die("There was an error.");
    }
} 
else 
{
    # There's no active session, let's generate one
	$loginurl = $facebook->getLoginUrl(array(
				'scope'			=> 'email,read_stream, publish_stream, user_birthday, user_location, user_work_history, user_hometown, user_photos',
				'redirect_uri'	=> SITE_URL.'login-facebook',
				'display'=>'popup'
				));
				redirectPage($loginurl);
	
}
?>
<!-- after authentication close the popup -->
<script type="text/javascript">
window.close();
</script>
