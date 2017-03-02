<?php
class Settings extends home{	
	function __construct() {
		parent::__construct();
		global $db,$fields,$sessUserId,$userId,$sessUsername, $type;
		$this->type = $type;
	}

	public function getPageContent($userId){ 
		$final_result = NULL;
		if($userId == $this->sessUserId)
		{
			$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");				
			$final_result = $main_content->parse();	
		}

		$userData = $this->db->pdoQuery("SELECT u.address,u.zipCode,u.countryId,u.stateId,u.cityId,ct.cityName,s.stateName,c.countryName,n.is_active,no.* FROM tbl_users AS u 
			LEFT JOIN tbl_city AS ct ON u.cityId = ct.id  
			LEFT JOIN tbl_state AS s ON u.stateId = s.id 
			LEFT JOIN tbl_country AS c ON u.countryId = c.id 
			LEFT JOIN tbl_newsletter_subscriber AS n ON n.email=u.email
			LEFT JOIN tbl_notifications AS no ON no.userId=u.id 
			WHERE u.id = ?",array($this->sessUserId))->result();
		$id = isset($userData['id'])? $userData['id'] : '';	
		$address = isset($userData['address'])? $userData['address'] : '' ;	

		$zipCode = isset($userData['zipCode'])? $userData['zipCode'] : '';
		$countryid =$userData['countryId'];
		$stateid = $userData['stateId'];
		$cityid = $userData['cityId'];
		$firstName =  isset($userData['firstName'])? $userData['firstName'] : '';
		$country = $this->getCountry($countryid);
		$states = $this->getStates($countryid,$stateid);
		$city = $this->getCities($stateid,$cityid);
		
		if($userData['is_active'] == 'y') 
			$chk = 'checked';
		else
			$chk = '';

		$newProductPosted = $userData['newProductPosted']=='y'?'checked':'';
		$amountAddedInWallet = $userData['amountAddedInWallet']=='y'?'checked':'';
		//$receiveReplayFromAdmin = $userData['receiveReplayFromAdmin']=='y'?'checked':'';
		$newPrormoPosted = $userData['newPrormoPosted']=='y'?'checked':'';
		$orderStatusByAdmin = $userData['orderStatusByAdmin']=='y'?'checked':'';
		$reminder = $userData['reminder']=='y'?'checked':'';
		
		$fields = array('%ADDRESS%','%COUNTRY%','%STATE%','%CITY%','%ZIP%','%CHKED%','%NEW_PRODUCT%','%AMOUNT_WALLET%','%PROMO_CODE%','%ORDER_STATUS%','%REMINDER%');

		$fields_replace = array($address,$country,$states['states'],$city['cities'],$zipCode,$chk,$newProductPosted,$amountAddedInWallet,$newPrormoPosted,$orderStatusByAdmin,$reminder);
		$final_result=str_replace($fields,$fields_replace,$final_result);
		return $final_result;

	}


	public function changepass(){
		global $sessUserId;
		$final_result = NULL;		
		$currpass = '';
		$newpass = '';
		$confpass = '';
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();
		$fields = array('%CURRPASS%','%NEWPASS%','%CONFPASS%');
		$fields_replace = array($currpass,$newpass,$confpass);
		$final_result = str_replace($fields,$fields_replace,$final_result);
		return $final_result;
	}


	public function submitProcedure($passArr)
	{
		global $sessUserId;
		$curruntpass = isset($passArr['curruntpass']) ? $passArr['curruntpass'] :'';
		$newpass = isset($passArr['newpass']) ? $passArr['newpass'] :'';
		$confirmpass = isset($passArr['confirmpass']) ? $passArr['confirmpass'] :'';

		$qrySel=$this->db->pdoQuery("SELECT * FROM tbl_users WHERE id='".$sessUserId."'");
		$resultData = $qrySel->result();
		if($resultData['password'] == md5($curruntpass))
		{
			if($newpass == $confirmpass)
			{
				$insArr = new stdClass();
				$insArr = array(
					'password'=>md5($confirmpass),
					'ipAddress'=>$_SERVER['REMOTE_ADDR']
					);
				$qryUpd = $this->db->update('tbl_users',$insArr,array('id'=>$sessUserId));
				$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'succChangePass'));
				redirectPage(SITE_URL.'settings/');
				break;
			}
			else
			{
				$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'passNotmatch'));
				redirectPage(SITE_URL.'settings/');	
			}

		}

		else
		{
			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'wrongPass'));
			redirectPage(SITE_URL.'settings/');	
		}
	}



}

?>
