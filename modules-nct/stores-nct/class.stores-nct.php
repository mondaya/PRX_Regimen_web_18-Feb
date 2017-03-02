<?php
class Stores {
	
	protected $db;
	public $module;
		
	function __construct($module,$page,$cateId,$subCateId) {
		global $db,$fields,$sessUserId;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->cateId = $cateId;
		$this->subCateId = $subCateId;
		$this->fields = $fields;
		$this->table = 'tbl_categories';
		$this->module=$module;
		$this->page = $page;
		
	}
	
	public function store_data() 
	{
		if(isset($this->page) && $this->page > 1)
		{
			$offset = ($this->page - 1) * LIMIT;
		}
		else
		{
			$offset = 0;
		}

		$whereCon = '';

		//For category
		if($this->cateId != ''){
			$whereCon .=" FIND_IN_SET(".$this->cateId.",categoryId)";
		}

		//For sub category
		if($this->subCateId != ''){
			$whereCon .=" AND FIND_IN_SET(".$this->subCateId.",subcategoryId)";
		}

		//For is active
		if($this->subCateId > 0 && $this->cateId > 0 || $this->cateId > 0){
			$isActive = 'AND isActive="y"';
		}else{
			$isActive = 'isActive="y"';
		}


		
		$queryCount = "SELECT id from tbl_stores
		WHERE $whereCon $isActive ORDER BY id desc";

		$resultCount = $this->db->pdoQuery($queryCount);
 		$this->totalRowCount = $resultCount->affectedRows();

		$query = "SELECT id,storeName,storeLink,storeImage from tbl_stores
		WHERE $whereCon $isActive ORDER BY id desc LIMIT ".$offset." , ".LIMIT."";

		$result = $this->db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$this->totalRow = $result->affectedRows();
		
		return $fetchRes;
		
	}

	public function getStoreList(){


		$storeData = $this->store_data();
		//echo '<pre>';
		//print_r($storeData);exit;

		if(empty($storeData)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$storeList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/store_list-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields = array(
				'%STORE_NM%','%IMG_SRC%','%STORE_LINK%','%FAVORITE_ICON%'
			);

			$storeList = '';
			foreach ($storeData as $value) {
				$storeImg = checkImage('store-nct/'.$value['id'].'/',$value['storeImage']);

				//Get brand favorite or not
				$exist = getTableValue("tbl_favourite_store", "id",array("storeId"=>$value['id'],"userId"=>$this->sessUserId));
				$favoriteIcon='<a href="javascript:void(0)" class="active-link favourite" data-id="'.$value['id'].'" data-value="'.($exist == 0 ? 'on' : 'off').'" ><img src="'.SITE_IMG.'fav-'.($exist>0 ? 'on' : 'off').'.png" alt="favourite" /></a>';
				
				$fields_replace = array(
					substr($value['storeName'],0,20),$storeImg,$value['storeLink'],$favoriteIcon
				);

				$storeList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $storeList;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'stores-nct',$this->totalRow);

		return $paginationData;
	}

	public function getPageContent(){
		$final_result = NULL;
		
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();
		
		$fields = array("%CATE_ID%",'%SUBCATE_ID%');
		$fields_replace = array($this->cateId,$this->subCateId);

		$final_result = str_replace($fields, $fields_replace, $final_result);
		
		return $final_result;
	}
}