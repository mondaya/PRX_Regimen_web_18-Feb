<?php
class favoriteCate {
	
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

	public function cate_data(){

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
			$queryCount = "SELECT fc.id from tbl_favorite_categories as fc
				      LEFT JOIN tbl_categories AS c ON(fc.categoryId = c.id)
				      WHERE fc.userId = ".$this->sessUserId."
					  order by fc.id desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT fc.id,fc.categoryId,c.categoryName,c.categoryPhoto from tbl_favorite_categories as fc
				      LEFT JOIN tbl_categories AS c ON(fc.categoryId = c.id)
				      WHERE fc.userId = ".$this->sessUserId."
					  order by fc.id desc
					  LIMIT ".$offset." , ".LIMIT."";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		
		return $fetchRes;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'favoriteCate-nct',$this->totalRow);

		return $paginationData;
	}

	
	public function getcateList(){


		$cateData = $this->cate_data();
		//echo '<pre>';
		//print_r($cateData);exit;

		if(empty($cateData)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$cateList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/cate_list-nct.tpl.php");
			$final_result = $main_content->parse();



			$fields = array(
				"%CATE_NM%","%IMG_SRC%","%SITE_URL%","%ID%","%CATE_ID%"
			);

			$cateList = '';
			foreach ($cateData as $value) {
				
				$imagesrc = checkImage('category/'.$value['categoryId'].'/'.$value['categoryPhoto']);

				$fields_replace = array(
					$value['categoryName'],$imagesrc,SITE_URL,$value['id'],$value['categoryId']
				);

				$cateList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $cateList;
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
		$fields = array("%VALUE%","%DISPLAY_VALUE%");	
		foreach($getState as $value){

			$alreadyFavorite = getTotalRows("tbl_favorite_categories",array("categoryId"=>$value['id'],"userId"=>$this->sessUserId),"id");

			if($alreadyFavorite == 0){
				$fields_replace = array($value['id'],$value['categoryName']);
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