<?php
class paymentHistory {
	
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

	public function payment_data(){

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
			$queryCount = "SELECT user_id,transaction_id,transaction_type,payment_gateway,paid_amount,created_date FROM tbl_payment_history WHERE user_id = ".$this->sessUserId." order by created_date desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT user_id,transaction_id,transaction_type,payment_gateway,paid_amount,created_date FROM tbl_payment_history WHERE user_id = ".$this->sessUserId." order by created_date desc 
				LIMIT ".$offset." , ".LIMIT."";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		
		return $fetchRes;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'paymentHistory-nct',$this->totalRow);

		return $paginationData;
	}

	
	public function getpaymentList(){


		$paymentData = $this->payment_data();
		//echo '<pre>';
		//print_r($paymentData);exit;

		if(empty($paymentData)){
			
			$main_content = new Templater(DIR_TMPL."/no_table_records-nct.tpl.php");
			$paymentList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/payment_list-nct.tpl.php");
			$final_result = $main_content->parse();



			$fields = array(
				'%NUMBER%','%TXN_ID%','%PRODUCT_NM%','%PAYMENT_GATEWAY%','%DATE%','%SITE_CURR%','%AMOUNT%'
			);

			$paymentList = '';
			$i = 1;
			foreach ($paymentData as $value) {

				$title = $value['transaction_type']=='p'?'Product amount':'Deposit fund';

				$payment_gateway = ($value["payment_gateway"]=='p')?"Paypal":(($value["payment_gateway"]=='pg')?"Paga":(($value["payment_gateway"]=='vi')?"Visa":(($value["payment_gateway"]=='w')?"Wallet":"Verve")));

				$fields_replace = array(
					$i,$value['transaction_id'],$title,$payment_gateway,date('m-d-Y',strtotime($value['created_date'])),SITE_CURR,number_format($value['paid_amount'],2)
				);

				$paymentList .=str_replace($fields,$fields_replace,$final_result);
				$i++;
					
			}
		}

		return $paymentList;
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		return $final_result;
	}

	
}