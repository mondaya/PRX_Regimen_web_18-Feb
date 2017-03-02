<?php
class dealDetail {
	
	protected $db;
	public $module;
		
	function __construct($module,$dealId) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->dealId = $dealId;
		$this->fields = $fields;
		$this->table = 'tbl_product_deal';
		$this->module=$module;
		$this->currencyId=$currencyId;
		$this->currencyCode=$currencyCode;
		$this->currencySign=$currencySign;
		
	}

	public function getImageSlider(){

		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/dealImage-nct.tpl.php");
		$final_result = $main_content->parse();
		
		$image = $this->db->select('tbl_product_image','name',array("productId"=>$this->dealId))->results();

		$fields = array("%IMAGE_PATH%","%I%",'%ACTIVE%');

		$images = $active = '';
		$i = 1;
		
		foreach ($image as $key => $value) {
		$active = $i==1?'active':'';
		$fields_replace = array(checkImage('product/'.$value['productId'].'/'.$value['name']),$i,$active);

		$images .= str_replace($fields,$fields_replace,$final_result);

		
			$i ++;
		}

		return $images;
	}
	
	
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();
		
		$query = "select p.*,c.categoryName,s.subcategoryName FROM tbl_product_deals as p 
				  LEFT JOIN tbl_categories as c ON (p.categoryId = c.id)
				  LEFT JOIN tbl_subcategory as s ON (p.subcategoryId = s.id)
				  WHERE p.id = ".$this->dealId." and p.isActive = 'y' and productType = 'a'";

		$dealData = $this->db->pdoQuery($query)->result();

		if($dealData['isDiscount'] == 'y'){
			$dealPrice = convertCurrency($this->currencyId,$dealData['discountPrice']);
			$actualPrice = convertCurrency($this->currencyId,$dealData['actualPrice']);
			$actualPrice = $this->currencySign.number_format($actualPrice,2);
			$discountPercentage = ( $dealData['discountPercentage']. '%');
		}else{
			$dealPrice = convertCurrency($this->currencyId,$dealData['actualPrice']);
			$actualPrice = '';
			$discountPercentage = '-';
		}

		$imageSlider = $this->getImageSlider();
		$singalImage = getTableValue('tbl_product_image','name',array("productId"=>$this->dealId));
		$imageSrc = checkImage('product/'.$this->dealId.'/'.$singalImage);

		$quantityOption = $this->getQuiOption();

		$fields = array("%PRODUCT_NM%","%CATE_NM%","%SUB_CATE%","%POSTED_DATE%","%WEIGHT%","%ACTUAL_PRICE%","%DISCOUNT_PRICE%","%CURR_SIGN%","%DIS_PER%","%QUANTITY%","%DESC%",'%IMAGE_SLIDER%','%DEAL_ID%',"%PRODUCT_IMG%","%QUI_OPTION%");

		$currencySign = $actualPrice>0?$this->currencySign:'';
		$fields_replace = array($dealData['productName'],$dealData['categoryName'],$dealData['subcategoryName'],date('Y-m-d',strtotime($dealData['createdDate'])),$dealData['weight'],$actualPrice,$this->currencySign.number_format($dealPrice,2),$this->currencySign,$discountPercentage,$dealData['quantity'],addslashes($dealData['productDescription']),$imageSlider,$this->dealId,$imageSrc,$quantityOption);

		$dealDetail = str_replace($fields,$fields_replace,$final_result);

		return $dealDetail;
	}

	public function deals_data(){

			$cateId = getTableValue('tbl_product_deals','categoryId',array("id"=>$this->dealId));

			$query = "SELECT d.*,di.name
					  FROM tbl_product_deals as d 
					  LEFT JOIN tbl_categories as c ON (c.id = d.categoryId)
					  LEFT JOIN tbl_subcategory as sc ON (sc.id = d.subcategoryId)
					  LEFT JOIN tbl_product_image as di ON (di.productId = d.id)
					  WHERE d.categoryId = ".$cateId." AND d.isActive = 'y' AND productType = 'a' AND d.id NOT IN(".$this->dealId.") AND d.quantity > 0
					  group by di.productId 
					  order by d.id desc
					  LIMIT 8";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		
		return $fetchRes;
	}

	public function getSimilarDealsList(){


		$dealData = $this->deals_data();
		//echo '<pre>';
		//print_r($driverData);exit;

		if(empty($dealData)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$dealList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/deal_list-nct.tpl.php");
			$final_result = $main_content->parse();



			$fields = array(
				'%DEAL_NM%','%DEAL_IMG%','%DISCOUNT_PRICE%','%ACTUAL_PRICE%','%DIS_PER%','%CURR_SIGN%',"%LINK%","%DEAL_ID%"
			);

			$dealList = '';
			foreach ($dealData as $value) {

				if($value['isDiscount'] == 'y'){
					$dealPrice = convertCurrency($this->currencyId,$value['discountPrice']);
					$actualPrice = convertCurrency($this->currencyId,$value['actualPrice']);
					$actualPrice = $this->currencySign.number_format($actualPrice,2);
					$discountPercentage = ( $value['discountPercentage']. '% off');
					
				}else{
					$dealPrice = convertCurrency($this->currencyId,$value['actualPrice']);
					$actualPrice = $discountPercentage = '';
					
				}
				$currencySign = $actualPrice>0?$this->currencySign:'';
				$fields_replace = array(
					substr($value['productName'],0,40),checkImage('product/'.$value['id'].'/'.$value['name']),$this->currencySign.number_format($dealPrice,2),$actualPrice,$discountPercentage,$this->currencySign,SITE_URL.'product/'.$value['id'],$value['id']
				);

				$dealList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $dealList;
	}

	public function getQuiOption(){
		$content = NULL;
		$quantity = getTableValue('tbl_product_deals','quantity',array('id'=>$this->dealId));
		$content .= '<select name="dealquantity" class="dealquantity">';
		for($i=1;$i<=$quantity;$i++){
		
			$selected = $i==1?'selected':'';

			
			$content .= '<option value='.$i.'>'.$i.'</option>';
			
		}	
		$content .= '</select>';
		return sanitize_output($content);
	}

}