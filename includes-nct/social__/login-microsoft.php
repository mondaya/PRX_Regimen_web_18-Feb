<?php
	require('microsoft/http.php');
	require('microsoft/oauth_client.php');
	require('config/microsoft_config.php');
	$client = new oauth_client_class;
	$client->server = 'Microsoft';
	$client->debug = false;
	$client->debug_http = true;
	$client->client_id = CLIENT_ID;//Your Client ID
	$application_line = __LINE__;
	$client->client_secret = CLIENT_SECRET; // Your Client Secret
	$client->redirect_uri = REDIRECT_URI;
	die($client->client_id."---".$client->client_secret."---".$client->redirect_uri);

	if(strlen($client->client_id) == 0
	|| strlen($client->client_secret) == 0)
		die('Please go to Microsoft Live Connect Developer Center page '.
			'https://manage.dev.live.com/AddApplication.aspx and create a new'.
			'application, and in the line '.$application_line.
			' set the client_id to Client ID and client_secret with Client secret. '.
			'The callback URL must be '.$client->redirect_uri.' but make sure '.
			'the domain is valid and can be resolved by a public DNS.');
	$client->scope = 'wl.basic wl.emails';
	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->authorization_error))
			{
				$client->error = $client->authorization_error;
				$success = false;
			}
			elseif(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://apis.live.net/v5.0/me',
					'GET', array(), array('FailOnAccessError'=>true), $user);
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
	if($success)
	{
		$_SESSION['data']=$user;
		$fnm=$user->first_name;
		$lnm=$user->last_name;
		$email=$user->email->account;
		$user = new User();
        $userdata = $user->checkUser($fnm,$lnm,$email);
        if(!empty($userdata))
	 	{
            session_start();
            $_SESSION['id'] = $userdata['id'];
			$_SESSION['email'] = $email;
			$_SESSION['username']=$username;
            header("Location: home.php");
            exit;
        }		
	}
	else
	{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>OAuth client error</title>
</head>
<body>
<h1>OAuth client error</h1>
<pre>Error: <?php echo HtmlSpecialChars($client->error); ?></pre>
</body>
</html>
<?php
	}

?>