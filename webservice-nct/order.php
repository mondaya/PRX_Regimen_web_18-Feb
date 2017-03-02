<?php
	require_once("../includes-nct/config-nct.php");
	require_once("general-functions.php");
	$module = 'order-nct';
	$request_data=$_REQUEST;
	$filedata =$_FILES;
	
	$id = isset($request_data['id'])?$request_data['id']:0;
	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$start = isset($request_data['start'])?$request_data['start']:0;
	$limit = isset($request_data['limit'])?$request_data['limit']:8;
	$keyword = isset($request_data['keyword'])?$request_data['keyword']:'';
	$categoryId = isset($request_data['categoryId'])?$request_data['categoryId']:'';
	$subCategoryId = isset($request_data['subCategoryId'])?$request_data['subCategoryId']:'';
	$curCode = isset($request_data['currencyCode'])?$request_data['currencyCode']:$currencyCode;
	if(isset($request_data['action']) && strtolower($request_data['action'])=='addcustomorder' && $userId>0){
		extract($request_data);
		$data=json_decode($customorderData);
			
			if(count($data->addCustomOrders)>0)
			{
				$orderdata =$data->addCustomOrders;
				$data =array();
				$countryId ='';
				foreach ($orderdata as $key=> $value) {

					$data['userId'] =$userId;
	                $data['orderId'] = 'ORDER'.time().strtoupper(genrateRandom(4));
					$data['productName'] = $value->productName;
					$data['productUrl'] = $value->productUrl;
					//$data['productNumber'] = $value->productNumber;
					$data['productPrice'] = $value->productPrice;
					$data['quantity'] = $value->quantity;
					$data['size'] = $value->size;
					$data['color'] = $value->color;
	                $totalProductPrice =$value->productPrice * $value->quantity;
	                $dutiesAmount = getDutiesAmount_api($totalProductPrice,$userId,$countryId);
	                $adminCharge = getAdminCharge_api($totalProductPrice,$userId,$countryId);
	                $data['dutiesAmount'] = strval($dutiesAmount);
	                $data['adminCharge'] = strval($adminCharge);
	                $data['createdDate'] = date('Y-m-d H:i:s');
	                
					$db->insert('tbl_custom_orders',$data);
				}
				$status=true;
				$message='Custom order placed successfully'; 
				$res_array['status']=  $status; 
				$res_array['message']=  $message; 
		   		echo json_encode($res_array);
			}else{
				$res_array['status']=false;
				$res_array['message']='fill all values';    
		   		echo json_encode($res_array);
			}
    }else if(isset($request_data['action']) && strtolower($request_data['action'])=='editcustomorder' && $userId>0){
		extract($request_data);
    	if(!empty($productName) && !empty($productUrl) && !empty($productPrice) && !empty($quantity) && !empty($size) && !empty($color) && !empty($orderId)){
				$data['userId'] = $userId;
                $data['productName'] = $productName;
				$data['productUrl'] = $productUrl;
				$data['productPrice'] = $productPrice;
				//$data['productNumber'] = $productNumber;
				$data['quantity'] = $quantity;
				$data['size'] = $size;
				$data['color'] = $color;
                $totalProductPrice = $productPrice * $quantity;
                $dutiesAmount = getDutiesAmount_api($totalProductPrice,$userId,$countryId);
                $adminCharge = getAdminCharge_api($totalProductPrice,$userId,$countryId);
                $data['dutiesAmount'] = strval($dutiesAmount);
                $data['adminCharge'] = strval($adminCharge);
                $data['createdDate'] = date('Y-m-d H:i:s');
                
				$db->update('tbl_custom_orders',$data,array('id'=>$orderId));
 				$res_array['status']=true;
				$res_array['message']='custom order edited successfully';    
		   		echo json_encode($res_array);
		}else{
			$res_array['status']=false;
			$res_array['message']='fill all values';    
	   		echo json_encode($res_array);
		}			
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='getcustomorders' && $userId>0){
		$json_content = customOrderList_api($request_data,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='deletecustomorders' && $userId>0 && $id>0){
		$checkOriginal = getTableValue('tbl_custom_orders', 'userId', array('id'=>$id));
		if($checkOriginal == $userId) {
			$db->update('tbl_custom_orders',array("id_delete"=>"y"),array("id"=>$id));
			$res_array['status']=true;
			$res_array['message']='custom order deleted successfully.';    
	   		echo json_encode($res_array);			
		}else{
			$res_array['status']=false;
			$res_array['message']='As you are not ordered this item, So you can not delete this order.';    
	   		echo json_encode($res_array);
		}	
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='customorderdetail' && $userId>0 && $id>0){
		$json_content = customOrderDetail_api($userId,$id,$curCode);
		echo $json_content;
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='returncustomorder' && $userId>0 && $id>0){

		extract($request_data);
		$subject =isset($subject)?$subject:'';
		$message =isset($message)?$message:'';
		
		if(!empty($subject) && !empty($message))
		{
			$db->update("tbl_custom_orders",array("deliveryStatus"=>"r"),array("id"=>$id));

			$data = array();
			$data['userId'] = $userId;
			$data['orderId'] = $id;
			$data['subject'] = $subject;
			$data['message'] = $message;
			$data['createdDate'] = date('Y-m-d H:i:s');

			
			$returnId = $db->insert("tbl_custom_return",$data)->getLastInsertId();
			

			//For update pending amount
			$amount = getTableValue('tbl_custom_orders','paidAmount',array('id'=>$id));
			$oldPending = getTableValue('tbl_users','pendingAmount',array('id'=>$userId));
			$finalPending = strval($oldPending + $amount);
			$db->update('tbl_users',array('pendingAmount'=>$finalPending),array('id'=>$userId));
			
			//For images
			 $uploads_dir = DIR_UPD."returnImage";
		     if(!file_exists($uploads_dir)){
			 	mkdir($uploads_dir,0777,true);
			 }

		  //start upload image with custom order
			 foreach ($_FILES as $key => $value) 
			 {
				if (count($_FILES[$key]["name"])>1) 
				{
			 			for($i=0;$i<count($_FILES[$key]['name']);$i++)
			 			{
			 				 if ($_FILES[$key]['error'][$i] == UPLOAD_ERR_OK) 
			 				 {
			 				 	 $tmp_name =$_FILES[$key]["tmp_name"][$i];
			 		     		 $name = $_FILES[$key]["name"][$i];
			 		     		 move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 		     		  $valArray = array("returnId"=>$returnId,"imageName"=>$name,"createdDate"=>date("Y-m-d h:i:s"));
				 					$db->insert("tbl_customreturn_image", $valArray);	
			 		     	}
			 			}

			 	}else
			 	{
			 		if ($_FILES[$key]['error'] == UPLOAD_ERR_OK) 
			 		{
			 			   $tmp_name=$_FILES[$key]["tmp_name"];
			 			   $name = $_FILES[$key]["name"];
			 			   move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 			    $valArray = array("returnId"=>$returnId,"imageName"=>$name,"createdDate"=>date("Y-m-d h:i:s"));
				 			$db->insert("tbl_customreturn_image", $valArray);

			 		}
			 	}	
			}
			//end upload image with custom order

			//start move before uploaded image

				foreach ($_FILES as $key => $value) 
			 	{
					if (count($_FILES[$key]["name"])>1) 
					{
						if ($_FILES[$key]['name'][0] == '') 
				 		{
				 			$files = scandir($uploads_dir."/c".$id,1);
				 			foreach ($files as $file) {
				 				if(!($file=='.' || $file=='..'))
				 				{
				 					//$uploads_dir."/c".$id."/".$file;
				 					copy($uploads_dir."/c".$id."/".$file,$uploads_dir."/".$file);
				 					unlink($uploads_dir."/c".$id."/".$file);
				 					$valArray = array("returnId"=>$returnId,"imageName"=>$file,"createdDate"=>date("Y-m-d h:i:s"));
					 				$db->insert("tbl_customreturn_image", $valArray);
								}
				 				
				 			}
				 			rmdir($uploads_dir."/c".$id);
				 		}
				 	}else
				 	{
				 		if ($_FILES[$key]['name'] == '') 
				 		{
				 			$files = scandir($uploads_dir."/c".$id,1);
				 			foreach ($files as $file) {
				 				if(!($file=='.' || $file=='..'))
				 				{
				 					//$uploads_dir."/c".$id."/".$file;
				 					copy($uploads_dir."/c".$id."/".$file,$uploads_dir."/".$file);
				 					unlink($uploads_dir."/c".$id."/".$file);
				 					$valArray = array("returnId"=>$returnId,"imageName"=>$file,"createdDate"=>date("Y-m-d h:i:s"));
					 				$db->insert("tbl_customreturn_image", $valArray);
								}
				 				
				 			}
				 			rmdir($uploads_dir."/c".$id);
				 		}
				 	}	
			}
				if (count($_FILES)<=0) 
				 {
				 			$files = scandir($uploads_dir."/c".$id,1);
				 			foreach ($files as $file) {
				 				if(!($file=='.' || $file=='..'))
				 				{
				 					//$uploads_dir."/c".$id."/".$file;
				 					copy($uploads_dir."/c".$id."/".$file,$uploads_dir."/".$file);
				 					unlink($uploads_dir."/c".$id."/".$file);
				 					$valArray = array("returnId"=>$returnId,"imageName"=>$file,"createdDate"=>date("Y-m-d h:i:s"));
					 				$db->insert("tbl_customreturn_image", $valArray);
								}
				 				
				 			}
				 			rmdir($uploads_dir."/c".$id);
				 	}
			//end move before uploaded image
			$res_array['status']=true;
			$res_array['message']='Return request sent successfully.';    
	   		echo json_encode($res_array);
	   	}else
	   	{
	   		$res_array['status']=false;
			$res_array['message']='fill all required values.';    
   			echo json_encode($res_array);
	   	}
	}else if(isset($request_data['action']) && strtolower($request_data['action'])=='returnorderimage'){
		$json_content = return_custom_order_image_api($request_data,$filedata);
		echo $json_content;	
		
	}else {
		$res_array['status']=false;
		$res_array['message']='Oops! Something went wrong. Please try again.';    
   		echo json_encode($res_array);
	}
?>	