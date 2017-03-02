<?php
class notifications {
	
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

	public function notification_data(){

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
			$queryCount = "SELECT id from tbl_user_notifications
				      WHERE toId = ".$this->sessUserId."
					  order by createdDate desc";

			$resultCount = $this->db->pdoQuery($queryCount);
	 		$this->totalRowCount = $resultCount->affectedRows();

			$query = "SELECT * from tbl_user_notifications
				      WHERE toId = ".$this->sessUserId."
					  order by createdDate desc
					  LIMIT ".$offset." , ".LIMIT."";

			$result = $this->db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$this->totalRow = $result->affectedRows();

	 		
		return $fetchRes;
	}

	public function getPagination(){
		
		$pager = getPagerData($this->totalRowCount, LIMIT,$this->page);

		$paginationData = pagination($pager, $this->page, 'notification-nct',$this->totalRow);

		return $paginationData;
	}

	
	public function getnotificationList(){


		$notificationData = $this->notification_data();
		//echo '<pre>';
		//print_r($notificationData);exit;

		if(empty($notificationData)){
			
			$main_content = new Templater(DIR_TMPL."/no_records-nct.tpl.php");
			$notificationList = $main_content->parse();	
		
		}else{

			$final_result = NULL;
			$main_content = new Templater(DIR_TMPL.$this->module."/notification_list-nct.tpl.php");
			$final_result = $main_content->parse();


			$fields = array(
				"%NOTI_DISC%","%NOTI_DATE%"
			);

			$notificationList = '';
			foreach ($notificationData as $value) {

				//notification data
				$notificationType = $value['notificationType'];
				$toId = $value['toId'];
				$refId = $value['refId'];
				$amount = number_format(convertCurrency($this->currencyId,$value['amount']),2);
				$createdDate = $value['createdDate'];

				$productName = getTableValue('tbl_product_deals',"productName",array("id"=>$refId));
				$coupon_code = getTableValue('tbl_coupons',"coupon_code",array("id"=>$refId));
				$customProductName = getTableValue('tbl_custom_orders',"productName",array("id"=>$refId));
				$status = getTableValue('tbl_custom_orders',"order_status",array("id"=>$refId));
				$orderStatus = $status=='a'?'accepted':'rejected';
				$reminder_title = getTableValue('tbl_reminders',"reminder_title",array("id"=>$refId));


				$notification = '';
				switch ($notificationType) {
					
					case '1':
						$notification = 'Product return refund amount '.$this->currencySign.$amount.' is received from admin for '.$productName;
						break;

					case '2':
						$notification = 'Redeem amount '.$this->currencySign.$amount.' is funded';
						break;

					case '3':
						$notification = 'New product deal as '.$productName.' is posted by admin';
						break;

					case '4':
						$notification = $this->currencySign.$amount.' is added in your wallet';
						break;

					case '5':
						$notification = 'A new promo code is posted on website by admin as '.$coupon_code;
						break;

					case '6':
						$notification = 'Custom order status is '.$orderStatus.' by admin for '.$customProductName;
						break;

					case '7':
						$notification = 'Reminder for '.$reminder_title;
						break;
				}
				
				
				$fields_replace = array(
					$notification,date('m-d-Y h:i A',strtotime($createdDate))					
				);

				$notificationList .=str_replace($fields,$fields_replace,$final_result);
					
			}
		}

		return $notificationList;
	}

	public function getPageContent(){
		$final_result = NULL;
		$main_content = new Templater(DIR_TMPL.$this->module."/".$this->module.".tpl.php");
		$final_result = $main_content->parse();

		return $final_result;
	}

	
}