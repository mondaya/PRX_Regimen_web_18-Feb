<?php
	$module = 'registration-nct';
	require_once("../../includes-nct/config-nct.php");
	require_once("class.registration-nct.php");

	$winTitle = 'Registration '.SITE_NM;
	$headTitle = 'Registration';
	$metaTag = getMetaTags(array("description"=>$winTitle,"keywords"=>$headTitle,"author"=>AUTHOR));
	$table = "tbl_users";
	$objReset=new Registration();

	$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');
	$provider = isset($_GET["provider"]) ? trim($_GET["provider"]) : (isset($_POST["provider"]) ? trim($_POST["provider"]) : '');
	if($sessUserId > 0){ redirectPage(SITE_URL); }

	if(isset($_POST['action']) && $_POST['action']='sbt_regi') {
		extract($_POST);
		$objPost->salute = isset($salute)?$salute:'';
		$objPost->firstName = isset($firstName)?$firstName:'';
		$objPost->lastName = isset($lastName)?$lastName:'';
		$objPost->email = isset($email)?$email:'';
		$objPost->secret = isset($secret)?$secret:'';
		$objPost->country   = isset($country) ? $country : '';
		$objPost->state   = isset($state) ? $state : '';
		$objPost->city   = isset($city) ? $city : '';
		$objPost->gen   = isset($gen) ? $gen : '';
		$objPost->address   = isset($address) ? $address : '';
		$objPost->paypalEmail   = isset($paypalEmail) ? $paypalEmail : '';
		$objPost->password1 = isset($password1)?$password1:'';
		$objPost->cpass = isset($cpassword)?$cpassword:'';
		$objPost->zipcode   = !empty($zip)?$zip:0;
		$objPost->countryCode   = !empty($countryCode)?$countryCode:0;
		$objPost->mobile   = !empty($mobile)?$mobile:0;
		$objPost->member  =  date('Y-m-d H:i:s');
		$objPost->ip   = get_ip_address();

		if(!empty($objPost->firstName)) {
			if($password1 != $cpassword) {
				$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Password not match"));
			} else {
				$isExist = $db->select('tbl_users',array('id'),array('email'=>$email), ' LIMIT 1');
				if($isExist->affectedRows() > 0) {
					$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Email you Entered already exists..!!"));
				} else {
					$insertarray=array("firstName"=>$firstName,"lastName"=>$lastName,"secret"=>$secret,"email"=>base64_encode($email),"password"=>md5($password1),"countryId"=>$country,"stateId"=>$state,"cityId"=>$city,"zipCode"=>$zip,"gender"=>$gen,"code"=>$countryCode,"mobileNumber"=>$mobile,"address"=>$address,"paypalEmail"=>base64_encode($paypalEmail),"isActive"=>'n','salute'=>$objPost->salute,"member"=>$objPost->member);
					$activationCode = $insertarray['activationCode'] = md5(time());

					$insert_id=$db->insert('tbl_users',$insertarray)->getLastInsertId();

					//For referral module
					$referralId = getTableValue('tbl_referral_users','id',array('email'=>$email));
					if($referralId > 0){
						$db->update('tbl_referral_users',array('isRegister'=>'y'),array('id'=>$referralId));
					}

					if($insert_id>0) {
						$activation_link = SITE_URL.'activation/'.base64_encode($insert_id).'/'.$activationCode.'/';
						$to = $email;

						$arrayCont = array('greetings'=>$insertarray['firstName'],'activationLink'=>$activation_link);
						sendMail($to, 'signup', $arrayCont);

						$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Thankyou. Your activation link is sent to your mail account. Kindly visit the link to activate your account'));
					} else {
						$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"Oops...!! Something went wrong,registration failed.."));
					}
				}
			}
		} else {
			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>"fillAllvalues"));
		}
	}

	if(isset($_GET['action']) && $_GET['action']=='activation' && !empty($_GET['id']) && !empty($_GET['activationCode'])) {
		$id = base64_decode($_GET['id']);
		$active = $db->select('tbl_users', array('id','isActive'), array('id'=>$id,'activationCode'=>$_GET['activationCode']))->result();
		if(!empty($active) && !empty($active['id'])) {
			if($active['isActive']=='n') {
				$db->update('tbl_users', array('isActive'=>'y','activationCode'=>''), array('id'=>$id));
				$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Account activated successfully'));
			} else {
				$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Activation link has been expired.'));
			}
		} else {
			$msgType = $_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'Activation link has been expired.'));
		}
		redirectPage(SITE_URL);
	}

	$final_result = NULL;
	$main_content = new Templater(DIR_TMPL."registration-nct/registration-nct.tpl.php");
	$final_result = $main_content->parse();
	$opt_content = new Templater(DIR_TMPL.'option-nct.tpl.php');
	$opt_content = $opt_content->parse();
	$opt_field = array('%VALUE%','%NAME%','%SELECTED%');
	$selCountry = $db->select('tbl_country',array('id','countryName'),array('isActive'=>'y'));
	$country_content = '<option value="">--Please Select Country*--</option>';
	$state_content = '<option value="">--Please Select State*--</option>';
	$city_content = '<option value="">--Please Select City*--</option>';
	if($selCountry->affectedRows()>0){
		$countries = $selCountry->results();
		foreach($countries as $country){
			$opt_field_replace = array($country['id'],$country['countryName'],'');
			$country_content .= str_replace($opt_field,$opt_field_replace,$opt_content);
		}
	}
	$email = $firstname =$lastname= $password = $cpassword= "";
	$fields = array('%FIRSTNAME%','%LASTNAME%','%EMAIL%','%PASSWORD%','%CPASSWORD%','%OPT_COUNTRY%','%OPT_STATE%','%OPT_CITY%');
	$fields_replace = array($firstname,$lastname,$email,$password,$cpassword,$country_content,$state_content,$city_content);
	$pageContent= str_replace($fields,$fields_replace,$final_result);
	require_once(DIR_TMPL."parsing-nct.tpl.php");
?>