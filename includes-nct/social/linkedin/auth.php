<?php
	
   	require_once('../../config-nct.php');

	$config['base_url']         = 		LINKEDIN_BASE_URL;
    $config['callback_url']         =   LINKEDIN_CALLBACK_URL;
    $config['linkedin_access']      =   LINKEDIN_ACCESS;
    $config['linkedin_secret']      =   LINKEDIN_SECRET;
	
	// [ARUN]  check weather it is come from login page or from account page
	//$_SESSION["ses_pagename"] = $_SERVER['HTTP_REFERER'];

    include_once "linkedin.php";

    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
	
	$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'],$config['callback_url']);
    //$linkedin->debug = true;

    # Now we retrieve a request token. It will be set as $linkedin->request_token
    $linkedin->getRequestToken();
    $_SESSION['requestToken'] = serialize($linkedin->request_token);
  	
    # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
   
    header("Location: " . $linkedin->generateAuthorizeUrl());
	//http://worldwidetutors.ncryptedprojects.com/auth/callback
?>
