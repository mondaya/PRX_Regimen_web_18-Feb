<?php
require_once("twitter/twitteroauth.php");
require_once('config/twconfig.php');

if (!empty($_GET['oauth_verifier']) && !isset($_POST['email']) ) 
{
?>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
Please Enter Your Email To Register:<input type="email" name="email" id="email" />
<input type="submit" value="Register" />
</form>
<?php
}
else if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret']) && isset($_POST['email'])) 
{
    // We've got everything we need
    $twitteroauth = new TwitterOAuth(YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	// Let's request the access token
    $access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
	// Save it in a session var
    $_SESSION['access_token'] = $access_token;
	// Let's get the user's info
    $user_info = $twitteroauth->get('account/verify_credentials');
	// Print user's info
    echo '<pre>';
    	print_r($user_info);
        $_SESSION['data']=$user_info;
    echo '</pre><br/>';
    if (isset($user_info->error)) 
	{
        // Something's wrong, go back to square 1  
        header('Location: login-twitter.php');
    } 
	else 
	{
	    $twitter_otoken=$_SESSION['oauth_token'];
	    $twitter_otoken_secret=$_SESSION['oauth_token_secret'];
	    $email=$_POST['email'];
        $uid = $user_info->id;
        $username = $user_info->name;
	    $nm=explode(" ",$username);
        $user = new User();
        $userdata = $user->checkUser($nm[0],$nm[1],$email);
        if(!empty($userdata)){
            session_start();
            $_SESSION['id'] = $userdata['id'];
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $_POST['email'];
            header("Location: home.php");
        }
    }
}
else 
{
    // Something's missing, go back to square 1
    header('Location: login-twitter.php');
}
?>