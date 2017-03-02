<?php
class redeem {
	
	protected $db;
	public $module;
		
	function __construct($module,$page) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->page = $page;
		$this->currencyId = $currencyId;
		$this->currencyCode = $currencyCode;
		$this->currencySign = $currencySign;
		$this->fields = $fields;
		$this->table = 'tbl_product_deals';
		$this->module=$module;
		
	}

	public function requestData(){

		/*$data = $this->db->select("tbl_redeem_request",array("amount","status","createdDate"),array("userId"=>$this->sessUserId))->results();

		return $data;*/

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
			$queryCount = "SELECT id from tbl_redeem_request
				      WHERE userId = ".$this->sessUserId."
					  order by id desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT id,amount,status,createdDate from tbl_redeem_request
				      WHERE userId = ".$this->sessUserId."
					  order by id desc
					  LIMIT ".$offset." , ".LIMIT."";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		return $fetchRes;

	}

	public function getRedeemRequest(){
		
		$request = $this->requestData();
		$this->currencySign="$";
		if(empty($request)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$requestList = $main_content->parse();	
		
		}else{

			//print_r($request);exit;
			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/request_list-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields = array(
				"%ID%","%AMOUNT%","%DATE%","%CURRENCY_SIGN%","%STATUS%"
			);

			$requestList = '';
			$i = 1;
			foreach ($request as $key => $value) {

				//$status = $value['status'] == 'f'?'Funded':'Pending'; 
				if($value["status"] == 'p'){
					$status = 'Pending';
				}else if($value["status"] == 'f'){
					$status = 'Funded';
				}else if($value["status"] == 'r'){
					$status = 'Rejected';
				}
				
				$fields_replace = array(
					$i,$value['amount'],date('m-d-Y',strtotime($value['createdDate'])),$this->currencySign,$status
				);

				$requestList .= str_replace($fields, $fields_replace, $final_result);
				$i++;
			}
		}

		return $requestList;
		
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'favoriteCate-nct',$this->totalRow);

		return $paginationData;
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		return $final_result;
	}

	
}