<?php
class Login extends Home  {
	function __construct() {
		parent::__construct();		
	}
	public function loginSubmit() {
		$uName = $this->objPost->uName;
		$uPass = $this->objPost->uPass;
		 
		$qrysel = $this->db->select("tbl_admin",array("id","uPass","status","updated_date"),array("uName" => $uName))->result();

		if(!empty($qrysel) > 0 && $qrysel['status']!='n' && $qrysel['status']!='t') {
				$fetchUser = $qrysel;
				$adm_id = $fetchUser['id'];
				if($fetchUser["uPass"]==md5($uPass)) {
					$_SESSION["adminUserId"] = (int)$fetchUser["id"];
					$_SESSION["uName"] = $uName;
					$_SESSION["last_login"] = $fetchUser["updated_date"];
					$this->db->update("tbl_admin", array("updated_date"=>date('Y-m-d H:i:s')), array("id"=>$adm_id));
					
					if(isset($_SESSION['req_uri_adm']) && $_SESSION['req_uri_adm']!=''){						
						$url = $_SESSION['req_uri_adm'];
						unset($_SESSION['req_uri_adm']);
						unset($_SESSION['loginDisplayed_adm']);
						redirectPage($url);
					}else{
						redirectPage(SITE_ADM_MOD.'home-nct/');
					}
				}
				else {
					return 'invaildUsers';
				}
		}else if($qrysel['status']=='n'){
			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'unapprovedUser'));
			redirectPage(SITE_ADM_MOD.$this->module.'/');
		}
		else {
			$_SESSION["msgType"] = disMessage(array('type'=>'err','var'=>'invaildUsers'));
			redirectPage(SITE_ADM_MOD.$this->module.'/');
		}
	}
	public function forgotProdedure() {
			
			$uEmail = isset($this->objPost->uEmail) ? $this->objPost->uEmail : '';
			$uName = isset($this->objPost->uName) ? $this->objPost->uName : '';
			$value = new stdClass();
			$qrysel = $this->db->select("tbl_admin",array("id,uEmail,uName,uPass"),array("uEmail"=>$uEmail))->result();
			if(!empty($qrysel) > 0) {
			$fetchUser = $qrysel;	
					$to = $fetchUser["uEmail"];
					$uName = $fetchUser["uName"];
					$id = (int)$fetchUser["id"];
					$subject = "Forgot Password";
					$value->uPass = genrateRandom();
					
					$this->db->update("tbl_admin",array("uPass"=>md5($value->uPass)),array("id"=>$id));

					$contArray = array(				
						"USER_NAME"=>$uName,
						"PASSWORD"=>$value->uPass,
						"LINK"=>SITE_ADM_MOD.'login-nct/'
					);
					sendMail($to,"forgot_password_admin",$contArray);
					return 'succForgotPass';
			}
			else {
				return 'wrongUsername';				
			}
	}
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$final_result = $main_content->parse();

		$search = array("%EMAIL%", "%PASSWORD%", "%CHECKED%");
		$isRemember = $_COOKIE["remember"]=='y'?'checked':'';
		$replace = array($_COOKIE["uName"], $_COOKIE["uPass"],$isRemember);

		$final_result = str_replace($search, $replace, $final_result);

		return $final_result;
	}
}
?>