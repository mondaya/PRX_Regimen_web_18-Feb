<?php 
	require_once("../config.php");
	require_once(DIR_CLASSES."mail.inc.php");
    

    $config['base_url']             =   'http://www.wethepeople.ws/includes/social/auth.php';
    $config['callback_url']         =   'http://www.wethepeople.ws/includes/social/demo.php';
    $config['linkedin_access']      =   '75ntc7v0jj4ft7';
    $config['linkedin_secret']      =   'SlNFdUYix2zdgW27';

    include_once "linkedin.php";
   
    
    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['callback_url'] );
    //$linkedin->debug = true;

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
	$email=$a->{'email-address'};
	$firstname=$a->{'first-name'};
	$lastname=$a->{'last-name'};	
	if($email !="")
	{
	$totRow = $db->select('tbl_members', "id,userName,userType", "email='".$email."'");
		if(mysql_num_rows($totRow)>0)
		{
			///Already Exist user can Login
			$row = mysql_fetch_assoc($totRow);
				$_SESSION["uId"]=$row['id'];	
				$_SESSION["userName"]=$row['userName'];	
				$_SESSION["uType"]=$row['userType'];	
				$redirectUrl = SITE_MOD."Profile";
				redirectPage($redirectUrl);
		}
		else
		{
			///Inser User 
			$objPost  = new stdClass();
			$password = generateRandString();
			$objPost->email = isset($email) ? $db->filtering($email,'input','string','') : '';
			$objPost->firstName = isset($firstname) ? $db->filtering($firstname,'input','string','') : '';
			$objPost->lastName = isset($firstname) ? $db->filtering($firstname,'input','string','') : '';
			$objPost->password = isset($password) ? $db->filtering(md5($password),'input','string','') : '';
			$objPost->isActive = 'y';
			$objPost->createdDate = date('Y-m-d H:i:s');
			$objPost->ipAddress = get_ip_address();
			$db->insert('tbl_members', $objPost);
			$uId = mysql_insert_id();
			$objPost->userPassword = $password;
			varifyEmail($objPost);
			$_SESSION["uId"]=$uId;
			$_SESSION["msgType"] = array('type'=>'suc','var'=>REGSUCCESS);
			$redirectUrl = SITE_MOD."home/?login=social&type=linkedin";
			redirectPage($redirectUrl);
		}
	
		exit;
	}
	else
	{
		redirectPage(SITE_URL);
	}
  function varifyEmail($value){
        ######## Mail Format :start ###########
        
		$to = array('0'=> array('name' => $value->firstName,'email' => $value->email));
		$cc = array();
		$bcc = array();
		$read = array();
		$reply = array();
		$sender = FROM_EMAIL;
		$senderName = FROM_NM;
		$subject = 'Thanks for registration';
		$emailTitle = 'Thanks for registration!';
		
		$contArray = array("greetings"=>$value->firstName, "userName"=>$value->email, "password"=>$value->userPassword);
		
        $message = genreateEmailTemplate($emailTitle, 'user-facebook-register', $contArray);

		$obj = new sendMail($to, $sender, $subject, $message, $cc, $bcc,$senderName, $read, true, $reply, true);
		$obj->sendEmail();
		######## Mail Format :end ###########
    }
 

?>
