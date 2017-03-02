<?php
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$module = 'registration';
	$request_data=$_REQUEST;
	
	$action = (isset($request_data['action'])&&$request_data['action']!='')?$request_data['action']:NULL;
	$countryId = isset($request_data['countryId'])?$request_data['countryId']:'';
	$stateId = isset($request_data['stateId'])?$request_data['stateId']:'';
	$userid = isset($request_data['userId'])?$request_data['userId']:0;
	if(strtolower($action) == 'getaccountsetting'){
		$json_content = getAccountSettig_api($userid);
		echo $json_content;
	}else if($action == 'getCountryState'){
		$json_content = getCountrySate_api();
		echo $json_content;
	}else if($action == 'getState'){
		$json_content = getState_api($countryId);
		echo $json_content;
	}else if($action == 'getCity'){
		$json_content = getCity_api($countryId,$stateId);
		echo $json_content;
	}else if($action == 'getRegister'){
		extract($_REQUEST);

		$objPost->salute = isset($salute)?$salute:'';
		$objPost->firstName = isset($firstName)?$firstName:'';
		$objPost->lastName = isset($lastName)?$lastName:'';
		$objPost->email = strtolower(isset($email)?$email:'');
		$objPost->secret = isset($secret)?$secret:'';
		$objPost->country   = isset($country) ? $country : '';
		$objPost->state   = isset($state) ? $state : '';
		$objPost->city   = isset($city) ? $city : '';
		$objPost->gen   = isset($gen) ? $gen : '';
		$objPost->address   = isset($address) ? $address : '';
		$objPost->paypalEmail   = isset($paypalEmail) ? $paypalEmail : '';
		$objPost->password1 = isset($password1)?$password1:'';
		$objPost->cpass = isset($cpassword)?$cpassword:'';
		$objPost->zipcode   = isset($zip)?$zip:'';
		$objPost->countryCode   = isset($countryCode)?$countryCode:'';
		$objPost->mobile   = isset($mobile)?$mobile:'';
		$objPost->member  =  date('Y-m-d H:i:s');
		$objPost->ip   = get_ip_address();
		$objPost->deviceToken =isset($deviceToken)?$deviceToken:'';

		if(!empty($objPost->firstName)) {
			if($password1 != $cpassword) {
				$res_array['status']=false;
				$res_array['message']='Password not match.'; 

   				echo json_encode($res_array);
			} else {
				if($objPost->email=='')
				{
					$res_array['status']=false;
					$res_array['message']='Fill email field.';    
	   				echo json_encode($res_array);
	   				exit;
				}
				$isExist = $db->select('tbl_users',array('id'),array('email'=>base64_encode($email)));
				if($isExist->affectedRows() > 0) {
					$res_array['status']=false;
					$res_array['message']='Email you are entered already exists..!!';    
	   				echo json_encode($res_array);
	   			} else {

					$insertarray=array("firstName"=>$objPost->firstName,"lastName"=>$objPost->lastName,"secret"=>$objPost->secret,"email"=>base64_encode($objPost->email),"password"=>md5($objPost->password1),"countryId"=>$objPost->country,"stateId"=>$objPost->state,"cityId"=>$objPost->city,"zipCode"=>$objPost->zipcode,"gender"=>$objPost->gen,"code"=>$objPost->countryCode,"mobileNumber"=>$objPost->mobile,"address"=>$objPost->address,"paypalEmail"=>base64_encode($objPost->paypalEmail),"isActive"=>'n','salute'=>$objPost->salute,"createdDate"=>$objPost->member,'deviceToken'=>$objPost->deviceToken,"member"=>$objPost->member);
					$activationCode = $insertarray['activationCode'] = md5(time());
					$insert_id=$db->insert('tbl_users',$insertarray)->getLastInsertId();

					//For referral module
					$referralId = getTableValue('tbl_referral_users','id',array('email'=>$email));
					if($referralId > 0){
						$db->update('tbl_referral_users',array('isRegister'=>'y'),array('id'=>$referralId));
					}
					if($insert_id>0) {

						$notidata=array(
							'newProductPosted'=>'y',
							'amountAddedInWallet'=>'y',
							'receiveReplayFromAdmin'=>'y',
							'receiveReplayFromAdmin'=>'y',
							'newPrormoPosted'=>'y',
							'orderStatusByAdmin'=>'y',
							'reminder'=>'y',
							'createdDate'=>date('Y-m-d H:i:s'),
							'userId'=>$insert_id
							);
						$db->insert('tbl_notifications',$notidata);

						$datasubsribe =array(
							'email'=>base64_encode($objPost->email),	
							'is_active'=>'y',
							'created_date'=>date('Y-m-d H:i:s'),
							'ipaddress'=>get_ip_address(),
							);
						$db->insert('tbl_newsletter_subscriber',$datasubsribe);

						$activation_link = SITE_URL.'activation/'.base64_encode($insert_id).'/'.$activationCode.'/';
						$to = $email;
						$arrayCont = array('greetings'=>$insertarray['firstName'],'activationLink'=>$activation_link);
						sendMail($to, 'signup', $arrayCont);
						$query = "select * from tbl_users where (email='" . base64_encode($objPost->email) . "' and password='" . md5($objPost->password1) . "')";
						$result = $db->pdoQuery($query);
						$fetchRes = $result->result();
						$uId=$fetchRes['id'];
						$res_array['userid'] = $fetchRes['id'];
						$res_array['salute'] = $fetchRes['salute'];
						$res_array['firstName'] = $fetchRes['firstName'];
						$res_array['lastName'] = $fetchRes['lastName'];
						$res_array['email'] = base64_decode($fetchRes['email']);
						$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
						$res_array['address']=$fetchRes['address'];
						$res_array['zipCode']=$fetchRes['zipCode'];
						$res_array['countryId']=is_null($fetchRes['countryId'])?'':$fetchRes['countryId'];
						$countryName =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
						$res_array['countryName'] =is_null($countryName)?'':$countryName;
						$res_array['stateId']=is_null($fetchRes['stateId'])?'':$fetchRes['stateId'];
						$stateName=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
						$res_array['stateName']=is_null($stateName)?'':$stateName;
						$res_array['cityId']=is_null($fetchRes['cityId'])?'':$fetchRes['cityId'];
						$cityName =getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
						$res_array['cityName']=is_null($cityName)?'':$cityName;
						$res_array['countryCode']=$fetchRes['code'];
						$res_array['mobile']=$fetchRes['mobileNumber'];
						$res_array['secret']=$fetchRes['secret'];
						$res_array['gen']=$fetchRes['gender'];
						$res_array['member']=$fetchRes['createdDate']=="0000-00-00 00:00:00"?'':$fetchRes['createdDate'];
						$res_array['ip']=$fetchRes['ipaddress'];
						$res_array['birthDate']=$fetchRes['birthDate']=="0000-00-00 00:00:00" || $fetchRes['birthDate']=="0000-00-00"?'':$fetchRes['birthDate'];
						$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
						$res_array['deviceToken']=$fetchRes['deviceToken'];	
						$res_array['status']=true;
						$res_array['message']='Thank you. Your activation link is sent to your email account. Kindly visit the link to activate your account';    
		   				echo json_encode($res_array);
						
					} else {
						$res_array['status'] =false;
						$res_array['message'] ='Oops...!! Something went wrong,registration failed..';    
		   				echo json_encode($res_array);
		   			}
				}
			}
		} else {
			$res_array['status']=false;
			$res_array['message']='Fill all values.';    
			echo json_encode($res_array);
			
		}
	}else{

			$res_array['status']=false;
			$res_array['message']='invalid action.';    
			echo json_encode($res_array);	
	
	}
?>	