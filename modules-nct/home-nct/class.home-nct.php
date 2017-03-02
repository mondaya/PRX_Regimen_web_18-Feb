<?php
class Home {
	function __construct() {
		global $db,$sessUserId,$currencyId,$currencyCode,$currencySign,$sessUserName;
		$this->db = $db;
		$this->table = '';
		$this->sessUserId = $sessUserId;
		$this->currencyId = $currencyId;
		$this->currencyCode = $currencyCode;
		$this->currencySign = $currencySign;
		$this->sessUserName = $sessUserName;
		foreach($GLOBALS as $key=>$values){
			$this->$key = $values;
		}
	}

	public function index() {
		$content = NULL;
		return $content;
	}

	public function getHeaderCurrency() {
		$currency_array = array();
		$currency = $currency_html = NULL;
		$currency = $this->db->pdoQuery("Select * from tbl_currency where isActive = 'y' order by id")->results();
		foreach ($currency as $key => $value) {
			if($this->currencyId==$value['id']) {
				$currency_array['curr_currency'] = $value['sign'].' '.$value['currency'];
			}
			$currency_html .= '<li><a href="'.SITE_URL.'currency/'.$value['id'].'?url='.selfURL().'"><b>'.$value['sign'].'</b> '.$value['currency'].'</a></li>';
		}
		$currency_array['currency_html'] = $currency_html;
		return $currency_array;
	}

	public function getHeaderContent(){
		$final_result = $login_content1 = NULL;
		if($this->sessUserId <= 0){
			$login_content1 = new Templater(DIR_TMPL."/login-nct.tpl.php");
			$login_content1 = $login_content1->parse();
			$search = array("%EMAIL%", "%PASSWORD%","%SECRET%", "%CHECKED%");
			$isRemember = $_COOKIE["isRemember"]=='y'?'checked':'';
			$replace = array($_COOKIE["email"], $_COOKIE["password"],$_COOKIE["secret"],$isRemember);
			$login_content1 = str_replace($search, $replace, $login_content1);
		} else{
			$login_content1 = new Templater(DIR_TMPL."loggedin-nct.tpl.php");
			$login_content1 = $login_content1->parse();
			$fields1 = array('%USER_NAME%', "%LOGOUT%", "%SETTINGS%", "%PROFILE%");
			$userName = getTableValue('tbl_users','firstName',array('id'=>$this->sessUserId));
			$fields_replace1 = array($userName, get_link('logout'), get_link('account_settings'), get_link('profile', $this->sessUserId));
			$login_content1 = str_replace($fields1, $fields_replace1, $login_content1);
		}
		return $login_content1;
	}

	public function get_slider() {
		$slider_content = $indicators = NULL;
		$sliders = $this->db->select('tbl_slider', array('*'), array('isActive'=>'y'))->results();
		$i = 1;
		foreach ($sliders as $key => $value) {
			$active = (($key==0)?'active':'');
// getImage($value['sliderImage'], 0, 'sliderImage', 1349, 347)
			$indicators .= '<li data-target="#bs-carousel" data-slide-to="'.$key.'" class="'.$active.'"></li>';
			$slider_content .= '
			<div class="item slides '.$active.'">
				<div class="slide-'.$i.'">
					<img src="'.SITE_SLIDER.$value['sliderImage'].'" alt="Slider Image" />
				</div>
				<div class="hero">
					<h5>'.html_entity_decode(htmlspecialchars($value['slider_description'])).'</h5>
				</div>
			</div>';
			$i ++;
		}
		return array('slider_html'=>$slider_content, 'indicators'=>$indicators);
	}

	public function get_banner() {
		$content = '';
		$slider_content = $indicators = NULL;
		$sliders = $this->db->select('tbl_banners', array('*'), array('is_active'=>'y'))->results();
		
		$i = 0;
		foreach ($sliders as $key => $value) {
			
			if ($i%4 == 0)
            {
                if ($i != 0)
                {
                    $content .= '</div></div>';
                }
             $content .= '<div class="item '.($i==0 ? "active" : "").'"><div class="row">';
            }

			$content.= '<div class="col-md-3 img-url">
		                    <a href="'.$value['banner_link'].'" title="'.$value['banner_name'].'" class="slider-img" target="_blank">
		                       <img src="'.SITE_BANNER.'/'.$value['id'].'/'.$value['banner_image'].'" alt="'.$value['banner_image'].'">
		                       <div class="carousel-caption">
		                          <h3>'.$value['banner_name'].'</h3>
		                       </div>
		                    </a>
		                </div>';

			$i++;
		}

		$content .= '</div></div>';


		return $content;
	}

