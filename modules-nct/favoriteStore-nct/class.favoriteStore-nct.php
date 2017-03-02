<?php
class favoriteStore {
	
	protected $db;
	public $module;
		
	function __construct($module,$page) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->currencyId = $currencyId;
		$this->currencyCode = $currencyCode;
		$this->currencySign = $currencySign;
		$this->fields = $fields;
		$this->table = 'tbl_product_deals';
		$this->module=$module;
		$this->page = $page;
		
	}

	public function store_data(){

			//Paging code
			if(isset($this->page) && $this->page > 1)
			{
				$offset = ($this->page - 1) * LIMIT;
			}
			else
			{
				$offset = 0;
			}

			//For total row count
			$queryCount = "SELECT fs.id from tbl_favourite_store as fs
				      LEFT JOIN tbl_stores AS s ON(fs.storeId = s.id)
				      WHERE fs.userId = ".$this->sessUserId."
					  order by fs.id desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT fs.id,fs.storeId,s.storeName,s.storeImage,s.storeLink from tbl_favourite_store as fs
				      LEFT JOIN tbl_stores AS s ON(fs.storeId = s.id)
				      WHERE fs.userId = ".$this->sessUserId."
					  order by fs.id desc
					  LIMIT ".$offset." , ".LIMIT."";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		
		return $fetchRes;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'favoriteStore-nct',$this->totalRow);

		return $paginationData;
	}

	
	public function getstoreList(){


		$storeData = $this->store_data();
		//echo '<pre>';
		//print_r($storeData);exit;

		if(empty($storeData)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$cateList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/store_list-nct.tpl.php");
			$final_result = $main_content->parse();



			$fields = array(
				"%STORE_NM%","%IMG_SRC%","%SITE_URL%","%ID%","%STORE_LINK%"
			);

			$cateList = '';
			foreach ($storeData as $value) {
				
				$imagesrc = checkImage('store-nct/'.$value['storeId'].'/'.$value['storeImage']);

				$fields_replace = array(
					$value['storeName'],$imagesrc,SITE_URL,$value['id'],$value['storeLink']
				);

				$cateList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $cateList;
	}

	public function getStore(){
		$final_result = array();
		$qrySel=$this->db->pdoQuery("SELECT id,storeName from tbl_stores where isActive = 'y' order by storeName asc")->results();
		foreach($qrySel as $fetchRes){
			$id = $fetchRes['id'];
			$storeName = $fetchRes['storeName'];
			$final_result[]=array("id"=>$id,"storeName"=>$storeName);	
		}
		return $final_result;
	}

	public function getCateOption(){
		$content = NULL;
		$getState=$this->getStore();

		$main_content = new Templater(DIR_TMPL.$this->module."/option-nct.tpl.php");
		$main_content = $main_content->parse();	
		$fields = array("%VALUE%","%DISPLAY_VALUE%");	
		foreach($getState as $value){

			$alreadyFavorite = getTotalRows("tbl_favourite_store",array("storeId"=>$value['id'],"userId"=>$this->sessUserId),"id");

			if($alreadyFavorite == 0){
				$fields_replace = array($value['id'],$value['storeName']);
				$content .= str_replace($fields, $fields_replace, $main_content);
			}
		}	
		return sanitize_output($content);
	}
	
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		/*$selected_d = $selected_r = $selected_p = $selected_s = '';
		if($this->status == 'd'){
			$selected_d = "selected";
		}else if($this->status == 'r'){
			$selected_r = "selected";
		}else if($this->status == 'p'){
			$selected_p = "selected";
		}else if($this->status == 's'){
			$selected_s = "selected";
		}

		$fields = array(
			"%SELECTED_D%","%SELECTED_R%","%SELECTED_P%","%SELECTED_S%"
		);

		$fields_replace = array(
			$selected_d,$selected_r,$selected_p,$selected_s
		);

		$final_result =str_replace($fields,$fields_replace,$final_result);*/

		return $final_result;
	}

	
}