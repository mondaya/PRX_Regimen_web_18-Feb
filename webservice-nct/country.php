
<?php
	require_once("../includes-nct/config-nct.php");
	$module = 'registration';
	$request_data=$_REQUEST;
	$action = (isset($request_data['action'])&&$request_data['action']!='')?$request_data['action']:NULL;
	$objHome=new Registration($db,$module);
	
	if($action == 'getState'){
		$json_content = $objHome->getState_api();
		echo $json_content;
	}else if($action == 'securityQuestions'){
		$json_content = $objHome->securityQuestions_api($request_data);
		echo $json_content;
	}else if($action == 'captcha'){
		$json_content = $objHome->captcha_api($request_data);
		echo $json_content;
	}else if($action == 'resendMail'){
		$json_content = $objHome->resendMail_api($request_data);
		echo $json_content;	
	}else{
		$res_array = array();
		$usrData = array();
		
		extract($request_data);
				
		$usrData['userType'] = $userType;
		$usrData['firstName'] = $firstName;
		$usrData['lastName'] = $lastName;
		$usrData['email'] = strtolower(trim($email));		
		$usrData['country'] = $country;
		$usrData['userName'] = strtolower($userName);
				
		if ($userType != '' && $firstName != '' && $email != '' && $userName != '' && $password != '' && $cPassword != '' ) {
			$details = $db->pdoQuery('SELECT uId,userName from tbl_users where email = "'.$usrData['email'].'" ')->result(); 
			$is_userName = $db->pdoQuery('SELECT uId from tbl_users where userName = "'.$usrData['userName'].'" ')->result(); 
			
			if($password!==$cPassword) {
				$res_array['message'] = "Password and confirm password not match";
				$status = false;
				
			}else if(isset($details['uId']) && $details['uId']>0){
				$res_array['message'] = "Email already exist. Please enter a different email.";
				$status = false;
				
			}else if(isset($is_userName) && $is_userName != ''){
				$res_array['message'] = "User name already exist. Please enter a different user name.";
				$status = false;
				
			}else{
				
				$usrData['createdDate'] = date('Y-m-d H:i:s');
				$usrData['password'] = md5($password);
				$usrData['activationCode'] = genrateRandom(10);
				$usrData['isActive'] = 'n';
				$usrData['loginType'] = 'n';
				
				$db->insert("tbl_users", $usrData);
				$lastInsertId =  $db->getLastInsertId(); 
				
				//For profile photo
				if(isset($_FILES["profilePhoto"])){
					$profilePhoto = $_FILES["profilePhoto"]["name"];
					$uploadFilePath = DIR_UPD.'profile/'.$lastInsertId.'/';
					if(isset($profilePhoto) && $profilePhoto != ''){
						if (! file_exists($uploadFilePath))  {
							 mkdir($uploadFilePath);
						}
						$name = genrateRandom(10).'.jpg';
						// move_uploaded_file($name, $uploadFilePath.$profilePhoto);
						move_uploaded_file($_FILES['profilePhoto']['tmp_name'], $uploadFilePath.$name);
						$db->update('tbl_users', array('profilePhoto'=>$name), array("uId"=>$lastInsertId));
					}
				}
				
				//Mail to user
				$to = $email;
				$greetings = $firstName;
				$userName = $userName;
				$email = $email;
				$link = '<a href="'.SITE_MOD.'registration-nct/activation.php?token='.$usrData['activationCode'].'" title="Please click here to activate your account">Please click here to activate your account</a>';
				$subject = 'Thank you for the registration!';
				$replace = array('greetings'=>$greetings,'email'=>$email,'username'=>$userName,'activationLink'=>$link,'adminName'=>SITE_NM);
				
				$message = generateEmailTemplate('user_register',$replace);
				
				sendEmailAddress($to, $subject, $message);
				
				$status = true;
				$res_array['message']='Registration successful. Please check your mail for account activation link.';
				$res_array['userId'] = $lastInsertId;
			}
		} 
		else {
			$status = "Please provide all required details.";		
			$res_array['status']=false;	
		}
			$res_array['status']=$status;
	    $res_json=json_encode($res_array);
	   	echo $res_json;
	}
?>	