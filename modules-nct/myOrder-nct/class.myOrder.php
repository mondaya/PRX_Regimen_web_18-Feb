<?php
class myOrder {
	
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
				$whereCon .= "p.productName LIKE '%".$this->searchText."%'";	
			}else{
				$whereCon .= "1 = 1";
			}

			//For category
			if($date != ''){
				$whereCon .=" AND DATE(o.createdDate) = '".date('Y-m-d',strtotime($date))."'";
			}

			//For sub category
			if($status != ''){
				$whereCon .=" AND o.deliveryStatus = '".$status."'";
			}

			
			//For total row count
			$queryCount = "SELECT o.id FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  WHERE $whereCon AND o.userId = ".$this->sessUserId." AND o.id_delete = 'n' AND o.paymentStatus = 'y' order by o.id desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT o.id,p.id as productId,p.productName,o.orderId,o.productPrice,o.quantity,o.deliveryStatus,o.transactionId,o.createdDate FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  WHERE $whereCon AND o.userId = ".$this->sessUserId." AND o.id_delete = 'n' AND o.paymentStatus = 'y' order by o.id desc
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
		
		if(empty($orderData)){
			
			$main_content = new Templater(DIR_TMPL."/no_table_records-nct.tpl.php");
			$orderList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/order_list-nct.tpl.php");
			$final_result = $main_content->parse();

			$fields = array(
				"%ORDER_ID%","%DATE%","%PRODUCT_NM%","%TRANSACTIONID%","%PRICE%","%CURR_SIGN%","%STATUS%","%ID%","%SITE_URL%","%PRODUCT_ID%"
			);

			$orderList = '';
			foreach ($orderData as $value) {

				$price = convertCurrency($this->currencyId,$value['productPrice']*$value['quantity']);
				if($value['deliveryStatus'] == 'd'){
					$status = 'Delivered';
				}else if($value['deliveryStatus'] == 'r'){
					$status = 'Returned';
				}else if($value['deliveryStatus'] == 'p'){
					$status = 'Pending';
				}else if($value['deliveryStatus'] == 's'){
					$status = 'Shipped';
				}

				$fields_replace = array(
					$value['orderId'],date('m-d-Y',strtotime($value['createdDate'])),substr($value['productName'],0,15),$value['transactionId'],number_format($price,2),$this->currencySign,$status,$value['id'],SITE_URL,$value['productId']
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

		$selected_d = $selected_r = $selected_p = $selected_s = '';
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

		$final_result =str_replace($fields,$fields_replace,$final_result);

		return $final_result;
	}

	
}