	public function stores(){
		$query = "SELECT * from tbl_stores WHERE isActive = 'y' order by id desc";
		$result = $this->db->pdoQuery($query);
		$fetchRes = $result->results();
		
		$content = '';
		
		$i = 0;
		foreach ($fetchRes as $key => $value) {

			if ($i%6 == 0)
            {
                if ($i != 0)
                {
                    $content .= '</div></div>';
                }
             $content .= '<div class="item '.($i==0 ? "active" : "").'"><div class="row">';
            }
			
			$content.='<div class="brand-logo">
                         <a href="'.$value['storeLink'].'" class="thumbnail"><img title="'.$value['storeName'].'" src="'.checkImage('store-nct/'.$value['id'].'/',$value['storeImage']).'" alt="Image"></a>
                       </div>';
			$i++;
		}

		$content .= '</div></div>';

		

		return $content;
		
	}

	public function getCountry($countryId = 0){
		$selCountry = $this->db->select('tbl_country',array('id','countryName'),array('isActive'=>'y'),' ORDER BY countryName')->results();
		//$opt_content = (new Templater(DIR_TMPL.'option-nct.tpl.php'))->parse();
		$opt_content = new Templater(DIR_TMPL.'option-nct.tpl.php');
		$opt_content = $opt_content->parse();
		ob_start();
		echo '<option value="">--Please Select*--</option>';
		foreach($selCountry as $country){
			extract($country);
			$selected = $countryId == $id ? 'selected="selected"' : '';
			echo str_replace(array('%VALUE%','%SELECTED%','%NAME%'),array($id,$selected,$countryName),$opt_content);
		}
		return ob_get_clean();
	}

	public function getStates($countryId,$stateId = 0){

		if($countryId == ''){
			$countryId = 0;
		}

		$content = array('states'=>'<option value="" selected="selected">--Please Select State*--</option>');
		$opt_content = new Templater(DIR_TMPL.'option-nct.tpl.php');
		$opt_content = $opt_content->parse();
		$opt_field = array('%VALUE%','%NAME%','%SELECTED%');
		$getState = $this->db->select('tbl_state',array('id','stateName'),array('countryId'=>$countryId,'isActive'=>'y'),' ORDER BY stateName ASC');
		if($getState->affectedRows()>0){
			$states = $getState->results();
			foreach($states as $state){
				$selected = ($state['id'] == $stateId ? 'selected="selected"' : '');
				$opt_field_replace = array($state['id'],$state['stateName'],$selected);
				$content['states'] .=str_replace($opt_field,$opt_field_replace,$opt_content);
			}
		}
		return $content;
	}

	public function getCities($stateId,$cityId = 0){

		if($stateId == ''){
			$stateId = 0;
		}
		
		$content = array('cities'=>'<option value="" selected="selected">--Please Select City*--</option>');
		$opt_content = new Templater(DIR_TMPL.'option-nct.tpl.php');
		$opt_content = $opt_content->parse();
		$opt_field = array('%VALUE%','%NAME%','%SELECTED%');
		$getCities = $this->db->select('tbl_city',array('id','cityName'),array('stateId'=>$stateId,'isActive'=>'y'),' ORDER BY cityName ASC');
		if($getCities->affectedRows()>0){
			$cities = $getCities->results();
			foreach($cities as $city){
				$selected = ($city['id'] == $cityId ? 'selected="selected"' : '');
				$opt_field_replace = array($city['id'],$city['cityName'],$selected);
				$content['cities'] .=str_replace($opt_field,$opt_field_replace,$opt_content);
			}
		}
		return $content;
	}


