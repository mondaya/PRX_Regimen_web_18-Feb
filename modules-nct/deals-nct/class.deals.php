<?php
class deals {
	
	protected $db;
	public $module;
		
	function __construct($module,$searchText,$cateId,$subCateId,$page) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->searchText = $searchText;
		$this->cateId = $cateId;
		$this->subCateId = $subCateId;
		$this->currencyId = $currencyId;
		$this->currencyCode = $currencyCode;
		$this->currencySign = $currencySign;
		$this->fields = $fields;
		$this->table = 'tbl_product_deals';
		$this->module=$module;
		$this->page = $page;
		
	}

	public function deals_data(){

		//Paging code
		if(isset($this->page) && $this->page > 1)
		{
			$offset = ($this->page - 1) * LIMIT;
		}
		else
		{
			$offset = 0;
		}
			
			$whereCon = '';
			
			//For filters
			if($this->searchText != ''){
				$whereCon .= "d.productName LIKE '%".$this->searchText."%'";	
			}else{
				$whereCon .= "1 = 1";
			}

			//For category
			if($this->cateId != ''){
				$whereCon .=" AND d.categoryId = ".$this->cateId."";
			}

			//For sub category
			if($this->subCateId != ''){
				$whereCon .=" AND d.subcategoryId = ".$this->subCateId."";
			}

			
			//For total row count
			$queryCount = "SELECT d.id
					  FROM tbl_product_deals as d 
					  LEFT JOIN tbl_categories as c ON (c.id = d.categoryId)
					  LEFT JOIN tbl_subcategory as sc ON (sc.id = d.subcategoryId)
					  LEFT JOIN tbl_product_image as di ON (di.productId = d.id)
					  WHERE $whereCon AND d.isActive = 'y' AND productType = 'a' AND d.quantity > 0
					  group by d.id 
					  order by d.id desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT d.*,di.name
					  FROM tbl_product_deals as d 
					  LEFT JOIN tbl_categories as c ON (c.id = d.categoryId)
					  LEFT JOIN tbl_subcategory as sc ON (sc.id = d.subcategoryId)
					  LEFT JOIN tbl_product_image as di ON (di.productId = d.id)
					  WHERE $whereCon AND d.isActive = 'y' AND productType = 'a' AND d.quantity > 0
					  group by d.id 
					  order by d.id desc
					  LIMIT ".$offset." , ".LIMIT."";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		
		return $fetchRes;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'deals-nct',$this->totalRow);

		return $paginationData;
	}

	public function getCate(){
		$final_result = array();
		$qrySel=$this->db->pdoQuery("SELECT id,categoryName from tbl_categories where isActive = 'y' order by categoryName asc")->results();
		foreach($qrySel as $fetchRes){
			$id = $fetchRes['id'];
			$categoryName = $fetchRes['categoryName'];
			$final_result[]=array("id"=>$id,"categoryName"=>$categoryName);	
		}
		return $final_result;
	}

	public function getCateOption(){
		$content = NULL;
		$getState=$this->getCate();

		$main_content = new Templater(DIR_TMPL.$this->module."/option-nct.tpl.php");
		$main_content = $main_content->parse();	
		$fields = array("%VALUE%","%DISPLAY_VALUE%","%SELECTED%");	
		foreach($getState as $value){
			$selected = $this->cateId==$value['id']?'selected':'';

			$fields_replace = array($value['id'],$value['categoryName'],$selected);
			$content .= str_replace($fields, $fields_replace, $main_content);
		}	
		return sanitize_output($content);
	}

	public function getSubCate(){
		$final_result = array();
		$qrySel=$this->db->pdoQuery("SELECT id,subcategoryName from tbl_subcategory where categoryId = ".$this->cateId." AND isActive = 'y' order by subcategoryName asc")->results();
		foreach($qrySel as $fetchRes){
			$id = $fetchRes['id'];
			$subcategoryName = $fetchRes['subcategoryName'];
			$final_result[]=array("id"=>$id,"subcategoryName"=>$subcategoryName);	
		}
		return $final_result;
	}

	public function getSubCateOption(){
		$content = NULL;
		$getState=$this->getSubCate();

		$main_content = new Templater(DIR_TMPL.$this->module."/option-nct.tpl.php");
		$main_content = $main_content->parse();	
		$fields = array("%VALUE%","%DISPLAY_VALUE%","%SELECTED%");	
		foreach($getState as $value){
			$selected = $this->subCateId==$value['id']?'selected':'';

			$fields_replace = array($value['id'],$value['subcategoryName'],$selected);
			$content .= str_replace($fields, $fields_replace, $main_content);
		}	
		return sanitize_output($content);
	}

	

	public function getDealsList(){


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
				//$currencySign = $actualPrice>0?$this->currencySign:'';
				
				$fields_replace = array(
					substr($value['productName'],0,40),checkImage('product/'.$value['id'].'/'.$value['name']),$this->currencySign.number_format($dealPrice,2),$actualPrice,$discountPercentage,$this->currencySign,SITE_URL.'product/'.$value['id'],$value['id']
				);

				$dealList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $dealList;
	}
	
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		$cartProduct = getTotalRows('tbl_cart',array("userId"=>$this->sessUserId),'id');

		$fields = array(
			'%CART_COUNT%'
		);

		$fields_replace = array(
			$cartProduct
		);

		$final_result = str_replace($fields,$fields_replace,$final_result);

		return $final_result;
	}

	
}