<?php
class SubCategories {
	
	protected $db;
	public $module;
		
	function __construct($module,$page,$cateId) {
		global $db,$fields,$sessUserId;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->fields = $fields;
		$this->table = 'tbl_categories';
		$this->module=$module;
		$this->page = $page;
		$this->cateId = $cateId;
		
	}
	
	public function subCategories_data() 
	{
		if(isset($this->page) && $this->page > 1)
		{
			$offset = ($this->page - 1) * LIMIT;
		}
		else
		{
			$offset = 0;
		}


		
		$queryCount = "SELECT id from tbl_subcategory
		WHERE isActive='y' AND categoryId = ".$this->cateId." ORDER BY id desc";

		$resultCount = $this->db->pdoQuery($queryCount);
 		$this->totalRowCount = $resultCount->affectedRows();

		$query = "SELECT id,subcategoryName,subcategoryImage,subcategoryDesc from tbl_subcategory
		WHERE isActive='y' AND categoryId = ".$this->cateId." ORDER BY id desc LIMIT ".$offset." , ".LIMIT."";

		$result = $this->db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$this->totalRow = $result->affectedRows();
		
		return $fetchRes;
		
	}

	public function getSubCategoriesList(){


		$subCategoriesData = $this->subCategories_data();
		//echo '<pre>';
		//print_r($categoriesData);exit;

		if(empty($subCategoriesData)){
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$cateList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/subcategories_list-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields = array(
				'%SUBCATEGORY_NM%','%SUBCATEGORY_DESC%','%SUBCATEGORY_IMG%','%HREF%'
			);

			$cateList = '';
			foreach ($subCategoriesData as $value) {
				$cateImg = checkImage('subcategory/'.$value['id'].'/',$value['subcategoryImage']);
				
				$fields_replace = array(
					substr($value['subcategoryName'],0,40),substr($value['subcategoryDesc'],0,70),$cateImg,SITE_URL.'stores/'.$this->cateId.'/'.$value['id']
				);

				$cateList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $cateList;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'mybid',$this->totalRow);

		return $paginationData;
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content = $main_content->parse();

		$cateDesc = getTableValue('tbl_categories','description',array("id"=>$this->cateId));

		$fields = array(
				'%CATEGORY_DESC%','%CATE_ID%'
		);

		$fields_replace = array(
				$cateDesc,$this->cateId
		);

		$final_result .=str_replace($fields,$fields_replace,$main_content);

		return $final_result;
	}
}