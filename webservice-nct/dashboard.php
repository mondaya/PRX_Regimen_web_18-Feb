<?php

	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$module = 'registration';
	
	$request_data=$_REQUEST;
	
	$action = (isset($request_data['action'])&&$request_data['action']!='')?$request_data['action']:NULL;
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	
	if($action == 'userProfile' && $userId>0){
		$json_content = getUserProfile_api($userId);
		echo $json_content;
	}else if($action == 'reminderOperation' && $userId>0){
		$json_content = reminderOperation_api($request_data);
		echo $json_content;
	}else if($action == 'reminderList' && $userId>0){
		$json_content = reminderList_api($request_data);
		echo $json_content;
	}else if($action == 'editUserProfile' && $userId>0){
		extract($request_data);
		$objPost->salute = isset($salute) ? $salute : 'mr';
		$objPost->firstName = isset($firstName) ? $firstName : '';
		$objPost->lastName = isset($lastName) ? $lastName : '';
		$objPost->secret = isset($secret) ? $secret : '';
		$objPost->gender = isset($gender) ? $gender : 'm';
		$objPost->birthDate = isset($birthDate) ? date('Y-m-d', strtotime($birthDate)) : date('Y-m-d');
		$objPost->address = isset($address) ? $address : '';
		$objPost->countryId = isset($country) ? $country : '';
		$objPost->stateId = isset($state) ? $state : '';
		$objPost->cityId = isset($city) ? $city : '';
		$objPost->zipCode = isset($zipCode) ? $zipCode : '';
		$objPost->code   = !empty($countryCode)?$countryCode:'';
		$objPost->mobileNumber = isset($mobile) ? $mobile : '';
		$objPost->paypalEmail = isset($paypalEmail) ? base64_encode($paypalEmail) : '';
		//$objPost->buyStatus = 'n';

		if(!empty($objPost->firstName)) {
			
				/* start - image upload section */
				if(isset($_FILES["profileImage"]["name"]) && $_FILES["profileImage"]["name"]!=""){
					$file_name = $_FILES["profileImage"]["name"];
					$make_directory = DIR_UPD . 'profile/' . $userId . '/';
					if (!is_dir($make_directory))
						mkdir($make_directory, 0755);
					$path = DIR_UPD . 'profile/' . $userId . '/' . $file_name;
					move_uploaded_file($_FILES['profileImage']['tmp_name'], $path);
					$objPost->profileImage = $file_name != "" ? $file_name : "";
				}
				/*Ends - image upload section*/

			$users = $db->select('tbl_users','*',array('id'=>$userId))->result();
			$db->update('tbl_users', (array)$objPost, array('id'=>$userId));
				
				//Send email notification to user and admin
				$email = getTableValue('tbl_users','email',array('id'=>$userId));
				$arrayCont = array(
					'USER_NM' => $objPost->firstName
				);
				sendMail(ADMIN_EMAIL, 'profile_admin', $arrayCont);
				sendMail(base64_decode($email), 'profile_user', $arrayCont);

				//$profileImage = getTableValue('tbl_users', 'profileImage', array('id'=>$userId));
				//$res_array['userImage'] = checkImage('profile/'.$userId.'/'.$profileImage);
				$query = "select * from tbl_users where (id=" . $userId. ")";
				$result = $db->pdoQuery($query);
				$fetchRes = $result->result();
						$uId=$fetchRes['id'];
						$res_array['userId'] = $fetchRes['id'];
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
						$res_array['birthDate']=$fetchRes['birthDate']=="0000-00-00 00:00:00" || $fetchRes['birthDate']=="0000-00-00" ?'':$fetchRes['birthDate'];
						$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
						$res_array['deviceToken']=$fetchRes['deviceToken'];	
					
				$status = true;
				$res_array['status'] = $status;
				$buyStatus = getTableValue('tbl_users','buyStatus',array('id'=>$userId));
				if($buyStatus=='n')
				{
					$res_array['message'] = 'Your profile updated successfully. Please wait for admin purchase approval.';	
				}else
				{
					$res_array['message'] = 'Your profile updated successfully.';	
				}
				
				echo json_encode($res_array);

		} else {
				$status = false;
				$res_array['status'] = $status;
				$res_array['message'] = 'Fill all values.';
				echo json_encode($res_array);
			
		}		
	}else if($action == 'accountSettings' && $userId>0){
				extract($request_data);
		
				$curruntpass = isset($curruntpass) ? $curruntpass :'';
				$newpass = isset($newpass) ? $newpass :'';
				
				if($curruntpass!='' && $newpass!=''){
					if(strlen($newpass)<6 || strlen($newpass)<6)
					{
						$status = false;
						$res_array['status'] = $status;
						$res_array['message'] = 'Password required minimum 6 characters.';
						echo json_encode($res_array);
						exit;
					}
					$qrySel=$db->pdoQuery("SELECT * FROM tbl_users WHERE id='".$userId."'");
					$resultData = $qrySel->result();

					if($resultData['password'] != md5($curruntpass))
					{
						$status = false;
						$res_array['status'] = $status;
						$res_array['message'] = 'Password don\'t match.';
						echo json_encode($res_array);
						exit;
					}
				}
				 	$insArr = array(
				 	'ipAddress'=>$_SERVER['REMOTE_ADDR'],
					'address'=>isset($address) ? $address : '',
					'zipCode'=>isset($zipcode) ? $zipcode : '',
					'countryId'=>isset($countryid) ? $countryid : 0,
					'stateId'=>isset($stateid) ? $stateid : 0,
					'cityId'=>isset($cityid) ? $cityid :0,
					'createdDate'=> date('Y-m-d H:i:s')
					);
					if($newpass!='')
					{
						$insArr['password']=md5($newpass);
					}
					$db->update('tbl_users',$insArr,array('id'=>$userId));
					$newProductPosted =isset($newProductPosted)?$newProductPosted:'n';
					$amountAddedInWallet =isset($amountAddedInWallet)?$amountAddedInWallet:'n';
					$receiveReplayFromAdmin =isset($receiveReplayFromAdmin)?$receiveReplayFromAdmin:'n';
					$newPrormoPosted =isset($newPrormoPosted)?$newPrormoPosted:'n';
					$orderStatusByAdmin =isset($orderStatusByAdmin)?$orderStatusByAdmin:'n';
					$reminder =isset($reminder)?$reminder:'n';

					$data = array();
					$data['newProductPosted'] = $newProductPosted;
					$data['amountAddedInWallet'] = $amountAddedInWallet;
					$data['receiveReplayFromAdmin'] = 'y';//$receiveReplayFromAdmin;
					$data['newPrormoPosted'] = $newPrormoPosted;
					$data['orderStatusByAdmin'] = $orderStatusByAdmin;
					$data['reminder'] = $reminder;
					
					$exist = getTableValue('tbl_notifications','id',array('userId'=>$userId));
					if($exist > 0){
						$db->update('tbl_notifications',$data,array("userId"=>$userId));
					}else{
						$data['userId'] = $userId;
						$db->insert('tbl_notifications',$data);
					}		
			
					$cNewsLetter= isset($cNewsLetter)?$cNewsLetter:'n';	
					
					$getEmail = $db->select('tbl_users',array('email'),array('id'=>$userId))->result();
					$getStatus = $db->select('tbl_newsletter_subscriber',array('id'),array('email'=>$getEmail['email']))->result();

					if($getStatus['id'] > 0 &&  $cNewsLetter=='n'){
						$db->delete('tbl_newsletter_subscriber',array('email'=>$getEmail['email']));
					}else if($getStatus['id'] <= 0 && $cNewsLetter=='y'){
						$db->insert('tbl_newsletter_subscriber',array('is_active'=>'y','email'=>$getEmail['email'],'created_date'=>date('Y-m-d H:i:s')));
					}
				$status = true;
				$res_array['status'] = $status;
				$res_array['message'] = 'Account settinge changed successfully.';
				echo json_encode($res_array);
	}else if($action == 'accountConstant'){
		
		$data = $db->pdoQuery("select * from tbl_myaccount_contant")->result();
		if($data ==true)
		{
			$res_array['constant']=$data;
			$res_array['status']=true;
			$res_array['message']='success';
		}else{
			$res_array['status']=false;
			$res_array['message']='Constant not available.';
		}
		echo json_encode($res_array);
	}else{
		$status = false;
		$res_array['status'] = $status;
		$res_array['message'] = 'something went wrong! Please try again.';
		echo json_encode($res_array);
	}
?>	