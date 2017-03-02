<?php
	$module = 'home-nct';
	require_once("../../includes-nct/config-nct.php");
	require_once("class.home-nct.php");

    $winTitle = 'Welcome to '.SITE_NM;
    $headTitle = 'Home';
    $metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));
    $objMain = new Home();

    if(isset($_POST['btnSubscribe']) && !empty($_POST['subEmail'])) {
    	extract($_POST);
    	$objPost->email = !empty($subEmail)?base64_encode($subEmail):'';
    	if(!empty($objPost->email)) {
    		$exist = getTableValue('tbl_newsletter_subscriber', 'id', array('email'=>$objPost->email));
    		if(empty($exist)) {
    			$objPost->hash = md5(rand(0,1000));
    			$objPost->is_active = 'n';
    			$objPost->created_date = date('Y-m-d H:i:s');
    			$objPost->ipaddress = get_ip_address();
    			$sub_id = $db->insert('tbl_newsletter_subscriber', (array)$objPost)->lastInsertId();
    			$arrayCont = array('activationLink'=>SITE_URL.'newsletter/verify/'.$sub_id);
				sendMail($subEmail,'newlater_succ',$arrayCont);
				$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'sucNewslater'));
    		} else {
    			$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'emailExist'));
    		}
    		redirectPage(SITE_URL);
    	} else {
    		$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'fillAllvalues'));
    	}
    }

    if(isset($_POST['login-submit'])) {
        extract($_POST);
        $objPost = new stdClass();
        $objPost->email = isset($username) ? $username : '';
        $objPost->password = isset($password) ? $password : '';
        $objPost->secret = isset($secret) ? $secret : '';
        $objPost->isRemember = isset($isRemember) ? $isRemember : 'n';

        if(isset($objPost->email) && $objPost->email != '' && isset($objPost->password) && $objPost->password != '') {
            $selUser = $db->select('tbl_users',array('id','firstName','isActive'), array('email'=>base64_encode($objPost->email),'password'=>md5($objPost->password),'secret'=>$objPost->secret), ' LIMIT 1');
            if($selUser->affectedRows() > 0){
                $userDetails = $selUser->result();
                if($userDetails['isActive'] == 'y') {
                    $_SESSION['sessUserId'] = $userDetails['id'];
                    $_SESSION['sessUserName'] = $userDetails['firstName'];
                    if (isset($objPost->isRemember) && $objPost->isRemember=='y') {
                        setcookie('email',$objPost->email,time()+3600*24*30,'/');
                        setcookie('password',$objPost->password,time()+3600*24*30,'/');
                        setcookie('secret',$objPost->secret,time()+3600*24*30,'/');
                        setcookie('isRemember',$objPost->isRemember,time()+3600*24*30,'/');
                    } else {
                        setcookie('email','',time()-3600,'/');
                        setcookie('password','',time()-3600,'/');
                        setcookie('secret','',time()-3600,'/');
                        setcookie('isRemember','',time()-3600,'/');
                    }
                    $_SESSION["msgType"] = $msgType = disMessage(array('type'=>'suc','var'=>"Login Successfull"));
                    redirectPage(SITE_URL);
                } else{
                    $_SESSION["msgType"] = $msgType = disMessage(array('type'=>'err','var'=>"Your account is not active by admin."));
                }
            } else{
                $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Invalid Username or password or secret"));
            }
        } else {
            $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"fillAllvalues"));
        }
        redirectPage(SITE_URL);
    }

    if(isset($_POST['forgot-submit']) && $_SERVER['REQUEST_METHOD']=="POST") {
        $emailaddress = ($_POST['forgot_username'])?base64_encode($_POST['forgot_username']):'';
        if(!empty($emailaddress)) {
            $queryRes = $db->pdoQuery("SELECT * FROM tbl_users WHERE email='".$emailaddress."' AND isActive ='y'");
            $emailpresent = $queryRes->affectedRows();
            if($emailpresent > 0){
                $userdata=$queryRes->result();
                $password=generatePassword(6);
                $passwordmd5=md5($password);
                $activationCode=$userdata['activationCode'];
                $username=$userdata['firstName'];
                $secret=$userdata['secret'];
                $newpassword = $password;
                $finalpassword = $passwordmd5;
                $id=$userdata['id'];
                $to=$userdata['email'];

                $valArray = array('password'=>$finalpassword);

                $db->update('tbl_users',$valArray,array('email'=>$emailaddress));
                    
                    $conarray=array(
                        'greetings'=>$username,
                        'EMAIL'=>base64_decode($userdata['email']),
                        'PASSWORD'=>$password,
                        'SECRET'=>$secret
                    );

                    sendMail(base64_decode($to),"forgot_password",$conarray);

                $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>"Please check your E-mail. New password has been successfully sent to your mail Account."));
                 redirectPage(SITE_URL);
            } else {
                $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Opps.. email entered is wrong..!"));
            }
        } else {
            $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Email is Required"));
        }
    }

    if(isset($_SESSION['User']) && !empty($_SESSION['User']['id']) && !empty($_SESSION['User']['login_from'])) {
        $exist = 0;
        if($_SESSION['User']['login_from']=='f') {
            $objPost->email = isset($_SESSION['User']['email'])?base64_encode(filtering($_SESSION['User']['email'],'input','string','')):"";
            $objPost->firstName = isset($_SESSION['User']['first_name'])?filtering($_SESSION['User']['first_name'],'input','string',''):"";
            $objPost->lastName = isset($_SESSION['User']['last_name'])?filtering($_SESSION['User']['last_name'],'input','string',''):"";
            $objPost->gender = isset($_SESSION['User']['gender'])?filtering($_SESSION['User']['gender'],'input','string',''):"";
            //$objPost->isActive = 'n';

            if(!empty($objPost->email) && !empty($objPost->firstName) && !empty($objPost->lastName) && !empty($objPost->gender)) {
                $_SESSION['User'] = ''; $_SESSION['User']['first_name'] = ''; $_SESSION['User']['last_name'] = ''; $_SESSION['User']['last_name'] = ''; unset($_SESSION['User']);

                $exist = $db->select('tbl_users', array('id, password, isActive'), array('email'=>$objPost->email))->result();
                if(!empty($exist) && !empty($exist['id'])) {
                    
                    $_SESSION['sessUserId'] = $exist['id'];
                    $_SESSION['sessUserName'] = $exist['firstName'];
                    $_SESSION["msgType"] = $msgType = disMessage(array('type'=>'suc','var'=>"Login Successfull"));
                    redirectPage(SITE_URL);
                    
                } else {
                    $objPost->activationCode = md5(time());
                    $user_password = genrateRandom(6);
                    $objPost->password = md5($user_password);
                    $objPost->isActive = 'y';
                    $objPost->profileImage = md5(date("Y-m-d H:i:s") + rand()) . '.jpg';
                    $insert_id = $db->insert('tbl_users',(array)$objPost)->getLastInsertId();

                    $th1 = 'http://graph.facebook.com/' . $_SESSION['User']['id'] . '/picture?width=650&height=650';
                    $dir = DIR_UPD_USER.$insert_id;
                    mkdir($dir, 0777);
                    $uploadPath = $dir.'/'.$objPost->profileImage;
                    copy($th1, $uploadPath);

                    //$activation_link = SITE_URL.'activation/'.base64_encode($insert_id).'/'.$objPost->activationCode.'/';
                    //$arrayCont = array('greetings'=>$objPost->firstName,'PASSWORD'=>$user_password);
                    //sendMail(base64_decode($objPost->email), 'signup_social', $arrayCont);
                    $_SESSION['sessUserId'] = $insert_id;
                    $_SESSION['sessUserName'] = $objPost->firstName;
                    
                    $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Login Successfull'));
                    redirectPage(SITE_URL);
                }
            }
        } else if($_SESSION['User']['login_from']=='g') {
            $objPost->email = isset($_SESSION['User']['email'])?base64_encode(filtering($_SESSION['User']['email'],'input','string','')):"";
            
            $name = explode(' ',$_SESSION['User']['name']);
            
            $objPost->firstName = isset($name[0])?filtering($name[0],'input','string',''):"";
            $objPost->lastName = isset($name[1])?filtering($name[1],'input','string',''):$objPost->firstName;
            //$objPost->gender = isset($_SESSION['User']['gender'])?filtering($_SESSION['User']['gender'],'input','string',''):"";
            //$objPost->isActive = 'n';
            //echo $objPost->email.'/'.$objPost->firstName.'/'.$objPost->lastName.'/'.$objPost->gender;exit;
            if(!empty($objPost->email) && !empty($objPost->firstName) && !empty($objPost->lastName)) {
                $_SESSION['User'] = ''; $_SESSION['User']['first_name'] = ''; $_SESSION['User']['last_name'] = ''; $_SESSION['User']['last_name'] = ''; unset($_SESSION['User']);

                $exist = $db->select('tbl_users', array('id, password, isActive'), array('email'=>$objPost->email))->result();
                if(!empty($exist) && !empty($exist['id'])) {
                    
                    $_SESSION['sessUserId'] = $exist['id'];
                    $_SESSION['sessUserName'] = $exist['firstName'];
                    $_SESSION["msgType"] = $msgType = disMessage(array('type'=>'suc','var'=>"Login Successfull"));
                    redirectPage(SITE_URL);
                    
                } else {
                    
                    $objPost->activationCode = md5(time());
                    $user_password = genrateRandom(6);
                    $objPost->password = md5($user_password);
                    $objPost->isActive = 'y';
                    $objPost->profileImage = md5(date("Y-m-d H:i:s") + rand()) . '.jpg';
                    $insert_id = $db->insert('tbl_users',(array)$objPost)->getLastInsertId();

                    $th1 = $_SESSION['User']['picture'];
                    $dir = DIR_UPD_USER.$insert_id;
                    mkdir($dir, 0777);
                    $uploadPath = $dir.'/'.$objPost->profileImage;
                    copy($th1, $uploadPath);

                    //$activation_link = SITE_URL.'activation/'.base64_encode($insert_id).'/'.$objPost->activationCode.'/';
                    //$arrayCont = array('greetings'=>$objPost->firstName,'EMAIL'=>$user_password,'PASSWORD'=>$user_password);
                    //sendMail(base64_decode($objPost->email), 'signup_social', $arrayCont);
                    $_SESSION['sessUserId'] = $insert_id;
                    $_SESSION['sessUserName'] = $objPost->firstName;
                    
                    $msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Login Successfull'));
                    redirectPage(SITE_URL);
                }
            }
        }
       // redirectPage(SITE_URL);
    }

    $slider = $objMain->get_slider();
    $banner = $objMain->get_banner();
    $stores = $objMain->stores();

    $final_result = NULL;
    $main_content = new Templater(DIR_TMPL.$module."/".$module.".tpl.php");
    $pageContent = $main_content->parse();

    //Get home content info
    $query = $db->select("tbl_home_contant","*")->result();
    $fetchRes = $query;

    //print_r($fetchRes);

    $find = array("%INDICATORS%", "%SLIDER%", "%BANNER%", "%TOP_CATEGORY%","%STEP1TITLE%","%STEP1DESC%","%STEP2TITLE%","%STEP2DESC%","%STEP3TITLE%","%STEP3DESC%","%WHYWLLNESS%","%WHYTITLE%",'%STORES%');
    $replace = array($slider['indicators'], $slider['slider_html'], $banner, $objMain->getCategory(),html_entity_decode(htmlspecialchars($fetchRes['step1Title'])),html_entity_decode(htmlspecialchars($fetchRes['step1Desc'])),html_entity_decode(htmlspecialchars($fetchRes['step2Title'])),html_entity_decode(htmlspecialchars($fetchRes['step2Desc'])),html_entity_decode(htmlspecialchars($fetchRes['step3Title'])),html_entity_decode(htmlspecialchars($fetchRes['step3Desc'])),html_entity_decode(htmlspecialchars($fetchRes['whyWellness'])),$fetchRes['whyTitle'],$stores);
    $pageContent = str_replace($find, $replace, $pageContent);
	require_once(DIR_TMPL."parsing-nct.tpl.php");
?>