	public function getCategory() {
	$category_main = $category = $category_content = $category_html = $sub_categories_content = $category_tab_content = $all_categories = $allcategory = $allproducts = $all_products= NULL;
	$category_main = new Templater(DIR_TMPL.$this->module.'/categories.tpl.php');
	$category_main = $category_main->parse();
	$category_main_find = array("%TOP_CATEGORY%", "%SUB_CATEGORIES%", "%ALL_CATEGORIES%", "%ALL_DEALS%");

	$category_content = new Templater(DIR_TMPL.$this->module.'/category_tab.tpl.php');
	$category_content = $category_content->parse();
	$find = array("%ACTIVE_CLASS%", "%CAT_ID%", "%CAT_NAME%");

	$category_tab = new Templater(DIR_TMPL.$this->module.'/category_tab_content.tpl.php');
	$category_tab = $category_tab->parse();
	$category_tab_find = array("%ACTIVE_CLASS%", "%CAT_ID%", "%CAT_DESC%", "%SUB_CATEGORIES%",'%CATE_ID%');

	$sub_categories = new Templater(DIR_TMPL.$this->module.'/sub_categories.tpl.php');
	$sub_categories = $sub_categories->parse();

	$deals = new Templater(DIR_TMPL.$this->module.'/deals.tpl.php');
	$deals = $deals->parse();
	$categories_find = array("%SUB_CAT_IMG%", "%SUB_CAT_NM%","%HREF%");
	$sub_categories_find = array("%SUB_CAT_IMG%", "%SUB_CAT_NM%","%HREF%");

	$category = $this->db->select('tbl_categories', array('*'), array('isActive'=>'y', 'is_display'=>'y'), 'ORDER BY id desc')->results();
	if(!empty($category)) {
		foreach ($category as $key => $value) {
			$active_class = (($key==0)?'active':'');
			$replace = array($active_class, $value['id'], $value['categoryName']);
			$category_html .= str_replace($find, $replace, $category_content);

			$sub_categories_content='';
			$subcategory = $this->db->select('tbl_subcategory', array('*'), array('isActive'=>'y', 'categoryId'=>$value['id']), 'ORDER BY id desc LIMIT 0, 8')->results();
			foreach ($subcategory as $key => $svalue) {
				// getImage($svalue['subcategoryImage'], 'subcategory/'.$svalue['id'], 200, 200)
				// SITE_UPD_SUBCATEGORY.$svalue['id'].'/'.$svalue['subcategoryImage']
				$subCateImg = checkImage('subcategory/'.$svalue['id'].'/',$svalue['subcategoryImage']);
				$sub_categories_replace = array($subCateImg, substr($svalue['subcategoryName'],0,30),SITE_URL.'stores/'.$value['id'].'/'.$svalue['id']);
				$sub_categories_content .= str_replace($sub_categories_find, $sub_categories_replace, $sub_categories);
			}
			$category_tab_replce = array($active_class, $value['id'], substr($value['description'],0,150), $sub_categories_content,$value['id']);
			$category_tab_content .= str_replace($category_tab_find, $category_tab_replce, $category_tab);
		}
	}

	$allcategory = $this->db->select('tbl_categories', array('*'), array('isActive'=>'y'),'ORDER BY id desc LIMIT 0, 8')->results();
	foreach ($allcategory as $key => $value) {
		// SITE_UPD_CATEGORY.$value['id'].'/'.$value['categoryPhoto']
		$cateImg = checkImage('category/'.$value['id'].'/',$value['categoryPhoto']);
		$sub_categories_replace = array($cateImg, substr($value['categoryName'],0,30),SITE_URL.'subcategories/'.$value['id']);
		$all_categories .= str_replace($categories_find, $sub_categories_replace, $sub_categories);
	}

	$allproducts = $this->db->pdoQuery('SELECT p.*, i.name FROM tbl_product_deals AS p LEFT JOIN tbl_product_image AS i ON i.productId=p.id WHERE p.productType="a" and p.isActive="y" AND p.quantity > 0 group by p.id LIMIT 0,8', array('y'))->results();
	foreach ($allproducts as $key => $value) {
		// SITE_UPD_PRODUCT.$value['id'].'/'.$value['name']
		$dealImg = checkImage('product/'.$value['id'].'/',$value['name']);
		$deals_find = array("%SUB_CAT_IMG%", "%SUB_CAT_NM%","%PRODUCT_ID%");
		$deals_replace = array($dealImg, substr($value['productName'],0,40),$value['id']);
		$all_products .= str_replace($deals_find, $deals_replace, $deals);
	}

	$category_main_replace = array($category_html, $category_tab_content, $all_categories, $all_products);
	$category_main = str_replace($category_main_find, $category_main_replace, $category_main);
	return $category_main;
	}

	public function getSupportData(){

		$query = "SELECT * from tbl_content WHERE isActive = 'y' and section='s' order by pId desc";
		$result = $this->db->pdoQuery($query);
		$fetchRes = $result->results();
		
		if(!empty($fetchRes)){

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL."/more_info-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields1 = array(
				'%PAGE_NAME%','%CONTANT_LINK%'
			);

			$supportInfoList = '';
			foreach ($fetchRes as $value) {
				
				$fields_replace1 = array(
					$value['pageTitle'],SITE_URL.'content/'.$value['pId'].'/'
				);

				$supportInfoList .=str_replace($fields1,$fields_replace1,$final_result);
			}
		}

