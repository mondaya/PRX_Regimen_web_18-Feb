<?php
//Always place this code at the top of the Page

if (isset($_SESSION['id'])) 
{
    // Redirection to login page twitter or facebook
    header("location: home.php");
}
?>
<title>All Social Login Customized BY HR</title>
<style type="text/css">
    #buttons
	{
	text-align:center
	}
    #buttons img,
    #buttons a img
    { border: none;}
	h1
	{
	font-family:Arial, Helvetica, sans-serif;
	color:#999999;
	}
	
</style>



<div id="buttons">
<h1>Twitter Facebook Login </h1>
    <a href="login-twitter.php?login&oauth_provider=twitter"><img src="images/tw_login.png"></a>&nbsp;&nbsp;&nbsp;
    <a href="login-facebook.php?login&oauth_provider=facebook"><img src="images/fb_login.png"></a> <br />
    <?php include("login-gmail.php");?>
    <a href="login-linkedin.php?login&oauth_provider=linkedin"><img src="images/linkedin_login.png"></a> <br />
    <a href="login-microsoft.php?login&oauth_provider=microsoft"><img src="images/microsoft_login.png"></a> <br />
	<br />
</div>
