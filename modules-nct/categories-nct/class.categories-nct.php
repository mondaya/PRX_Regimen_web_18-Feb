<?php
class Categories {
	
	protected $db;
	public $module;
		
	function __construct($module,$page) {
		global $db,$fields,$sessUserId;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->fields = $fields;
		$this->table = 'tbl_categories';
		$this->module=$module;
		$this->page = $page;
		
	}
	
	public function categories_data() 
	{
		if(isset($this->page) && $this->page > 1)
		{
			$offset = ($this->page - 1) * LIMIT;
		}
		else
		{
			$offset = 0;
		}


		
		$queryCount = "SELECT id from tbl_categories
		WHERE isActive='y' ORDER BY id desc";

		$resultCount = $this->db->pdoQuery($queryCount);
 		$this->totalRowCount = $resultCount->affectedRows();

		$query = "SELECT id,categoryName,categoryPhoto,description from tbl_categories
		WHERE isActive='y' ORDER BY id desc LIMIT ".$offset." , ".LIMIT."";

		$result = $this->db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$this->totalRow = $result->affectedRows();
		
		return $fetchRes;
		
	}

	public function getCategoriesList(){


		$categoriesData = $this->categories_data();
		//echo '<pre>';
		//print_r($categoriesData);exit;

		if(empty($categoriesData)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$bidList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/categories_list-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields = array(
				'%CATEGORY_NM%','%CATEGORY_DESC%','%CATEGORY_IMG%','%HREF%','%FAVORITE_ICON%'
			);

			$bidList = '';
			foreach ($categoriesData as $value) {
				$cateImg = checkImage('category/'.$value['id'].'/',$value['categoryPhoto']);

				//Get category favorite or not
				$exist = getTableValue("tbl_favorite_categories", "id",array("categoryId"=>$value['id'],"userId"=>$this->sessUserId));
				$favoriteIcon='<a href="javascript:void(0)" class="active-link favourite" data-id="'.$value['id'].'" data-value="'.($exist == 0 ? 'on' : 'off').'" ><img src="'.SITE_IMG.'fav-'.($exist>0 ? 'on' : 'off').'.png" alt="favourite" /></a>';
				
				$fields_replace = array(
					substr($value['categoryName'],0,40),substr($value['description'],0,70),$cateImg,SITE_URL.'subcategories/'.$value['id'],$favoriteIcon
				);

				$bidList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $bidList;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'subcategories-nct',$this->totalRow);

		return $paginationData;
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();
		return $final_result;
	}
}