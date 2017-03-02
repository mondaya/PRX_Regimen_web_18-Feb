<?php
	require_once '../config-nct.php';
	require_once 'lib/facebook/facebook.php';

	//For Google
	require_once 'lib/google/Google_Client.php';
	require_once 'lib/google/Google_Oauth2Service.php';

	echo GOOGLE_CLIENT_ID;
	echo "<br />";
	echo GOOGLE_CLIENT_SECRET;
	echo "<br />";
	echo REDIRECT_URI;
	echo "<br />";
	echo APPROVAL_PROMPT;
	echo "<br />";
	echo ACCESS_TYPE;
	echo "<br />";
	exit;

	class Social{
		function facebook() {
			$facebook = new Facebook(array(
				'appId' =>  FB_APP_ID,
				'secret' => FB_APP_SECRET,
			));

			//get the user facebook id
			$user = $facebook->getUser();
			if($user){
				try{
					//get the facebook user profile data
					$user_profile = $facebook->api('/me?fields=id,email,first_name,last_name,gender,location');
					$params = array('next' => SITE_SOCIAL.'logout.php');

					//logout url
					$logout =$facebook->getLogoutUrl($params);
					$_SESSION['User']=$user_profile;
					$_SESSION['User']['login_from'] = 'f';
					$_SESSION['facebook_logout']=$logout;
				}catch(FacebookApiException $e){
					error_log($e);
					$user = NULL;
				}
			}

			if(empty($user)) {
				//login url
				$loginurl = $facebook->getLoginUrl(array(
					'scope' => 'email, user_birthday, user_location, user_work_history, user_hometown, user_photos',
					'redirect_uri' => SITE_SOCIAL.'login.php?facebook',
					'display' => 'popup'
				));
				header('Location: '.$loginurl);
			}
		}

		function google() {
			$client = new Google_Client();
			$client->setApplicationName(SITE_NM." Login Functionallity");
			$client->setClientId(GOOGLE_CLIENT_ID);
			$client->setClientSecret(GOOGLE_CLIENT_SECRET);
			$client->setRedirectUri(REDIRECT_URI);
			$client->setApprovalPrompt(APPROVAL_PROMPT);
			$client->setAccessType(ACCESS_TYPE);
			$oauth2 = new Google_Oauth2Service($client);

			if (isset($_GET['code'])) {
				$client->authenticate($_GET['code']);
				$_SESSION['token'] = $client->getAccessToken();
			}
			if (isset($_SESSION['token'])) {
				$client->setAccessToken($_SESSION['token']);
			}
			if (isset($_REQUEST['error'])) {
				echo '<script type="text/javascript">window.close();</script>'; exit;
			}
			if ($client->getAccessToken()) {
				$user = $oauth2->userinfo->get();
				$_SESSION['User']=$user;
				$_SESSION['User']['login_from'] = 'g';
				$_SESSION['token'] = $client->getAccessToken();
			} else {
				$authUrl = $client->createAuthUrl();
				header('Location: '.$authUrl);
			}
		}
	}
?>