		return $supportInfoList;

	}

	public function getHelpData(){

		$query = "SELECT * from tbl_content WHERE isActive = 'y' and section='h' order by pId desc";
		$result = $this->db->pdoQuery($query);
		$fetchRes = $result->results();
		
		if(!empty($fetchRes)){

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL."/more_info-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields1 = array(
				'%PAGE_NAME%','%CONTANT_LINK%'
			);

			$helpInfoList = '';
			foreach ($fetchRes as $value) {
				
				$fields_replace1 = array(
					$value['pageTitle'],SITE_URL.'content/'.$value['pId'].'/'
				);

				$helpInfoList .=str_replace($fields1,$fields_replace1,$final_result);
			}
		}

		return $helpInfoList;

	}

	public function getWalletData(){

		$query = "SELECT * from tbl_content WHERE isActive = 'y' and section='w' order by pId desc";
		$result = $this->db->pdoQuery($query);
		$fetchRes = $result->results();
		
		if(!empty($fetchRes)){

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL."/more_info-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields1 = array(
				'%PAGE_NAME%','%CONTANT_LINK%'
			);

			$helpInfoList = '';
			foreach ($fetchRes as $value) {
				
				$fields_replace1 = array(
					$value['pageTitle'],SITE_URL.'content/'.$value['pId'].'/'
				);

				$helpInfoList .=str_replace($fields1,$fields_replace1,$final_result);
			}
		}

		return $helpInfoList;

	}

	public function getUserData($userId) { 
        $final_result = NULL;
        $this->sessUserId = $this->sessUserId;
        $main_content = new Templater(DIR_TMPL."home-nct/address-nct.tpl.php");
        $final_result = $main_content->parse();

        $qrySel = $this->db->select('tbl_users', array('*'), array('id' => $this->sessUserId, 'isActive' => 'y'));

        $userData = $qrySel->result();

        $id = $userData['id'];
        $salute = $userData['salute'];
        $firstName = $userData['firstName'];
        $lastName = $userData['lastName'];
        $email = base64_decode($userData['email']);
        $member = $userData['member'];
        $address = $userData['address'];
        $zipCode = $userData['zipCode']>0?$userData['zipCode']:'';
        $profileImage = $userData['profileImage'];
        $mobileNumber = $userData['mobileNumber'];
        $birthDate = $userData['birthDate'] != '0000-00-00' ? date('m-d-Y', strtotime($userData['birthDate'])) : '';
        $gender = $userData['gender'];
        $paypalEmail = base64_decode($userData['paypalEmail']);

        $countryid = $userData['countryId'];
        $stateid = $userData['stateId'];
        $cityid = $userData['cityId'];

        $country = $this->getCountry($countryid);
        $states = $this->getStates($countryid, $stateid);
        $city = $this->getCities($stateid, $cityid);

        /* $follower_msg = SITE_URL."compose/".$userId.'/'; */
        $profile = checkImage($userData['profileImage'], 25);

        $salute_mr = (($userData['salute']=='mr')?'selected="selected"':'');
        $salute_mrs = (($userData['salute']=='mrs')?'selected="selected"':'');
        $salute_ms = (($userData['salute']=='ms')?'selected="selected"':'');
        $salute_dr = (($userData['salute']=='dr')?'selected="selected"':'');
        $selected_m = (($userData['gender']=='m')?'selected="selected"':'');
		$selected_f = (($userData['gender']=='f')?'selected="selected"':'');

        $fields = array('%PROFILEIMG%', '%SAL%', '%FNAME%', '%LNAME%', '%EMAIL%', '%MEMBER%', '%GENDER%', '%BIRTH%', '%ADDRESS%', '%COUNTRY%', '%STATE%', '%CITY%', '%ZIP%', '%MOBILE%', '%PMAIL%', '%USER_ID%', "%USER_AVATAR%", "%USER_NAME%", '%CROP_PATH%', '%USER_ID%', "%SELECTED_MR%", "%SELECTED_MRS%", "%SELECTED_MS%", "%SELECTED_DR%", "%SELECTED_M%", "%SELECTED_F%", "%SECRET_WORD%", '%BACK_BTN_URL%');
        $fields_replace = array($profile, $salute, $firstName, $lastName, $email, $member, $gender, $birthDate, $address, $country, $states['states'], $city['cities'], $zipCode, $mobileNumber, $paypalEmail, $id, checkImage('profile/'.$userId.'/'.$userData['profileImage']), $firstName . ' ' . $lastName, CROP_PATH, $this->sessUserId, $salute_mr, $salute_mrs, $salute_ms, $salute_dr, $selected_m, $selected_f, $userData['secret'], get_link('profile', $this->sessUserId));
        $final_result = str_replace($fields, $fields_replace, $final_result);
        return $final_result;
    }


}
?>