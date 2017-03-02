<?php
class myCustomOrder {
	
	protected $db;
	public $module;
		
	function __construct($module,$searchText,$date,$status,$page) {
		global $db,$fields,$sessUserId,$currencyId,$currencyCode,$currencySign;
				
		$this->db = $db;
		$this->sessUserId = $sessUserId;
		$this->searchText = $searchText;
		$this->date = $date;
		$this->status = $status;
		$this->currencyId = $currencyId;
		$this->currencyCode = $currencyCode;
		$this->currencySign = $currencySign;
		$this->fields = $fields;
		$this->table = 'tbl_product_deals';
		$this->module=$module;
		$this->page = $page;
		
	}

	public function order_data(){

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
				$whereCon .= "productName LIKE '%".$this->searchText."%'";	
			}else{
				$whereCon .= "1 = 1";
			}

			//For category
			if($this->date != ''){
				$whereCon .=" AND DATE(createdDate) = '".date('Y-m-d',strtotime($this->date))."'";
			}

			//For sub category
			if($this->status != ''){
				$whereCon .=" AND order_status = '".$this->status."'";
			}

			
			//For total row count
			$queryCount = "SELECT id FROM tbl_custom_orders
					  WHERE $whereCon AND userId = ".$this->sessUserId." AND id_delete = 'n'
					  order by id desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT id,productName,orderId,productPrice,quantity,order_status,paymentStatus,createdDate
					  FROM tbl_custom_orders 
					  WHERE $whereCon AND userId = ".$this->sessUserId." AND id_delete = 'n'
					  order by id desc
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

	
	public function getOrderList(){


		$orderData = $this->order_data();
		//echo '<pre>';
		//print_r($orderData);exit;

		if(empty($orderData)){
			
			$main_content = new Templater(DIR_TMPL."/no_table_records-nct.tpl.php");
			$orderList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/order_list-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields = array(
				"%ORDER_ID%","%DATE%","%PRODUCT_NM%","%PRICE%","%CURR_SIGN%","%STATUS%","%PAY_BUTTON%","%ID%","%SITE_URL%"
			);

			$orderList = '';
			foreach ($orderData as $value) {

				$price = convertCurrency($this->currencyId,$value['productPrice']*$value['quantity']);
				if($value['order_status'] == 'a'){
					$status = 'Accepted';
				}else if($value['order_status'] == 'r'){
					$status = 'Rejected';
				}else if($value['order_status'] == 'p'){
					$status = 'Pending';
				}

				//For pay button
				$payBotton = '';
				if($value['order_status'] == 'a'){

					if($value['paymentStatus'] == 'n'){

						$payBotton = NULL;
						$main_content = new Templater(DIR_TMPL.$this->module."/pay_button-nct.tpl.php");
						$payBotton = $main_content->parse();

						$fields1 = array(
							"%ORDER_ID%"
						);

						$fields_replace1 = array(
							$value['id']
						);

					}else{
						
						$payBotton = NULL;
						$main_content = new Templater(DIR_TMPL.$this->module."/payid_button-nct.tpl.php");
						$payBotton = $main_content->parse();
					}

					$payBotton = str_replace($fields1, $fields_replace1, $payBotton);					
				}

				$fields_replace = array(
					$value['orderId'],date('m-d-Y',strtotime($value['createdDate'])),substr($value['productName'],0,15),number_format($price,2),$this->currencySign,$status,$payBotton,$value['id'],SITE_URL
				);

				$orderList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $orderList;
	}
	
	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		$selected_a = $selected_r = $selected_p = '';
		if($this->status == 'a'){
			$selected_a = "selected";
		}else if($this->status == 'r'){
			$selected_r = "selected";
		}else if($this->status == 'p'){
			$selected_p = "selected";
		}

		$fields = array(
			"%SELECTED_A%","%SELECTED_R%","%SELECTED_P%"
		);

		$fields_replace = array(
			$selected_a,$selected_r,$selected_p
		);

		$final_result =str_replace($fields,$fields_replace,$final_result);

		return $final_result;
	}

	
}