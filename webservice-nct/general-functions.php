<?php 
//helper function start
/*
	get image from other store
*/
function getImageFromStore($image_url)
{
	$ch = curl_init();
	$timeout = 0;
	curl_setopt ($ch, CURLOPT_URL, $image_url);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	// Getting binary data
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
	$image = curl_exec($ch);
	curl_close($ch);
	return $image;
}

function getd2dShippingAmount_api($price,$userId){
        global $db;

        $userSateId = getTableValue('tbl_users','stateId',array("id"=>$userId));

        $sData = $db->pdoQuery("select amount,minimumAmount from tbl_shipping_amount where stateId = ".$userSateId." and isActive = 'y'")->result();

        $fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();

        if($sData['amount'] != ''){
            $shippingPer = $sData['amount'];
        }else{
            $shippingPer = DEFAULT_SHIPPING;
        }
		$shippingAmount = $price * $shippingPer / 100;

        if($shippingAmount > $sData['minimumAmount']){
            $finalAmount = $shippingAmount;
        }else{
            $finalAmount = $sData['minimumAmount'];
        }

        return $finalAmount;
 }

function getPickShippingAmount_api($price,$stateId,$userId){
        global $db;
		$finalAmount = 0;
        if($stateId > 0){

            $sData = $db->pdoQuery("select amount,minimumAmount from tbl_shipping_amount where stateId = ".$stateId." and isActive = 'y'")->result();
            if($sData['amount'] != ''){
                $shippingPer = $sData['amount'];
            }else{
                $shippingPer = DEFAULT_SHIPPING;
            }
            $shippingAmount = $price * $shippingPer / 100;
            if($shippingAmount > $sData['minimumAmount']){
                $finalAmount = $shippingAmount;
            }else{
                $finalAmount = $sData['minimumAmount'];
            }
        }

        return $finalAmount;
    }
    function getDutiesAmount_api($price,$userId,$countryId){
        global $db;
         if($countryId=='')
        	{
        	  $countryId = getTableValue('tbl_users','countryId',array('id'=>$userId));
        	}
        
		$fetchRes = $db->select('tbl_duties_amount',array('amount','minimumAmount'),array('countryId'=>$countryId,'isActive'=>'y'))->result();

        if($fetchRes['amount'] > 0){
            $dutiesPer = $fetchRes['amount'];
        }else{
            $dutiesPer = DEFAULT_DUTIES;
        }
        $dutiesAmount = $price * $dutiesPer / 100;
        
        if($dutiesAmount > $fetchRes['minimumAmount']){
            $finalAmount = $dutiesAmount;
        }else{
            $finalAmount = $fetchRes['minimumAmount'];
        }
        return $finalAmount;
    }

    function getAdminCharge_api($price,$userId,$countryId){
        global $db;
        if($countryId=='')
        {
        	  $countryId = getTableValue('tbl_users','countryId',array('id'=>$userId));
        }
      

        $fetchRes = $db->select('tbl_admin_charge',array('amount','minimumAmount'),array('countryId'=>$countryId,'isActive'=>'y'))->result();

        if($fetchRes['amount'] > 0){
            $adminChargePer = $fetchRes['amount'];
        }else{
            $adminChargePer = DEFAULT_ADMIN_CHARGE;
        }

        $adminCharge = $price * $adminChargePer / 100;
        
        if($adminCharge > $fetchRes['minimumAmount']){
            $finalAmount = $adminCharge;
        }else{
            $finalAmount = $fetchRes['minimumAmount'];
        }

        return $finalAmount;
    }

    //get delivery address api
function getDeliveryAddress_api($userId,$deliveryOption,$pick_point){
        global $db;
        $address = ' ';

		$number = getTableValue('tbl_users','mobileNumber',array('id'=>$userId));
        $code = getTableValue('tbl_users','code',array('id'=>$userId));

        if($deliveryOption == 'p'){
            if($pick_point>0)
            {
            $fetchRes = $db->pdoQuery('Select p.id,p.pointName,p.pointAddress,co.countryName,s.stateName from tbl_pick_points as p 
                LEFT JOIN tbl_state as s ON(p.stateId = s.id)
                LEFT JOIN tbl_country as co ON(p.countryId = co.id)
                WHERE p.id = '.$pick_point.'')->result();
             $address = $fetchRes['pointAddress'].','.$fetchRes['pointName'].','.$fetchRes['stateName'].','.$fetchRes['countryName'].' '.$code.' '.$number;
           }else
           {
           	 $fetchRes = $db->pdoQuery('Select u.id,u.address,ct.cityName,co.countryName,s.stateName from tbl_users as u 
                LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
                LEFT JOIN tbl_state as s ON(u.stateId = s.id)
                LEFT JOIN tbl_country as co ON(u.countryId = co.id)
                WHERE u.id = '.$userId.'')->result();
           	 $address = $fetchRes['address'].','.$fetchRes['cityName'].','.$fetchRes['stateName'].','.$fetchRes['countryName'].' '.$code.' '.$number;
           }
           

        }else if($deliveryOption == 'd'){

           $fetchRes = $db->pdoQuery('Select u.id,u.address,ct.cityName,co.countryName,s.stateName from tbl_users as u 
                LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
                LEFT JOIN tbl_state as s ON(u.stateId = s.id)
                LEFT JOIN tbl_country as co ON(u.countryId = co.id)
                WHERE u.id = '.$userId.'')->result();
            
            $address = $fetchRes['address'].','.$fetchRes['cityName'].','.$fetchRes['stateName'].','.$fetchRes['countryName'].' '.$code.' '.$number;

        }else
        {
        	$fetchRes = $db->pdoQuery('Select u.id,u.address,ct.cityName,co.countryName,s.stateName from tbl_users as u 
                LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
                LEFT JOIN tbl_state as s ON(u.stateId = s.id)
                LEFT JOIN tbl_country as co ON(u.countryId = co.id)
                WHERE u.id = '.$userId.'')->result();
          
            $address = $fetchRes['address'].','.$fetchRes['cityName'].','.$fetchRes['stateName'].','.$fetchRes['countryName'].' '.$code.' '.$number;
        }

        return $address;
    }
//helper function end

/*get all category list*/
function category_list_api($keyword,$page,$limit,$userId) {
		global $db;
		$category_id = $category_name = NULL;
		$catagory_row = array();
		//scroll to page and display more limits data
		$start  = 0;
		$limit =$limit * $page;
		$i=0;
		$where = "";
		if($keyword!=""){
			$where = " AND categoryName LIKE '%".$keyword."%'";
		}
		if($page>0)
		{
			$allcategory = $db->select('tbl_categories', array('*'), array('isActive'=>'y'),$where.' ORDER BY id desc LIMIT '.$start.','.$limit)->results();
		$totRows = $db->select('tbl_categories', array('*'), array('isActive'=>'y'),$where)->affectedRows();
		
		}else
		{
			$allcategory = $db->select('tbl_categories', array('*'), array('isActive'=>'y'),$where.' ORDER BY id desc')->results();
		$totRows = $db->select('tbl_categories', array('*'), array('isActive'=>'y'),$where)->affectedRows();
			
		}
		
		if($totRows > 0){
			foreach ($allcategory as $key => $value) {
			 $category_row[$i]['category_id'] = $value['id'];
			 $catId= $value['id'];
			 $category_row[$i]['category_Img'] = checkImage('category/'.$value['id'].'/',$value['categoryPhoto']);	
			 $category_row[$i]['category_name'] = $value['categoryName'];
			/* $query= "select id,storeName,storeLink,storeImage,isScrap from tbl_stores where isActive='y' AND FIND_IN_SET(".$catId.",categoryId)";
				 $storlist=array();
				 $si=0;
				 $fetchResStore = $db->pdoQuery($query)->results();
				 	foreach ($fetchResStore as  $value) {
				 		$storlist[$si]['storeId'] =$value['id'];
				 		$storlist[$si]['storeName'] =$value['storeName'];
				 		$si++;
				 	}
				 	$category_row[$i]['storeList'] =$storlist; */
			if($userId>0){
				 $checkFav = $db->select('tbl_favorite_categories', array('id'), array('userId'=>$userId,'categoryId'=>$value['id']))->affectedRows();
				
				if($checkFav > 0){	
					$category_row[$i]['isfav'] = true;
				}else{
					$category_row[$i]['isfav'] = false;
				}
			}else{
				$category_row[$i]['isfav'] = false;
			}	
			 $i++;
			}
			$status = true;
			$message = "success";
		}else{
			$status = false;
			$message = "No any category found";
		}
		$res_array['total_records']=$totRows;
		$narecords =(intval($totRows) - intval($limit));
		$res_array['next_available_records']= $narecords >0?$narecords:0;
		$res_array['categoryList'] = $category_row;	
		$res_array['status'] = $status;
		$res_array['message'] = $message;
		$res_json = json_encode($res_array);
		return $res_json;
}
/*  get all home category api
	by:NTC 28102016
*/
function category_home_api()
{
		global $db;
		$homecategory = $db->select('tbl_categories', array('*'), array('isActive'=>'y', 'is_display'=>'y'), 'ORDER BY id desc')->results();
		
			$i=0;
			$category_row =  array();
			foreach ($homecategory as $value) {
					
				$category_row[$i]['category_id'] = $value['id'];
				$category_row[$i]['category_Img'] = checkImage('category/'.$value['id'].'/',$value['categoryPhoto']);	
				$category_row[$i]['category_name'] = $value['categoryName'];
				$i++;
			}
			
		if($i>0){
			$res_array['homecategory']=$category_row;
			$status =true;
			$res_array['status']=$status;
			$res_array['message']='success';
		}else
		{
			$status =false;
			$res_array['status']=$status;
			$res_array['message']='Home category not available.';
		}
		
		return json_encode($res_array);
}
/*set-remove category favorite api*/
function favCategoryUpdate_api($request_data){
	global $db;
	extract($request_data);
	$objPost = new stdClass();
	$objPost->categoryId = isset($categoryId)?$categoryId:0;
	$objPost->userId = isset($userId)?$userId:0;
	if($categoryId > 0 && $userId > 0) {
		$exist = getTableValue('tbl_favorite_categories', 'id', array('categoryId'=>$objPost->categoryId,"userId"=>$userId));
		if(empty($exist)) {
			$objPost->createdDate = date('Y-m-d H:i:s');
			$fav_id = $db->insert('tbl_favorite_categories', (array)$objPost)->lastInsertId();
			$status = true;
			$res_array['status'] = $status;
			$res_array['message'] = 'You have successfully set this category as favorite category.';
			echo json_encode($res_array);
		} else {
			$db->delete('tbl_favorite_categories', (array)$objPost);
			$status = true;
			$res_array['status'] = $status;
			$res_array['message'] = 'You have successfully remove this category from your favorite list.';
			echo json_encode($res_array);
		}
		
	}else{
			$status = false;
			$res_array['status'] = $status;
			$res_array['message'] = 'Fill all values.';
			echo json_encode($res_array);
	}
}
/*get all favourite categories*/
function fav_categoryList_api($userId){
	global $db;
	extract($request_data);
	$category_row = array();
	$i = 0;
	if($userId > 0){
	$allcategory = $db->pdoQuery("SELECT f.*,c.*,c.id as catId from tbl_favorite_categories as f LEFT JOIN tbl_categories as c on(f.categoryId = c.id) where c.isActive='y' AND f.userId=".$userId." ORDER BY f.createdDate DESC");
	$categories = $allcategory->results();
	$totRows = $allcategory->affectedRows();
	if($totRows > 0){
		foreach ($categories as $key => $value) {
			$category_row[$i]['category_id'] = $value['catId'];
			$category_row[$i]['category_Img'] = checkImage('category/'.$value['catId'].'/',$value['categoryPhoto']);	
			$category_row[$i]['category_name'] = $value['categoryName'];
			$i++; 
		}
		$status = true;
		$message = "success";
	}else{
		$status = false;
		$message = "No any category found";
	}
	}else{
		$status = false;
		$message = "Fill all value";
	}
	$res_array['categoryList'] = $category_row;	
	$res_array['status'] = $status;
	$res_array['message'] = $message;
	$res_json = json_encode($res_array);
	return $res_json;
}
/*get subcategory list from category id*/
function subcategory_list_api($categoryId,$page,$limit){
	global $db;
		$subcatagory_row = array();
		//scroll to page and display more limits data
		$start  = 0;
		$limit =$limit * $page;
		$i=0;
		if($page>0)
		{
		$allsubcategory = $db->select('tbl_subcategory', array('*'), array('isActive'=>'y', 'categoryId'=>$categoryId), 'ORDER BY id desc LIMIT '.$start.','.$limit)->results();
		}else{
			$allsubcategory = $db->select('tbl_subcategory', array('*'), array('isActive'=>'y', 'categoryId'=>$categoryId), 'ORDER BY id desc')->results();
		}
		
		$totRows = $db->select('tbl_subcategory', array('*'), array('isActive'=>'y','categoryId'=>$categoryId))->affectedRows();
		 	$subcategory_row=array();
		if($totRows > 0){	
			foreach ($allsubcategory as $key => $svalue) {
			 $subcategory_row[$i]['subcategory_id'] = $svalue['id'];
			 $subcategory_row[$i]['subcategory_Img'] = checkImage('subcategory/'.$svalue['id'].'/',$svalue['subcategoryImage']);	
			 $subcategory_row[$i]['subcategory_name'] = $svalue['subcategoryName'];
			 $i++;
			}
			$status = true;
			$message = "success";
		}else{
			$status = false;
			$message = "No any category found";
		}

		$res_array['total_records']=$totRows;
		$narecords =(intval($totRows) - intval($limit));
		$res_array['next_available_records']= $narecords >0?$narecords:0;
		$res_array['subCategoryList'] = $subcategory_row;	
		$res_array['status'] = $status;
		$res_array['message'] = $message;
		$res_json = json_encode($res_array);
		return $res_json;
}
/*get store list with filters*/
function getStoreList_api($userId,$catId,$subCatId,$page,$limit){
	global $db;
	$whereCon = '';
	//scroll to page and display more limits data
	$start  = 0;
	$limit =$limit * $page;
	$i=0;
	$res_row = array();

		//For category
		if($catId != ''){
			$whereCon .=" FIND_IN_SET(".$catId.",categoryId)";
		}

		//For sub category
		if($subCatId != ''){
			$whereCon .=" AND FIND_IN_SET(".$subCatId.",subcategoryId)";
		}
		//For is active
		if($subCatId > 0 && $catId > 0 || $catId > 0){
			$isActive = "AND isActive='y'";
		}else{
			$isActive = "isActive='y'";
		}

		$queryCount = "SELECT id from tbl_stores
		WHERE $whereCon $isActive ORDER BY id desc";

		$totalRowCount = $db->pdoQuery($queryCount)->affectedRows();
		if($totalRowCount>0){

		$query = "SELECT * from tbl_stores WHERE $whereCon $isActive ORDER BY id desc LIMIT ".$start." , ".$limit."";
		$fetchRes = $db->pdoQuery($query)->results();
		foreach($fetchRes as $res){
 			$res_row[$i]['storeId'] = $res['id'];
 			$res_row[$i]['storeName'] = $res['storeName'];
 			$res_row[$i]['storeImg'] = checkImage('store-nct/'.$res['id'].'/',$res['storeImage']);
 			$res_row[$i]['storeLink'] = $res['storeLink'];
 			$res_row[$i]['storeCartLink'] = $res['storeCartLink'];
 			$res_row[$i]['isScrap'] = $res['isScrap'];
 			if($userId>0){
 				$exist = getTableValue("tbl_favourite_store", "id",array("storeId"=>$res['id'],"userId"=>$userId));
 				$isfav = $exist>0?true:false;
 			}else{
 				$isfav = false;
 			}
 			$res_row[$i]['isFavourite'] = $isfav; 

 			$status = true;
 			$message = "success";
 			$i++;
 		}
 		}else{
 			$status = false;
 			$message = "No any store found";
 		}
 		
 		$res_array['total_records']=$totalRowCount;
		$narecords =(intval($totalRowCount) - intval($limit));
		$res_array['next_available_records']= $narecords >0?$narecords:0;
 		$res_array['storeList'] = $res_row;	
		$res_array['status'] = $status;
		$res_array['message'] = $message;
		
		$res_json = json_encode($res_array);
		return $res_json;
 		
}
//get all store list api
function getAllStoreList_api($userId,$page,$limit){
	global $db;
	//scroll to page and display more limits data
	$start  = 0;
	$limit =$limit * $page;
	$i=0;
	$res_row = array();
		$totalRowCount = $db->pdoQuery("SELECT * from tbl_stores WHERE  isActive='y'")->affectedRows();
		if($totalRowCount>0){

			$query = "SELECT * from tbl_stores WHERE  isActive='y' ORDER BY id desc LIMIT ".$start." , ".$limit."";
			$fetchRes = $db->pdoQuery($query)->results();
			foreach($fetchRes as $res){
	 			$res_row[$i]['storeId'] = $res['id'];
	 			$res_row[$i]['storeName'] = $res['storeName'];
	 			$res_row[$i]['storeImg'] = checkImage('store-nct/'.$res['id'].'/',$res['storeImage']);
	 			$res_row[$i]['storeLink'] = $res['storeLink'];
	 			$res_row[$i]['storeCartLink'] = $res['storeCartLink'];
	 			$res_row[$i]['isScrap'] = $res['isScrap'];
	 			if($userId>0){
	 				$exist = getTableValue("tbl_favourite_store", "id",array("storeId"=>$res['id'],"userId"=>$userId));
	 				$isfav = $exist>0?true:false;
	 			}else{
	 				$isfav = false;
	 			}
	 			$res_row[$i]['isFavourite'] = $isfav; 

	 			$status = true;
	 			$message = "success";
	 			$i++;
 		}
 		}else{
 			$status = false;
 			$message = "No any store found";
 		}
 		
 		$res_array['total_records']=$totalRowCount;
		$narecords =(intval($totalRowCount) - intval($limit));
		$res_array['next_available_records']= $narecords >0?$narecords:0;
 		$res_array['storeList'] = $res_row;	
		$res_array['status'] = $status;
		$res_array['message'] = $message;
		
		$res_json = json_encode($res_array);
		return $res_json;
 }
/*
	get all favorite store api
	:by NTC 13102016
*/
function fav_storeList_api($userId){
	global $db;
	extract($request_data);
	$store_row = array();
	$i = 0;
	if($userId > 0){
	$allStore = $db->pdoQuery("SELECT f.*,c.*,c.id as storeId from tbl_favourite_store as f LEFT JOIN tbl_stores as c on(f.storeId = c.id) where c.isActive='y' AND f.userId=".$userId." ORDER BY f.createdDate DESC");
	$stores = $allStore->results();
	$totRows = $allStore->affectedRows();
	if($totRows > 0){
		foreach ($stores as $key => $value) {
			$store_row[$i]['store_id'] = $value['storeId'];
			$store_row[$i]['store_Img'] = checkImage('store-nct/'.$value['storeId'].'/',$value['storeImage']);	
			$store_row[$i]['store_name'] = $value['storeName'];
			$store_row[$i]['storeLink'] = $value['storeLink'];
 			$store_row[$i]['storeCartLink'] = $value['storeCartLink'];
			$store_row[$i]['isScrap'] = $value['isScrap'];	
			$i++;
		}
		$status = true;
		$message = "success";
	}else{
		$status = false;
		$message = "No any store found";
	}
	}else{
		$status = false;
		$message = "Fill all value";
	}
	$res_array['storeList'] = $store_row;	
	$res_array['status'] = $status;
	$res_array['message'] = $message;

	$res_json = json_encode($res_array);
	return $res_json;
}

/*
	set remove favorite store api
	:by NTC 12102016
*/
function favStoreUpdate_api($request_data)
{
	global $db;
	extract($request_data);
	$objpost= new stdClass();
	$objpost->storeId =isset($storeId)?$storeId:0;
	$objpost->userId =isset($userId)?$userId:0;
	if($storeId>0 && $userId>0)
	{
		$exituser =getTableValue('tbl_users','id',array('id'=>$objpost->userId));
		$exitstore =getTableValue('tbl_stores','id',array('id'=>$objpost->storeId));
		
		if(empty($exituser) || empty($exitstore))
		{
			if(empty($exituser))
			{
				$status=false;
				$res_array['status']=$status;
				$res_array['message']='user not exist.';
				return json_encode($res_array);
			}else
			{
				$status=false;
				$res_array['status']=$status;
				$res_array['message']='store not exist.';
				return json_encode($res_array);
			}
		}else{
			$exist =getTableValue('tbl_favourite_store','id',array('storeId'=>$objpost->storeId,'userId'=>$objpost->userId));
			if(empty($exist))
			{
				$objpost->createdDate =date('Y-m-d H:i:s');
				$fav_store_id =$db->insert('tbl_favourite_store',(array)$objpost)->lastInsertId();
				$status =true;
				$res_array['status']=$status;
				$res_array['message']='You have successfully set this store as favorite store.';
				return json_encode($res_array);
			}else
			{
				$db->delete('tbl_favourite_store',(array)$objpost);
				$status=true;
				$res_array['status']=$status;
				$res_array['message']='You have successfully remove this store from your favorite store.';
				return json_encode($res_array);
			}
		}

	}else
	{
		$status=false;
		$res_array['status']=$status;
		$res_array['message']='fill all values.';
		return json_encode($res_array);
	}
}

/*get country & its state list*/
function getCountrySate_api(){
	global $db;
	$selCountry = $db->select('tbl_country',array('id','countryName'),array('isActive'=>'y'),' ORDER BY countryName')->results();
	$i=0;
	foreach($selCountry as $country){
		$c_row[$i]['countryid'] = $country['id'];
		$c_row[$i]['countryName'] = $country['countryName'];
		$selState = $db->select('tbl_state',array('id','stateName'),array('countryId'=>$country['id'],'isActive'=>'y'),' ORDER BY stateName ASC')->results();
		$j=0;
		foreach($selState as $state){
			$c_row[$i]['stateList'][$j]['stateid'] = $state['id'];
			$c_row[$i]['stateList'][$j]['stateName'] = $state['stateName'];
			$j++;
		}
		$i++;
	}
	$res_array['countrySateList'] = $c_row;	
	$res_array['status'] = true;
	$res_array['message'] = "success";
	$res_json = json_encode($res_array);
	return $res_json;		
}
/*seperate state selection list*/
function getState_api($countryId){
	global $db;

	$s_row = array();
	if($countryId == ''){
		$countryId = 0;
	}
	$selState = $db->select('tbl_state',array('id','stateName'),array('countryId'=>$countryId,'isActive'=>'y'),' ORDER BY stateName ASC')->results();
	$i=0;
	foreach($selState as $state){
		$s_row[$i]['id'] = $state['id'];
		$s_row[$i]['stateName'] = $state['stateName'];
		$i++;
	}
	$res_array['stateList'] = $s_row;	
	$res_array['status'] = true;
	$res_array['message'] = "success";
	$res_json = json_encode($res_array);
	return $res_json;	
}
/*get city list*/
function getCity_api($countryId,$stateId){
	global $db;
	
	$city_row = array();
	if($countryId == ''){
		$countryId = 0;
	}
	if($stateId == ''){
		$stateId = 0;
	}
	$selCity = $db->select('tbl_city',array('id','cityName'),array('countryId'=>$countryId,'stateId'=>$stateId,'isActive'=>'y'),' ORDER BY cityName ASC')->results();
	$i=0;
	foreach($selCity as $city){
		$city_row[$i]['id'] = $city['id'];
		$city_row[$i]['cityName'] = $city['cityName'];
		$i++;
	}
	$res_array['cityList'] = $city_row;	
	$res_array['status'] = true;
	$res_array['message'] = "success";
	$res_json = json_encode($res_array);
	return $res_json;	
}
/*Login with email*/
function login_api($request_data) {
	global $db;
	extract($request_data);
	$res_array = array();
	$req_email = strtolower($email);
	$req_password = $password;
	$req_secret = $secret;
	$deviceToken =isset($deviceToken)?$deviceToken:'';
	$deviceType =isset($deviceType)?$deviceType:'';
	if ($req_email == '' || $req_password == '' || $req_secret == '') {
		$status = false;
		$message = "Please provide all required details.";
	}
	else {
		$query = "select * from tbl_users where (email='" . base64_encode($req_email) . "' and password='" . md5($req_password) . "' and secret='". $req_secret ."')";
		$result = $db->pdoQuery($query);
		$fetchRes = $result->result();
		$totalRow = $result->affectedRows();

		if ($totalRow > 0) {

			$uId = $fetchRes['id'];
			$isActive = $fetchRes['isActive'];
			$password = $fetchRes['password'];
			$activationCode = $fetchRes['activationCode'];
			if($deviceToken!=''){
				$db->update('tbl_users',array('deviceToken'=>$deviceToken,'deviceType'=>$deviceType),array('id'=>$uId));
			}

			if ($isActive == 'y') {
				$status = true;
				$message = "Login successfully";
				$uId=$fetchRes['id'];
						$res_array['userId'] = $fetchRes['id'];
						$res_array['salute'] = $fetchRes['salute'];
						$res_array['firstName'] = $fetchRes['firstName'];
						$res_array['lastName'] = $fetchRes['lastName'];
						$res_array['email'] = base64_decode($fetchRes['email']);
						$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
						$res_array['address']=$fetchRes['address'];
						$res_array['zipCode']=$fetchRes['zipCode'];
						$res_array['countryId']=is_null($fetchRes['countryId'])?'':$fetchRes['countryId'];
						$countryName =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
						$res_array['countryName'] =is_null($countryName)?'':$countryName;
						$res_array['stateId']=is_null($fetchRes['stateId'])?'':$fetchRes['stateId'];
						$stateName=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
						$res_array['stateName']=is_null($stateName)?'':$stateName;
						$res_array['cityId']=is_null($fetchRes['cityId'])?'':$fetchRes['cityId'];
						$cityName =getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
						$res_array['cityName']=is_null($cityName)?'':$cityName;
						$res_array['countryCode']=$fetchRes['code'];
						$res_array['mobile']=$fetchRes['mobileNumber'];
						$res_array['secret']=$fetchRes['secret'];
						$res_array['gen']=$fetchRes['gender'];
						$res_array['member']=$fetchRes['createdDate']=="0000-00-00 00:00:00"?'':$fetchRes['createdDate'];
						$res_array['ip']=$fetchRes['ipaddress'];
						$res_array['birthDate']=$fetchRes['birthDate']=="0000-00-00 00:00:00" || $fetchRes['birthDate']=="0000-00-00"?'':$fetchRes['birthDate'];
						$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
						$res_array['deviceToken']=$fetchRes['deviceToken'];	
			}
			else {
				if ($isActive == 'n') {
					$status = false;
					$message = "Please check your email for account verification link or contact admin for more info!!";
				}				
			}
		}
		else {
			$status = false;
			$message = "Invalid credential.";
		}
	}
	$res_array['status'] = $status;
	$res_array['message'] = $message;
	$res_json = json_encode($res_array);
	return $res_json;
}
/*social login api*/
function social_login_api($request_data){
	global $db;
		extract($request_data);
		$deviceToken =isset($deviceToken)?$deviceToken:'';
		$deviceType =isset($deviceType)?$deviceType:'';
		if ($firstName != '' && $email != '') {
			$fetchRes = $db->pdoQuery('SELECT * from tbl_users where email = "' . base64_encode(strtolower($request_data['email'])) . '" ')->result();

			if (isset($fetchRes['id']) && $fetchRes['id'] > 0) {
				$uId=$fetchRes['id'];
				if($deviceToken!=''){
					$db->update('tbl_users',array('deviceToken'=>$deviceToken,'deviceType'=>$deviceType),array('id'=>$fetchRes['id']));
				}
						$res_array['userId'] = $fetchRes['id'];
						$res_array['salute'] = $fetchRes['salute'];
						$res_array['firstName'] = $fetchRes['firstName'];
						$res_array['lastName'] = $fetchRes['lastName'];
						$res_array['email'] = base64_decode($fetchRes['email']);
						$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
						$res_array['address']=$fetchRes['address'];
						$res_array['zipCode']=$fetchRes['zipCode'];
						$res_array['countryId']=is_null($fetchRes['countryId'])?'':$fetchRes['countryId'];
						$countryName =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
						$res_array['countryName'] =is_null($countryName)?'':$countryName;
						$res_array['stateId']=is_null($fetchRes['stateId'])?'':$fetchRes['stateId'];
						$stateName=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
						$res_array['stateName']=is_null($stateName)?'':$stateName;
						$res_array['cityId']=is_null($fetchRes['cityId'])?'':$fetchRes['cityId'];
						$cityName =getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
						$res_array['cityName']=is_null($cityName)?'':$cityName;
						$res_array['countryCode']=$fetchRes['code'];
						$res_array['mobile']=$fetchRes['mobileNumber'];
						$res_array['secret']=$fetchRes['secret'];
						$res_array['gen']=$fetchRes['gender'];
						$res_array['member']=$fetchRes['createdDate']=="0000-00-00 00:00:00"?'':$fetchRes['createdDate'];
						$res_array['ip']=$fetchRes['ipaddress'];
						$res_array['birthDate']=$fetchRes['birthDate']=="0000-00-00 00:00:00" || $fetchRes['birthDate']=="0000-00-00"?'':$fetchRes['birthDate'];
						$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
						$res_array['deviceToken']=$fetchRes['deviceToken'];	
				$status = true;
				$res_array['status'] = $status;
				$res_array['message'] = 'Welcome back ' . $fetchRes['firstName'];
				echo json_encode($res_array);
			}
			else {
				$usrData['firstName'] = $firstName;
				$usrData['lastName'] = isset($lastName) ? $lastName : " ";
				$usrData['email'] = base64_encode($email);
				genrateRandom(5);
				$usrData['isActive'] = 'y';
				$usrData['createdDate'] = date('Y-m-d H:i:s');
				$usrData['member'] = date('Y-m-d H:i:s');
				$password = time();
				$usrData['password'] = md5($password);
				$usrData['activationCode'] = md5(time());
				$usrData['loginWith'] = 'social';
				$usrData['deviceToken'] = $deviceToken;
				$usrData['deviceType'] = $deviceType;

				$db->insert("tbl_users", $usrData);
				$lastInsertId = $db->getLastInsertId();
				$notidata=array(
							'newProductPosted'=>'y',
							'amountAddedInWallet'=>'y',
							'receiveReplayFromAdmin'=>'y',
							'receiveReplayFromAdmin'=>'y',
							'newPrormoPosted'=>'y',
							'orderStatusByAdmin'=>'y',
							'reminder'=>'y',
							'createdDate'=>date('Y-m-d H:i:s'),
							'userId'=>$lastInsertId
							);
				$db->insert('tbl_notifications',$notidata);
				$datasubsribe =array(
							'email'=>base64_encode($email),	
							'is_active'=>'y',
							'created_date'=>date('Y-m-d H:i:s'),
							'ipaddress'=>get_ip_address(),
							);
				$db->insert('tbl_newsletter_subscriber',$datasubsribe);

				$to = $email;
				$greetings = $usrData['firstName'];
				$password = $password;
				$email = $to;
				$subject = 'Thank you for the registration!';
				$arrayCont = array('FIRST_NAME'=>$greetings,'EMAIL'=>$email,'PASSWORD'=>$password,'adminName'=>SITE_NM);
				
				sendMail($to, 'social_signup', $arrayCont);
				$qry="select * from tbl_users where id=".$lastInsertId;
				$fetchRes =$db->pdoQuery($qry)->result();
				if($deviceToken!=''){
					$db->update('tbl_users',array('deviceToken'=>$deviceToken),array('id'=>$fetchRes['id']));
				}
				$status = true;
				$res_array['userId'] = (int)$fetchRes['id'];
				$res_array['salute'] = $fetchRes['salute'];
				$res_array['firstName'] = $fetchRes['firstName'];
				$res_array['lastName'] = $fetchRes['lastName'];
				$res_array['email'] = base64_decode($fetchRes['email']);
				$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
				$res_array['address']=$fetchRes['address'];
				$res_array['zipCode']=$fetchRes['zipCode'];
				$res_array['countryId']=$fetchRes['countryId'];
				$res_array['countryName'] =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
				$res_array['stateId']=$fetchRes['stateId'];
				$res_array['stateName']=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
				$res_array['cityId']=$fetchRes['cityId'];
				$res_array['cityName']=getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
				$res_array['countryCode']=$fetchRes['code'];
				$res_array['mobile']=$fetchRes['mobileNumber'];
				$res_array['secret']=$fetchRes['secret'];
				$res_array['gen']=$fetchRes['gender'];
				$res_array['member']=$fetchRes['createdDate']=="0000-00-00 00:00:00"?'':$fetchRes['createdDate'];
				$res_array['ip']=$fetchRes['ipaddress'];
				$res_array['birthDate']=$fetchRes['birthDate']=="0000-00-00 00:00:00" || $fetchRes['birthDate']=="0000-00-00"?'':$fetchRes['birthDate'];
				$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
				$res_array['status'] = $status;
				$res_array['message'] = 'Registration successful';
				echo json_encode($res_array);
			}
		}
		else {
			$status = false;
			$res_array['status'] = $status;
			$res_array['message'] = 'Failed to retrieve required information. Please try again.';
			
			echo json_encode($res_array);
		}
}
/*forgot password api*/
function forgotPassword_api($request_data){
	global $db;
	$emailaddress = ($request_data['email'])?$request_data['email']:'';
	$emailaddress =strtolower($emailaddress);
    if(!empty($emailaddress)) {
        $queryRes = $db->pdoQuery("SELECT * FROM tbl_users WHERE email='".base64_encode($emailaddress)."' AND isActive ='y'");
        $emailpresent = $queryRes->affectedRows();
        if($emailpresent > 0){
            $userdata=$queryRes->result();
            $password=generatePassword(6);
            $passwordmd5=md5($password);
            $activationCode=$userdata['activationCode'];
            $username=$userdata['firstName'];
            $secret=$userdata['secret'];
            $newpassword = $password;
            $finalpassword = $passwordmd5;
            $id=$userdata['id'];
            $to=$emailaddress;
            $valArray = array('password'=>$finalpassword);
            $db->update('tbl_users',$valArray,array('email'=>base64_encode($emailaddress)));
                $conarray=array(
                    'greetings'=>$username,
                    'EMAIL'=>$emailaddress,
                    'PASSWORD'=>$password,
                    'SECRET'=>$secret
                );

                sendMail($to,"forgot_password",$conarray);
             	$status = true;
				$res_array['status'] = $status;
				$res_array['message'] = 'Please check your E-mail. New password has been successfully sent to your email Account.';
				echo json_encode($res_array);
        } else {
        		$status = false;
				$res_array['status'] = $status;
				$res_array['message'] = 'Opps.. email entered is wrong..!';
				echo json_encode($res_array);
        }
    } else {
			$status = false;
			$res_array['status'] = $status;
			$res_array['message'] = 'Email is Required';
			echo json_encode($res_array);
  	}
}
/*subscribe for newsletter*/
function sbscrb_newslleter_api($request_data){
	global $db;
	extract($request_data);
	$objPost = new stdClass();
	$objPost->email = strtolower(!empty($subEmail)?base64_encode($subEmail):'');
	if(!empty($objPost->email)) {
		$exist = getTableValue('tbl_newsletter_subscriber', 'id', array('email'=>$objPost->email));
		if(empty($exist)) {
			$objPost->hash = md5(rand(0,1000));
			$objPost->is_active = 'n';
			$objPost->created_date = date('Y-m-d H:i:s');
			$objPost->ipaddress = get_ip_address();
			$sub_id = $db->insert('tbl_newsletter_subscriber', (array)$objPost)->lastInsertId();
			$arrayCont = array('activationLink'=>SITE_URL.'newsletter/verify/'.$sub_id);
			sendMail($subEmail,'newlater_succ',$arrayCont);
			$status = true;
			$res_array['status'] = $status;
			$res_array['message'] = 'Please check your E-mail. you have successfully subscribe for newsletter.';
			echo json_encode($res_array);
		} else {
			$status = false;
			$res_array['status'] = $status;
			$res_array['message'] = 'you have already subscribe for newsletter.';
			echo json_encode($res_array);
		}
		
	} else {
			$status = false;
			$res_array['status'] = $status;
			$res_array['message'] = 'Fill all values.';
			echo json_encode($res_array);
	}
}
/*footer static pages*/
function staticPages_api(){
	global $db;
	$query = "SELECT * from tbl_content WHERE isActive = 'y' order by pId desc";
	$result = $db->pdoQuery($query);
	$fetchRes = $result->results();
	$pageList = array();
	$i=0;
	if(!empty($fetchRes)){
		foreach ($fetchRes as $value) {
			$pageList[$i]['pageId'] = $value['pId'];
			$pageList[$i]['pageTitle'] = $value['pageTitle'];
			$pageList[$i]['pageWebLink'] = SITE_URL.'content/'.$value['pId'].'/';
			$pageList[$i]['content'] = base64_encode($value['pageDesc']);
			$i++;
		}
		$res_array['pageList'] = $pageList;
		$status = true;
		$res_array['status'] = $status;
		$res_array['message'] = 'success';
		echo json_encode($res_array);
	}else{
		$status = false;
		$res_array['status'] = $status;
		$res_array['message'] = 'No any info page found.';
		echo json_encode($res_array);
	}
}
/*user profile detail*/		
function getUserProfile_api($userId){
	global $db;
	$fetchRes = $db->pdoQuery("SELECT u.*,ct.cityName,s.stateName,c.countryName FROM tbl_users AS u LEFT JOIN tbl_city AS ct ON u.cityId = ct.id  LEFT JOIN tbl_state AS s ON u.stateId = s.id LEFT JOIN tbl_country AS c ON u.countryId = c.id WHERE u.id = ?", array($userId))->result();
		$uId=$fetchRes['id'];
		$res_array['userId'] = $fetchRes['id'];
		$res_array['salute'] = $fetchRes['salute'];
		$res_array['firstName'] = $fetchRes['firstName'];
		$res_array['lastName'] = $fetchRes['lastName'];
		$res_array['email'] = base64_decode($fetchRes['email']);
		$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
		$res_array['address']=$fetchRes['address'];
		$res_array['zipCode']=$fetchRes['zipCode'];
		$res_array['countryId']=is_null($fetchRes['countryId'])?'':$fetchRes['countryId'];
		$countryName =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
		$res_array['countryName'] =is_null($countryName)?'':$countryName;
		$res_array['stateId']=is_null($fetchRes['stateId'])?'':$fetchRes['stateId'];
		$stateName=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
		$res_array['stateName']=is_null($stateName)?'':$stateName;
		$res_array['cityId']=is_null($fetchRes['cityId'])?'':$fetchRes['cityId'];
		$cityName =getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
		$res_array['cityName']=is_null($cityName)?'':$cityName;
		$res_array['mobile']=$fetchRes['mobileNumber'];
		$res_array['secret']=$fetchRes['secret'];
		$res_array['gen']=$fetchRes['gender'];
		$res_array['member']=$fetchRes['member']=='0000-00-00 00:00:00' || $fetchRes['member']=="0000-00-00"?'':$fetchRes['member'];
		$res_array['ip']=$fetchRes['ipaddress'];
		$res_array['birthDate']=$fetchRes['birthDate']=='0000-00-00 00:00:00' || $fetchRes['birthDate']=="0000-00-00"?'':$fetchRes['birthDate'];
		$res_array['countryCode']=$fetchRes['code'];
		$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
		$res_array['deviceToken']=$fetchRes['deviceToken']=='(null)'?'':$fetchRes['deviceToken'];	    
	    $status = true;
		$res_array['status'] = $status;
		$res_array['message'] = 'success';
		echo json_encode($res_array);
}
/*set reminder api*/
function reminderOperation_api($request_data){

	global $db;
	extract($request_data);
	$objpost = new stdClass();
	$table = 'tbl_reminders';
	$objpost->reminder_title = !empty($reminder_title)?$reminder_title:'';
	$objpost->reminder_date = date('Y-m-d',strtotime($reminder_date));
	$objpost->ipaddress = get_ip_address();
		if($operation =='setReminder' && !empty($objpost->reminder_title) && !empty($objpost->reminder_date)) {
			$objpost->userId = $userId;
			$db->insert($table, (array)$objpost);
			$status = true;
			$res_array['status'] = $status;
			$res_array['message'] = 'Reminder added successfully';
			echo json_encode($res_array);
			
		}else if($operation =='editReminder' && !empty($id) && is_numeric($id) && !empty($objpost->reminder_title) && !empty($objpost->reminder_date)) {
			
			$db->update($table, (array)$objpost, array('id'=>$id));
			$status = true;
			$res_array['status'] = $status;
			$res_array['message'] = 'Reminder updated successfully';
			echo json_encode($res_array);
		
		}else if($operation =='deleteReminder' && !empty($id) && is_numeric($id) ) {

			$db->delete($table,array('id'=>$id));

			$status = true;
			$res_array['status'] = $status;
			$res_array['message'] = 'Reminder deleted successfully';
			echo json_encode($res_array);
		
	} else {
		$status = false;
		$res_array['status'] = $status;
		$res_array['message'] = 'Fill all values';
		echo json_encode($res_array);
	}
	
}
/*get reminder operation*/
function reminderList_api($request_data){
	global $db;
	extract($request_data);
	$totRow = getTotalRows('tbl_reminders',array("userId"=>$userId),'id');
	if($totRow>0){
		$i=0;
		$list = array();
		$sel = $db->pdoQuery("select * from tbl_reminders where userId=$userId order by reminder_date desc")->results();
		foreach ($sel as $key => $value) {
			$list[$i]['id'] = $value['id'];
			$list[$i]['reminder_title'] =$value['reminder_title'];
			$list[$i]['reminder_date'] = date('d-m-Y',strtotime($value['reminder_date']));
			$i++;
		}

		$res_array['reminderList'] = $list;	
		$status = true;
		$res_array['status'] = $status;
		$res_array['message'] = 'success';
		echo json_encode($res_array);	

	}else{
		$status = false;
		$res_array['status'] = $status;
		$res_array['message'] = 'Details not available';
		echo json_encode($res_array);
	}	
}
/*get product deals with search & filters*/
function getProductDeals_api($request_data,$curCode){
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	extract($request_data);
	$start=0;
	$page = isset($page)?$page:1;
	$limit = isset($limit)?($limit*$page):$page*8;
	$whereCon = "1=1";
	//For filters
	if(isset($searchText) && $searchText != ''){
		$whereCon .= " AND d.productName LIKE '%".$searchText."%'";	
	}	

	//For category
	if(isset($cateId) && $cateId != ''){
		$whereCon .=" AND d.categoryId = ".$cateId."";
	}

	//For sub category
	if(isset($subCateId) && $subCateId != ''){
		$whereCon .=" AND d.subcategoryId = ".$subCateId."";
	}
	$query = "SELECT d.*,di.name
			  FROM tbl_product_deals as d 
			  LEFT JOIN tbl_categories as c ON (c.id = d.categoryId)
			  LEFT JOIN tbl_subcategory as sc ON (sc.id = d.subcategoryId)
			  LEFT JOIN tbl_product_image as di ON (di.productId = d.id)
			  WHERE $whereCon AND d.isActive = 'y' AND productType = 'a' AND d.quantity > 0
			  group by d.id 
			  order by d.id desc";
	
		$result = $db->pdoQuery($query." LIMIT ".$start." , ".$limit."")->results();
		$totalRow = $db->pdoQuery($query)->affectedRows();
		$i=0;
		$deal_detail = array();
		$res_array = array();
		if($totalRow>0){
		foreach ($result as $value) {
			if($value['isDiscount'] == 'y'){
				$dealPrice = convertCurrency($currencyId,$value['discountPrice']);
				$actualPrice = convertCurrency($currencyId,$value['actualPrice']);
				$actualPrice =number_format($actualPrice,2);
				$discountPercentage = ( $value['discountPercentage']. '% off');
				
			}else{
				$dealPrice = convertCurrency($currencyId,$value['actualPrice']);
				$actualPrice = $discountPercentage = '';
				
			}
			$deal_detail[$i]['dealId'] = $value['id'];
			$deal_detail[$i]['productName'] = $value['productName'];
			$deal_detail[$i]['productImage'] = checkImage('product/'.$value['id'].'/'.$value['name']);
			$deal_detail[$i]['dealPrice'] = number_format($dealPrice,2);
			$deal_detail[$i]['actualPrice'] = $actualPrice;
			$deal_detail[$i]['discountPercentage'] = $discountPercentage;
			
			$i++;
		}
			$status = true;
			$res_array['total_records']=$totalRow;
			$narecords =(intval($totalRow) - intval($limit));
			$res_array['next_available_records']= $narecords >0?$narecords:0;
			$res_array['status'] = $status;
			$res_array['message'] = "success";
			$res_array['currencyCode']=$curCode;
			$res_array['currencySign'] = $currencySign;
			$res_array['dealDetail'] = $deal_detail;
			echo json_encode($res_array);

		}else{
			$status = false;
			$res_array['status'] = $status;
			$res_array['message'] = "No any product deals found.";	
			echo json_encode($res_array);

		}	
}
/*get product details*/
function getProductDetail_api($request_data){
	global $db,$currencySign,$currencyId,$currencyCode;
	extract($request_data);
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$currencyCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	$exist = getTableValue('tbl_product_deals', 'productName', array('id'=>$id));
	if(!empty($exist)) {
	$query = "select p.*,c.categoryName,s.subcategoryName FROM tbl_product_deals as p 
				  LEFT JOIN tbl_categories as c ON (p.categoryId = c.id)
				  LEFT JOIN tbl_subcategory as s ON (p.subcategoryId = s.id)
				  WHERE p.id = ".$id." and p.isActive = 'y' and productType = 'a'";

		$dealData = $db->pdoQuery($query)->result();

		if($dealData['isDiscount'] == 'y'){
			$dealPrice = convertCurrency($currencyId,$dealData['discountPrice']);
			$actualPrice = convertCurrency($currencyId,$dealData['actualPrice']);
			$actualPrice = number_format($actualPrice,2);
			$discountPercentage = ( $dealData['discountPercentage']. '% off');
		}else{
			$dealPrice = convertCurrency($currencyId,$dealData['actualPrice']);
			$actualPrice = '';
			$discountPercentage = '';
		}
		/*product images start*/
		$selImage = $db->select('tbl_product_image','name',array("productId"=>$id));
		$image = $selImage->results();
		$totImage = $selImage->affectedRows();
		$i=0;
		$images = array();
		if($totImage>0){
			foreach ($image as $key => $value) {
				$images[$i]['imageUrl'] = checkImage('product/'.$value['productId'].'/'.$value['name']);
				$i++;
			}
		}else{
			$images[0]['imageUrl'] = SITE_UPD.'no_image_thumb.png';
		}	
		/*product images ends*/

		$detail['productId'] = $id;
		$detail['productName'] = $dealData['productName'];
		$detail['productDescription'] = $dealData['productDescription'];
		$detail['categoryName'] = $dealData['categoryName'];
		$detail['subcategoryName'] = $dealData['subcategoryName'];
		$detail['createdDate'] = date('Y-m-d',strtotime($dealData['createdDate']));
		$detail['weight'] = $dealData['weight'] . ' Kg';
		$detail['actualPrice'] = $actualPrice;
		$detail['dealPrice'] = number_format($dealPrice,2);
		$detail['discountPercentage'] = $discountPercentage;
		$detail['quantity'] = $dealData['quantity'];
		$detail['description'] = addslashes($dealData['productDescription']);
		$detail['productImages'] = $images;
		$detail['link'] = "http://localhost/wellness/product/".$id;
		$detail['currencySign'] = $currencySign;
		$detail['currencyCode'] = $currencyCode;
		$res_array['productDetail'] = $detail;
		$status = true;
		$res_array['status'] = $status;
		$res_array['message'] = "success";
		echo json_encode($res_array);

	}else{
		$status = false;
		$res_array['status'] = $status;
		$res_array['message'] = "Detail not found";	
		echo json_encode($res_array);
	}	
}
function similarDeals($request_data,$id){
	global $db,$currencyId,$currencyCode,$currencySign;
	extract($request_data);
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$currencyCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	$cateId = getTableValue('tbl_product_deals','categoryId',array("id"=>$id));
	$cateId =$cateId==''?'0':$cateId;
	$query = "SELECT d.*,di.name
			  FROM tbl_product_deals as d 
			  LEFT JOIN tbl_categories as c ON (c.id = d.categoryId)
			  LEFT JOIN tbl_subcategory as sc ON (sc.id = d.subcategoryId)
			  LEFT JOIN tbl_product_image as di ON (di.productId = d.id)
			  WHERE d.categoryId = ".$cateId." AND d.isActive = 'y' AND productType = 'a' AND d.id NOT IN(".$id.") AND d.quantity > 0
			  group by di.productId 
			  order by d.id desc
			  LIMIT 8";

		$result = $db->pdoQuery($query);
		$fetchRes = $result->results();
		$totalRow = $result->affectedRows();
		$actualPrice='';
		if($totalRow > 0){
			$i=0;
			foreach ($fetchRes as $value) {
				if($value['isDiscount'] == 'y'){
					$dealPrice = convertCurrency($currencyId,$value['discountPrice']);
					$actualPrice = convertCurrency($currencyId,$value['actualPrice']);
					$actualPrice = number_format($actualPrice,2);
					$discountPercentage = ( $value['discountPercentage']. '% off');
					
				}else{
					$dealPrice = convertCurrency($currencyId,$value['actualPrice']);
					$actualPrice = $discountPercentage = '';
				}
				$similar[$i]['productId'] = $value['id'];
				$similar[$i]['productName'] = $value['productName'];
				$similar[$i]['productImage'] = checkImage('product/'.$value['id'].'/'.$value['name']);
				$similar[$i]['dealPrice'] = number_format($dealPrice,2);
				$similar[$i]['actualPrice'] =$actualPrice==NULL?'': number_format($actualPrice,2);
				$similar[$i]['discountPercentage'] = $discountPercentage;
				$i++;

			}
		$res_array['similarProductDeals'] = $similar;
		$res_array['currencySign'] = $currencySign;
		$res_array['currencyCode'] = $currencyCode;
		$status = true;
		$res_array['status'] = $status;
		$res_array['message'] = "success";
		echo json_encode($res_array);

	}else{
		$status = false;
		$res_array['status'] = $status;
		$res_array['message'] = "Detail not found";	
		echo json_encode($res_array);
	}
}
function customOrderList_api($request_data,$curCode){
	global $db,$currencySign,$currencyId;
	extract($request_data);
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	$page = isset($request_data['page'])?$request_data['page']:1;
	$limit = isset($request_data['limit'])?$request_data['limit']:8;
	$start=0;
	$limit =$page * $limit;
	$whereCon = '1=1';
		//For filters
	if(isset($searchText) && $searchText != ''){
		$whereCon .=" AND productName LIKE '%".$searchText."%'";	
	}
	//For category
	if(isset($fromDate) && $fromDate != '' && isset($toDate) && $toDate != ''){
		$whereCon .=" AND (DATE(createdDate) BETWEEN '".date('Y-m-d',strtotime($fromDate))."' AND '".date('Y-m-d',strtotime($toDate))."') ";
	}
	//For sub category
	if(isset($status) && $status != ''){
		$whereCon .=" AND order_status = '".$status."'";
	}
	//For total row count
	$queryCount = "SELECT id FROM tbl_custom_orders
			  WHERE $whereCon AND userId = ".$userId." AND id_delete = 'n'
			  order by id desc";
	$resultCount = $db->pdoQuery($queryCount);
	$totalRowCount = $resultCount->affectedRows();
	$query = "SELECT id,paymentStatus,productName,orderId,productPrice,quantity,order_status,paymentStatus,createdDate
			  FROM tbl_custom_orders 
			  WHERE $whereCon AND userId = ".$userId." AND id_delete = 'n'
			  order by id desc
			  LIMIT ".$start." , ".$limit."";
	$result = $db->pdoQuery($query);
	$fetchRes = $result->results();
	if($totalRowCount>0){
		$i=0;
		foreach ($fetchRes as $key => $value) {
			$price = convertCurrency($currencyId,$value['productPrice']*$value['quantity']);
		if($value['order_status'] == 'a'){
			$status = 'Accepted';
		}else if($value['order_status'] == 'r'){
			$status = 'Rejected';
		}else if($value['order_status'] == 'p'){
			$status = 'Pending';
		}else
		{
			$status='Pending';
		}
		//For pay button
		$orderList[$i]['isPayable'] = false;
		if($value['order_status'] == 'a'){
			if($value['paymentStatus'] == 'n'){
				$orderList[$i]['isPayable'] = true;
			}else
			{
				$orderList[$i]['isPayable'] = false;
			}
		}
		$orderList[$i]['paymentStatus'] = $value['paymentStatus'];
		$orderList[$i]['orderId'] = $value['orderId'];
		$orderList[$i]['orderDate'] = date('m-d-Y',strtotime($value['createdDate']));
		$orderList[$i]['productName'] = $value['productName'];
		$orderList[$i]['price'] = number_format($price,2);
		$orderList[$i]['orderStatus'] = $status;
		$orderList[$i]['id'] = $value['id'];
		$i++;
	}	
		$res_array['currencySign'] = $currencySign;
		$res_array['currencyCode'] = $curCode;
		$res_array['total_records']=$totalRowCount;
		$narecords =(intval($totalRowCount) - intval($limit));
		$res_array['next_available_records']= $narecords >0?$narecords:0;
		$res_array['customOrders'] = $orderList;
		$res_array['status'] = true;
		$res_array['message'] = "success";
		echo json_encode($res_array);
	
	}else{
	$status = false;
	$res_array['status'] = $status;
	$res_array['message'] = "No any record found.";	
	echo json_encode($res_array);
	}
}
/*get custom order detail based on order id*/
function customOrderDetail_api($userId,$id,$curCode){
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));

	$query = "SELECT * FROM tbl_custom_orders where id = ".$id."";
		$orders = $db->pdoQuery($query)->result();
	$shippingAddress = array();	
	$shipping='';
	$deliveryOption='';
	$address='';
	$addressTitle='';
	if($orders['paymentStatus'] == 'y'){
	$deliveryOption = $orders['deliveryOption'] == 'p'?'Pick Point':'Door To Door Delivery';
		$shipping = (($orders['deliveryStatus'] == 's')?'Shipped':(($orders['deliveryStatus'] == 'd')?'Delivered':(($orders['deliveryStatus'] == 'p')?'Pending':'Returned')));
		$address = getDeliveryAddress_api($userId,$orders['deliveryOption'],$orders['pick_point']);
		if($orders['deliveryOption'] == 'p'){
			$addressTitle = 'Pick point address';
		}else if($orders['deliveryOption'] == 'd'){
			$addressTitle = 'Delivery address';
		}
		
	}
		$shippingAddress['deliveryOption'] = $deliveryOption;
		$shippingAddress['addressTitle'] =  $addressTitle;
		$shippingAddress['address'] = strip_tags($address);
		$shippingAddress['shippingStatus'] = $shipping;
		//For return button
	$isReturnable = false;
	if($orders['deliveryStatus'] != 'r' && $orders['paymentStatus'] == 'y' && $orders['deliveryStatus']=='d'){
		$isReturnable = true;
	}else
	{
		$isReturnable = false;
	}
	//For delivery days
	$deliveryDays = '';
	if($orders['deliveryOption'] != 'n'){
		$deliveryDays = getDeliveryDays($orders['deliveryOption'],$userId,$orders['pick_point']);
	}

	//For pricing
	$price = convertCurrency($currencyId,$orders['productPrice']);
	$totalPrice = convertCurrency($currencyId,$orders['productPrice']*$orders['quantity']);
	$dutiesAmount = convertCurrency($currencyId,$orders['dutiesAmount']);
	$adminCharge = convertCurrency($currencyId,$orders['adminCharge']);
	$shippingAmount = convertCurrency($currencyId,$orders['shippingAmount']);
	$discountAmount = convertCurrency($currencyId,$orders['discountAmount']);

	$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;

	$productDetails=array();
	$amountdetails =array();
	
	$productDetails['id'] = $orders['id'];
	$productDetails['orderId'] = $orders['orderId'];
	$productDetails['productName'] = $orders['productName'];
	//$productDetails['productNumber'] = $orders['productNumber'];
	$productDetails['productUrl'] = $orders['productUrl'];
	$productDetails['price'] = number_format($price,2);
	$productDetails['quantity'] = $orders['quantity'];
	$productDetails['size'] = $orders['size'];
	$productDetails['color'] = $orders['color'];
	$productDetails['orderDate'] = date('m-d-Y',strtotime($orders['createdDate']));
	$orderStatus =$orders['order_status'];
	if($orderStatus=='a'){
		$orderStatus='Accepted';
	}else if($orderStatus=='r')
	{
		$orderStatus='Rejected';
	}else if($orderStatus=='p')
	{
		$orderStatus='Pending';
	}else
	{
		$orderStatus='Pending';
	}
	$productDetails['orderstatus'] = $orderStatus;
	$productDetails['paymentStatus'] = $orders['paymentStatus'];
	$amountdetails['actualPrice']=number_format($totalPrice,2);
	$amountdetails['orderprice']=number_format($totalPrice-$discountAmount);
	$amountdetails['dutiesAmount'] = number_format($dutiesAmount,2);
	$amountdetails['adminCharges'] = number_format($adminCharge,2);
	$amountdetails['shippingCharges'] = number_format($shippingAmount,2);
	$amountdetails['discountAmount'] = number_format($discountAmount,2);
	$amountdetails['totalAmount'] = number_format($totalAmount,2);
	
	$shippingAddress['isReturnable'] = $isReturnable;
	$shippingAddress['deliveryDays'] = $deliveryDays;
		
	$res_array['productdetails'] = $productDetails;
	$res_array['amountdetails'] = $amountdetails;
	$res_array['deliverydetails'] = $shippingAddress;
	$res_array['currencySign']=$currencySign;
	$res_array['currencyCode']=$curCode;
	$status = true;
	$res_array['status'] = $status;
	$res_array['message'] = "success";	
	echo json_encode($res_array);
}	
/*get all notification list api
	by:NTC 14102016
*/
function notification_list_api($page,$limit,$userId,$curCode)
{
		global $db,$currencySign;
		$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
		$currencySign =empty($currencySignn)?$currencySign:$currencySignn;
		$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
		$res_rows=array();
		$objpost = new stdClass();
		$objpost->userId =isset($userId)?$userId:0;
		$objpost->page=$page;
		$objpost->limit =$limit;
		if($objpost->userId >0)
		{	
			//scroll to page and display more limits data
			$start  = 0;
			$limit =$limit * $objpost->page;
			$i=0;
			//For total row count
				$query1 = "SELECT id from tbl_user_notifications WHERE toId = ".$objpost->userId." order by createdDate desc";
				$result1 = $db->pdoQuery($query1);
				$totalRow1 = $result1->affectedRows();
			if($page>0)
			{
				$query = "SELECT * from tbl_user_notifications
					      WHERE toId = ".$objpost->userId."
						  order by createdDate desc LIMIT ".$start." , ".$limit."";
			}else
			{
				$query = "SELECT id from tbl_user_notifications
					      WHERE toId = ".$objpost->userId."
						  order by createdDate desc";
			}
			$result = $db->pdoQuery($query);
		 	$fetchRes = $result->results();
		 	$totalRow = $result->affectedRows();
			 
			 if($totalRow>0)
			 {
			 	foreach ($fetchRes as $value) 
				 	{
				 		//notification data
						$notificationType = $value['notificationType'];
						$toId = isset($value['toId'])?$value['toId']:0;
						$refId = isset($value['refId'])?$value['refId']:0;
						$amount = isset($value['amount'])?convertCurrency($currencyId,$value['amount']):0;
						$createdDate = isset($value['createdDate'])?$value['createdDate']:'1900-12-31 00:00:00';

						$productName = getTableValue('tbl_product_deals',"productName",array("id"=>$refId));
						$productName = isset($productName)?$productName:'';
						$coupon_code = getTableValue('tbl_coupons',"coupon_code",array("id"=>$refId));
						$coupon_code =isset($coupon_code)?$coupon_code:'';
						$customProductName = getTableValue('tbl_custom_orders',"productName",array("id"=>$refId));
						$customProductName = isset($customProductName)?$customProductName:'';
						$status = getTableValue('tbl_custom_orders',"order_status",array("id"=>$refId));

						$orderStatus = $status=='a'?'accepted':'rejected';
						$reminder_title = getTableValue('tbl_reminders',"reminder_title",array("id"=>$refId));
						$reminder_title=isset($reminder_title)?$reminder_title:'';

						$notification = '';
						
						switch ($notificationType) 
						{
							
							case '1':
							$notification = 'Product return refund amount '.$currencySign.$amount.' is received from admin for '.$productName;
								break;

							case '2':
								$notification = 'Redeem amount '.$currencySign.$amount.' is funded';
								break;

							case '3':
								$notification = 'New product deal as '.$productName.' is posted by admin';
								break;

							case '4':
								$notification = $currencySign.$amount.' is added in your wallet';
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
						$res_array['total_records']=$totalRow1;
						$narecords =(intval($totalRow1) - intval($limit));
						$res_array['next_available_records']= $narecords >0?$narecords:0;
						$res_rows[$i]['notification'] =$notification;
						$res_rows[$i]['productName']=$productName;
						$res_rows[$i]['coupon_code']=$coupon_code;
						$res_rows[$i]['customProductName']=$customProductName;
						$res_rows[$i]['orderStatus']=$orderStatus;
						$res_rows[$i]['reminder_title']=$reminder_title;
						$res_rows[$i]['amount']=$amount;
						$res_rows[$i]['createdDate']=$createdDate;
						$i++;
				}
				$res_array['notifications']=$res_rows;
			 	$status=true;
			 	$res_array['currencySign']=$currencySign;
			 	$res_array['currencyCode']=$curCode;
			 	$res_array['status']=$status;
			 	$res_array['message']='success';
		 		return  json_encode($res_array);
			}else{
				$status =false;
				$res_array['status']=$status;
				$res_array['message']="notification not available.";
				return  json_encode($res_array);
		 	}
		}else
		 {
		 	$status =false;
		 	$res_array['status']=$status;
		 	$res_array['message']="fill all field.";
		 	return  json_encode($res_array);
		}
}
/*get wallet balance api
	by:NTC 14102016
*/
function wallet_balance_api($userId,$curCode){
	global $db,$currencySign,$currencyId;
	if($userId>0){
		$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
		$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
		$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
		
		$amount = $db->select("tbl_users",array("creditAmount","pendingAmount","redeemAmount"),array("id"=>$userId))->result();

		$creditAmount = convertCurrency($currencyId,$amount['creditAmount']);
		$redeemAmount = convertCurrency($currencyId,$amount['redeemAmount']);
		$pendingAmount = convertCurrency($currencyId,$amount['pendingAmount']);	
		$status=true;
		$message ="success";
		$res_array['currencyId']=$currencyId;
		$res_array['currencySign']=$currencySign;
		$res_array['currencyCode'] =$curCode;
		$res_array['creditAmount']=number_format($creditAmount,2);
		$res_array['redeemAmount']=number_format($redeemAmount,2);
		$res_array['pendingAmount']=number_format($pendingAmount,2);
		
	}else
	{
		$status =false;
		$message ="Invalid user id.";
	}
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*get redeem request history api
	by:NTC 15102016
*/
function wallet_redeemRequestList_api($userId,$curCode,$page,$limit)
{
	global $db,$currencySign,$currencyId;
	$curCode='USD';
	if($userId>0){
		$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
		$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
		$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
			//scroll to page and display more all limits data
			$start  = 0;
			$limit =$limit * $page;
			//For total row count
			$queryCount = "SELECT id from tbl_redeem_request
				      WHERE userId = ".$userId."
					  order by id desc";

			$resultCount = $db->pdoQuery($queryCount);
	 		$totalRowCount = $resultCount->affectedRows();
	 		if($totalRowCount>0){
				$query = "SELECT id,amount,status,createdDate from tbl_redeem_request
					      WHERE userId = ".$userId."
						  order by id desc
						  LIMIT ".$start." , ".$limit."";

				$result = $db->pdoQuery($query);
		 		$fetchRes = $result->results();
		 		$totalRow = $result->affectedRows();
		 		$res_rows=array();
		 		$i = 0;
				foreach ($fetchRes as $key => $value) {

					$status =isset($value["status"])?$value["status"]:'p';
					if($value["status"] == 'p'){
						$redeemstatus = 'Pending';
					}else if($value["status"] == 'f'){
						$redeemstatus = 'Funded';
					}else if($value["status"] == 'r'){
						$redeemstatus = 'Rejected';
					}
					$res_rows[$i]['redeemstatus']=$redeemstatus;
					$amount=isset($value['amount'])? convertCurrency($currencyId,$value['amount']):0;
					$res_rows[$i]['amount']=number_format($amount,2);
					$res_rows[$i]['createdDate']=isset($value['createdDate'])?$value['createdDate']:'1900-12-24 00:00:00';
					
					$i++;
				}
				$balance = getTableValue('tbl_users','creditAmount',array('id'=>$userId));
				$res_array['availableamount']=round($balance,2);
				$res_array['total_records']=$totalRowCount;
				$narecords =(intval($totalRowCount) - intval($limit));
				$res_array['next_available_records']= $narecords >0?$narecords:0;
				$res_array['history']=$res_rows;
				$res_array['currencyCode']=$curCode;
				$res_array['currencyId']=intval($currencyId);
				$res_array['currencySign']=$currencySign;

				$status=true;
				$message ="success";
			}else
			{	$balance = getTableValue('tbl_users','creditAmount',array('id'=>$userId));
				
				$res_array['availableamount']=$balance;
				$status=false;
				$message ="No records available.";
			}
	}else{
		$status =false;
		$message ="Invalid user id.";
	}	

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*get redeem request api
	by:NTC 15102016
*/
function wallet_redeemRequest_api($userId,$amount,$curCode='USD')
{
	//redeem request  only in doller
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0){
		
			if($amount>0)
			{
				$amount =round($amount,2);
				$data = array();
		    	$data['userId'] = $userId;
		    	$data['amount'] = strval($amount);
		    	$data['createdDate'] = date('Y-m-d H:i:s');
		    	$oldCredit = getTableValue('tbl_users','creditAmount',array("id"=>$userId));
			    if($oldCredit<convertCurrency(1,$amount)){
			    		$res_array['status']=false;
						$res_array['message']='You have not sufficient balance, please enter less amount.';
						return json_encode($res_array);
						exit;
			    	}
		    	$lastInsertId=$db->insert('tbl_redeem_request',$data)->lastInsertId();
		    	if($lastInsertId>0){
			    	//update pending and credit amount
			    	$oldRedeem = getTableValue('tbl_users','redeemAmount',array("id"=>$userId));

			    	$oldCredit = getTableValue('tbl_users','creditAmount',array("id"=>$userId));
			    	
			    	$finalRedeem = strval($oldRedeem + $amount);
			    	$finalCredit = strval($oldCredit - $amount);
			    	$db->update('tbl_users',array("redeemAmount"=>$finalRedeem,"creditAmount"=>$finalCredit),array("id"=>$userId));

			    	//Send mail to admin
			    	$username =getTableValue('tbl_users','firstName',array('id'=>$userId));
			    	
					//Send mail to admin
			    	$contArray = array(
						"USER_NM"=>$username,
						"AMOUNT"=>$currencySign.$amount
					);
					sendMail(ADMIN_EMAIL,"redeem_request",$contArray);
			    	$res_array['isertedId']=$lastInsertId;

			    	$status=true;
					$message ="Request sent successfully.";
				}else
				{
					$status=false;
					$message ="Opps.. something missing!.";
				}
			}else
			{
				$status=false;
				$message ="please enter valid amount.";
			}
	}else{
		$status =false;
		$message ="Invalid user id.";
	}	
	
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/*get deposit wallet amount api
	by:NTC 15102016
*/
function wallet_deposit_api($request_data)
{
	//deposit  amount only doller
	global $db,$currencySign,$currencyId;

	$userId = isset($request_data['userId'])?$request_data['userId']:0;
	$curCode ='USD';
	$transaction_id =isset($request_data['transactionId'])?$request_data['transactionId']:'';
	//$transaction_type =isset($request_data['transactionType'])?$request_data['transactionType'];
	$paymet_gateway ='p';//isset($request_data['paymentGateway'])?$request_data['paymentGateway']:'p';
	$amount =isset($request_data['paidAmount'])?$request_data['paidAmount']:'';

	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0){
		if($transaction_id!=''){
				$ch = curl_init();
				$clientId = PAYPAL_CLIENT_ID; //client Id
				$secret = PAYPAL_CLIENT_SECRET;//client secrete key
				curl_setopt($ch, CURLOPT_URL, PAYPAL_APP_URL_OAUTH2);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
				$result = curl_exec($ch);
				$accessToken = null;
				if (empty($result))
				{
					$res_array['status']=false;
					$res_array['message']='Invalid access token';
					return $res_array;
					exit;
				}else {
					$json = json_decode($result);
					$accessToken = $json->access_token;
				}
				curl_close($ch);
				
				$curl = curl_init(PAYPAL_APP_URL_PAYMENT.$transaction_id."");
				curl_setopt($curl, CURLOPT_POST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array(
					'Authorization: Bearer ' . $accessToken,
					'Accept: application/json',
					'Content-Type: application/json'
				));
			
				 $response = curl_exec($curl);
			        //print_r($response);exit;
				$result = json_decode($response,true);
				extract($result);
				$transaction_id	= $transactions[0]['related_resources'][0]['sale']['id'];
				$paypalAmount 	= (float)$transactions[0]['amount']['total'];
				$currency 	= $transactions[0]['amount']['currency'];
				$paymentStatus 	= $transactions[0]['related_resources'][0]['sale']['state'];
				$date= date('Y-m-d H:i:s');
				$res_array['paymentStatus']=$paymentStatus;
				if($paypalAmount <=0 && $paymentStatus != 'completed')
				{
					$status=false;
					$message="Your payment not completed.";
					$res_array['status']=$status;
					$res_array['message']=$message;

					return json_encode($res_array);
					exit();
				}
			if($paypalAmount>0)
			{
				$oldCredit = getTableValue('tbl_users','creditAmount',array("id"=>$userId));
				//$amount =convertCurrency($currencyId,$amount);
				$finalamount = strval($paypalAmount+$oldCredit);
				$affectedRows=$db->update('tbl_users',array("creditAmount"=>$finalamount),array('id'=>$userId))->affectedRows();
				if($affectedRows>0)
				{
					//For notification
					$users = $db->pdoQuery("Select id,firstName,email from tbl_users where id=".$userId."")->result();			
					//For dashboard notifications
					$db->insert("tbl_user_notifications",array("notificationType"=>4,"fromId"=>'',"toId"=>$users['id'],"amount"=>strval($paypalAmount),"createdDate"=>date("Y-m-d H:i:s")));

					//For email to user
					$contArray = array(
						"USER_NM"=>$users['firstName'],
						"AMOUNT"=>$currencySign.$paypalAmount
					);
					sendMail(base64_decode($users['email']),"amount_added",$contArray);

					//For transaction history
					$txn = array();
					$txn['user_id'] = $userId;
					$txn['transaction_id'] = $transaction_id;
					$txn['transaction_type'] = 'd';
					$txn['payment_gateway'] =$paymet_gateway;
					$txn['paid_amount'] = strval($paypalAmount);
					$db->insert('tbl_payment_history',$txn);
					
					$res_array['balance']=$finalamount;
				    $status=true;
					$message ="Payment of $".$finalamount." deposit successfully.";
				}else
				{
					$status=false;
					$message ="Opps..some issue occured.";
				}
				
			}else
			{
				$status=false;
				$message ="please enter valid amount.";
			}
		}else{
				$status=false;
				$message ="transaction id can't be blank.";
			}
	}else{
		$status =false;
		$message ="Invalid user id.";
	}	
	
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/*send request referral api
	by:NTC 17102016
*/
function referral_request_api($userId,$email)
{
	global $db;

	if($userId>0){
		if($email!=''){
			$userEmail = getTableValue('tbl_users','email',array('id'=>$userId));
			$email = explode(',',$email);
	    	$i = 0;
	    	foreach ($email as $key => $value) {
	    		$alreadyExist = getTotalRows('tbl_referral_users',array('email'=>strtolower($value)),'id');

	            if(base64_decode($userEmail) == $value){
	            	$status =false;
	                $message ='You can\'t refer yourself.';
	            }else
	            {
		    		if($alreadyExist > 0){
		    			$status =false;
		                $message =$value.' is already invited.';	
		    			
		    		}else{
		    			if(filter_var($value,FILTER_VALIDATE_EMAIL))
						{
			    			$data = array();
			    			$data['userId'] = $userId;
			    			$data['email'] = strtolower($value);
			    			$db->insert('tbl_referral_users',$data);
			    			//Email notification
	    					$link = '<a href="'.SITE_URL.'referral/'.base64_encode($userId).'">Click Here</a>';
	    					$userName =getTableValue('tbl_users','firstName',array('id'=>$userId));
			    			$contArray = array(
								"USER_NM"=>$userName,
								"REFERRAL_LINK"=>$link
							);
							sendMail($value,"referral_mail",$contArray);
							$status=true;
			    			$message ='User(s) invited successfully.';
			    		}else
			    		{
			    			$status=false;
			    			$message ='Please enter valid emails with comma seperated.';
			    		}
					}
				}	
	    		$i++;
	    	}
		}else
	    {
	    	$status=false;
	    	$message ='Please enter valid email.';
	    }
    }else{
		$status =false;
		$message ="Invalid user id.";
	}	
	
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/* get statistical data from wallet
	by:NTC 17102016
*/
function statistics_api($userId,$curCode)
{
	global $db,$currencySign,$currencyId,$currencyCode;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	
	if($userId>0){

		$totalReferredUser = getTotalRows('tbl_referral_users',array('userId'=>$userId),'id');
		$totalRegisteredUser = getTotalRows('tbl_referral_users',array('userId'=>$userId,'isRegister'=>'y'),'id');
		$totalPurchasedUser = getTotalRows('tbl_referral_users',array('userId'=>$userId,'isPurchase'=>'y'),'id');
		$referralAmount = getTableValue('tbl_users',"referralAmount",array('id'=>$userId));
		$referralAmount = isset($referralAmount)?$referralAmount:0;		
		$res_array['totalReferredUser'] =$totalReferredUser;
		$res_array['totalRegisteredUser']=$totalRegisteredUser;
		$res_array['totalPurchasedUser']=$totalPurchasedUser;
		$res_array['referralAmount']= number_format(convertCurrency($currencyId,$referralAmount),2);
		//Email notification
	    $link = SITE_URL.'referral/'.base64_encode($userId);
	    $res_array['referralLink']=$link;
		$res_array['currencySign']=$currencySign;
		$res_array['currencyCode']=$curCode;
		$status =true;
		$message ='success';

	}else{
		$status =false;
		$message ="Invalid user id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*get shopping cart information api
	by:NTC 18102016
*/
function cart_list_api($userId,$curCode)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('code'=>$curCode));

	if($userId>0){
		//create new order
		//Insert cart product in order table
		$cart = $db->pdoQuery("Select c.productId,c.quantity as cartQuantity,p.id,p.isDiscount,p.actualPrice,p.discountPrice from tbl_cart as c 
			INNER JOIN tbl_product_deals as p ON(p.id = c.productId)
			where c.userId = ".$userId."")->results();
			$data = array();
			
			foreach ($cart as $key => $value) 
			{
				if($value['isDiscount'] == 'y')
				{
					$productPrice = $value['discountPrice'];
				}else{
					$productPrice = $value['actualPrice'];
				}	
				$alreadyExist = getTableValue("tbl_orders","id",array("userId"=>$userId,"productId"=>$value['productId'],"paymentStatus"=>"n"));
				$data['userId'] = $userId;
				$data['orderId'] = 'ORDER'.time().strtoupper(genrateRandom(4));
				$data['productId'] = $value['productId'];
				$data['productPrice'] = strval($productPrice);
				$data['quantity'] = $value['cartQuantity'];
				$totalProductPrice = strval($productPrice * $value['cartQuantity']);
				
				$data['createdDate']=date('Y-m-d H:i:s');
			    if($alreadyExist > 0)
				{
					//For update shipping amount
					$deliveryOption = getTableValue('tbl_orders','deliveryOption',array('id'=>$alreadyExist));
						if($deliveryOption == 'd'){
							$countryId='';
							$productPrice = $data['productPrice']*$data['quantity'];
							$shippingAmount = getd2dShippingAmount_api($productPrice,$userId);
							$dutiesAmount = getDutiesAmount_api($totalProductPrice,$userId,$countryId);
							$adminCharge = getAdminCharge_api($totalProductPrice,$userId,$countryId);
							$data['dutiesAmount'] = strval($dutiesAmount);
							$data['adminCharge'] = strval($adminCharge);
							$data['shippingAmount'] = strval($shippingAmount);
						}else if($deliveryOption == 'p'){

							$pick_point = getTableValue('tbl_orders','pick_point',array('id'=>$alreadyExist));
							$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pick_point));
							$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pick_point));
							$productPrice = $data['productPrice']*$data['quantity'];
							$shippingAmount = getPickShippingAmount($productPrice,$stateId,$stateId);
							$dutiesAmount = getDutiesAmount_api($totalProductPrice,$userId,$countryId);
							$adminCharge = getAdminCharge_api($totalProductPrice,$userId,$countryId);
							$data['dutiesAmount'] = strval($dutiesAmount);
							$data['adminCharge'] = strval($adminCharge);
							$data['shippingAmount'] = strval($shippingAmount);
						}

						$db->update("tbl_orders",$data,array("id"=>$alreadyExist));
						
				}else{
						$db->Insert("tbl_orders",$data);	
				}
		}

		//end card item to new order

		$query = "SELECT c.id as cartId,o.id orderId,o.productId,o.productPrice,o.quantity,p.quantity as maxQuntity,p.id,p.productName,p.weight FROM tbl_orders as o 
				  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
				  LEFT JOIN tbl_cart as c ON(c.productId = o.productId)
				  where o.paymentStatus = 'n' AND c.userId = ".$userId." AND o.userId = ".$userId."";
		
		$result = $db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$totalRow =$result->affectedRows();
 		if($totalRow<=0){
			
			$status=false;
			$message="Cart empty.";
		
		}else{
			$items =array();
			$i=0;
			foreach($fetchRes as $value){
				$productPrice = number_format(convertCurrency($currencyId,isset($value['productPrice'])?$value['productPrice']:0),2);
				$totalAmount = number_format(convertCurrency($currencyId,(isset($value['productPrice'])?$value['productPrice']:0)*(isset($value['quantity'])?$value['quantity']:0)),2);
				$cartId=isset($value['cartId'])?$value['cartId']:0;
				$productName= isset($value['productName'])?$value['productName']:0;
				$weight =isset($value['weight'])?$value['weight']. ' Kg' :'NA';
				$quantity =isset($value['quantity'])?$value['quantity']:'NA';
				$maxQuantity= isset($value['maxQuntity'])?$value['maxQuntity']:'NA';

				$items[$i]['cartId']=$cartId;
				$items[$i]['productName']=$productName;
				$items[$i]['productId'] = $value['productId'];
				$items[$i]['quantity']=$quantity;
				$items[$i]['productPrice']=$productPrice;
				$items[$i]['totalAmount']=$totalAmount;
				$items[$i]['weight']=$weight;
				$items[$i]['maxQuntity']=$maxQuantity;
				$i++;

			}	

			$query = "SELECT o.*,p.productName,p.weight FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  where o.paymentStatus = 'n' AND o.userId = ".$userId."";
			$fetchRes = $db->pdoQuery($query)->results();
			$dutiesAmount=0;
			$totalPriced=0;
			$adminCharge=0;
			$shippingAmount=0;
			$discountAmount=0;
			foreach ($fetchRes as $key => $orders) {
				//For pricing in currency
				$price += convertCurrency($currencyId,$orders['productPrice']);
				 $totalPrice += convertCurrency($currencyId,$orders['productPrice'])*$orders['quantity'];
				 $adminCharge += convertCurrency($currencyId,$orders['adminCharge']);
				 $shippingAmount += convertCurrency($currencyId,$orders['shippingAmount']);
				 $discountAmount += convertCurrency($currencyId,$orders['discountAmount']);
				 $dutiesAmount+=convertCurrency($currencyId,$orders['dutiesAmount']);
				//end pricing in currency
				//for in doller
				$totalPriced += $orders['productPrice'] * $orders['quantity'];
				$dutiesAmountd += $orders['dutiesAmount'];
				$adminCharged += $orders['adminCharge'];
				$shippingAmountd += $orders['shippingAmount'];
				$discountAmountd += $orders['discountAmount'];
				//end in amountInDoller
			}
			$totalAmount = ($totalPrice + $dutiesAmount + $adminCharge + $shippingAmount) - $discountAmount;
			$amountInDoller =($totalPriced + $dutiesAmountd + $adminCharged + $shippingAmountd) - $discountAmountd;
	
			$status=true;
			$message='success';
			$res_array['cartItems']=$items;
			$res_array['totalPrice']=number_format($totalPrice,2);
			$res_array['dutiesAmount']=number_format($dutiesAmount,2);
			$res_array['adminCharge']=number_format($adminCharge,2);
			$res_array['shippingAmount']=number_format($shippingAmount,2);
			$res_array['discountAmount']=number_format($discountAmount,2);
			$res_array['totalAmount']=number_format($totalAmount,2);
			$res_array['amountInDoller']=round($amountInDoller,2);
			$res_array['currencyCode']=$curCode;
			$res_array['currencySign']=$currencySign;
		}

	}else{
		$status =false;
		$message ="Invalid user id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*get all pickup point information api
	by:NTC 19102016
*/
function pickup_points_list_api($userId){
	global $db;
	if($userId>0){
		$countryId =getTableValue('tbl_users','countryId',array('id'=>$userId));
		//if($countryId!='' || $countryId>0)
		//{
		/*if($countryId>0 && $countryId!='')
		{
				$query = $db->pdoQuery("select pp.countryId,pp.id,pp.stateId,s.stateName,c.countryName,pp.pointName,pp.pointAddress from tbl_pick_points as pp LEFT JOIN tbl_state as s ON(s.id = pp.stateId) LEFT JOIN tbl_country as c ON(c.id = pp.countryId) where pp.isActive = 'y' and pp.countryId=".$countryId." group by pp.stateId order by pp.id desc")->results();
		}else
		{*/
				$query = $db->pdoQuery("select pp.countryId,pp.id,pp.stateId,s.stateName,c.countryName,pp.pointName,pp.pointAddress from tbl_pick_points as pp 	LEFT JOIN tbl_state as s ON(s.id = pp.stateId) 	LEFT JOIN tbl_country as c ON(c.id = pp.countryId) where pp.isActive = 'y'  order by pp.id desc")->results();
		//}
			$i=0;
			$j=0;
			$itemsstate = array();
			$itemstatedata=array();
			foreach ($query as $key => $value) 
			{   
				/*if($countryId>0){

					$querystate = $db->pdoQuery("select pp.countryId,pp.id,pp.stateId,s.stateName,c.countryName,pp.pointName,pp.pointAddress from tbl_pick_points as pp LEFT JOIN tbl_state as s ON(s.id = pp.stateId) LEFT JOIN tbl_country as c ON(c.id = pp.countryId) where pp.isActive = 'y' and pp.countryId=".$countryId." and pp.stateId=".$value['stateId']." order by pp.id desc")->results();
				}else{
				*/		
					$querystate = $db->pdoQuery("select pp.countryId,pp.id,pp.stateId,s.stateName,c.countryName,pp.pointName,pp.pointAddress from tbl_pick_points as pp LEFT JOIN tbl_state as s ON(s.id = pp.stateId) LEFT JOIN tbl_country as c ON(c.id = pp.countryId) where pp.isActive = 'y' and  pp.stateId=".$value['stateId']." order by pp.id desc")->results();
				//}
				foreach ($querystate as $key => $valuestate) {
					$itemstatedata[$i]['pickupPointId']=$valuestate['id'];
					$itemstatedata[$i]['countryId']=$valuestate['countryId'];
					$itemstatedata[$i]['countryName']=$valuestate['countryName'];
					$itemstatedata[$i]['stateId']=$valuestate['stateId'];
					$itemstatedata[$i]['stateName']=$valuestate['stateName'];
					$itemstatedata[$i]['pointName']=$valuestate['pointName'];
					$itemstatedata[$i]['pointAddress']=$valuestate['pointAddress'];
					$i++;			
				}
				$itemsstate[$j]['stateId']=$value['stateId'];
				$itemsstate[$j]['stateName']=$value['stateName'];
				$itemsstate[$j]['pickupPoints']=$itemstatedata;
				$j++;
				$i=0;
					
			}
		$res_array['pickupPointList'] = count($itemsstate)>0?$itemsstate:'not available';
		$status=count($itemsstate)>0?true:false;
		$message='success';
		/*}else
		{
			$status=false;
			$message="User not set country in account setting please first complate fill your account setting.";
		}*/

	}else
	{
		$status=false;
		$message="Invalid user id.";
	}
		$res_array['status']=$status;
		$res_array['message']=$message;
		return json_encode($res_array);

}
/*get all state wise pickup point information api
	by:NTC 19102016
*/
function state_pickup_points_list_api($stateId){
	global $db;
	
		if($stateId>0)
		{
				$query = $db->pdoQuery("select pp.countryId,pp.id,pp.stateId,s.stateName,c.countryName,pp.pointName,pp.pointAddress from tbl_pick_points as pp 
				LEFT JOIN tbl_state as s ON(s.id = pp.stateId) 
				LEFT JOIN tbl_country as c ON(c.id = pp.countryId) where pp.isActive = 'y' and pp.stateId=".$stateId." order by pp.id desc")->results();
				$i=0;
				$items = array();
				foreach ($query as $key => $value) 
				{

					$items[$i]['pickupPointId']=$value['id'];
					$items[$i]['countryId']=$value['countryId'];
					$items[$i]['countryName']=$value['countryName'];
					$items[$i]['stateId']=$value['stateId'];
					$items[$i]['stateName']=$value['stateName'];
					$items[$i]['pointName']=$value['pointName'];
					$items[$i]['pointAddress']=$value['pointAddress'];
					$i++;	
				}	
				$res_array['pickupPoint'] =  count($items)>0?$items:'not available';
				$status=count($items)>0?true:false;
				$message='success';
		}else
		{
				$status =false;
				$message = "Invalid state id.";	
		}
		
	
		$res_array['status']=$status;
		$res_array['message']=$message;
		return json_encode($res_array);

}
/*get shopping cart delivery address information api
	& change shopping cart delivery address information
	by:NTC 19102016
*/
function cart_delivery_address_api($userId){
	global $db;
	if($userId>0 ){
		
		$deliveryTypeRS=$db->pdoQuery("select id,deliveryOption,pick_point from tbl_orders where paymentStatus='n' and userId=".$userId. " GROUP BY deliveryOption ")->results();
		$deliveryType =$deliveryTypeRS[0]['deliveryOption'];
		$pick_point = $deliveryTypeRS[0]['pick_point'];
		if($deliveryType=='d' || $deliveryType=='n' )
		{ 
			$countryId='';
			$fetchRes = $db->pdoQuery('Select u.stateId,u.countryId,u.cityId, u.salute,u.firstName,u.lastName, u.address,ct.cityName,co.countryName,s.stateName,u.code,u.mobileNumber from tbl_users as u 
                LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
                LEFT JOIN tbl_state as s ON(u.stateId = s.id)
                LEFT JOIN tbl_country as co ON(u.countryId = co.id)
                WHERE u.id = '.$userId.'')->result();
            if(!empty($fetchRes))
			{
				$res_array['salute']=isset($fetchRes['salute'])?$fetchRes['salute']:'';
				$res_array['firstName']=isset($fetchRes['firstName'])?$fetchRes['firstName']:'';
				$res_array['lastName']=isset($fetchRes['lastName'])?$fetchRes['lastName']:'';
				$res_array['countryName']=isset($fetchRes['countryName'])?$fetchRes['countryName']:'';
	            $res_array['state']= isset($fetchRes['stateName'])?$fetchRes['stateName']:'';
	            $res_array['city']=isset($fetchRes['cityName'])?$fetchRes['cityName']:'';
	            $res_array['address']=isset($fetchRes['address'])?$fetchRes['address']:'';
	            $res_array['countryCode']=  isset($fetchRes['code'])?$fetchRes['code']:'';
	            $res_array['cityId']=  isset($fetchRes['cityId'])?$fetchRes['cityId']:0;
	            $res_array['stateId']=  isset($fetchRes['stateId'])?$fetchRes['stateId']:0;
	            $res_array['countryId']=  isset($fetchRes['countryId'])?$fetchRes['countryId']:0;
	            $res_array['mobileNumber']=isset($fetchRes['mobileNumber'])?$fetchRes['mobileNumber']:'';
				$status= true;
				$message='success';
			}else{
				$status= false;
				$message='address not available for current user.';
			}	
		}else if($deliveryType=='p')
		{
			
			 $fetchRes = $db->pdoQuery('Select p.pointName,p.pointAddress,co.countryName,s.stateName from tbl_pick_points as p 
                LEFT JOIN tbl_state as s ON(p.stateId = s.id)
                LEFT JOIN tbl_country as co ON(p.countryId = co.id)
                WHERE p.id = '.$pick_point.'')->result();
            
            $countryCode= getTableValue('tbl_users','code',array('id'=>$userId));
            $mobile= getTableValue('tbl_users','mobileNumber',array('id'=>$userId));
            $salute= getTableValue('tbl_users','salute',array('id'=>$userId));
            $firstName= getTableValue('tbl_users','firstName',array('id'=>$userId));
            $lastName= getTableValue('tbl_users','lastName',array('id'=>$userId));

            $res_array['salute']=$salute;
            $res_array['firstName']=$firstName;
            $res_array['lastName']=$lastName;
            $res_array['pointName']=isset($fetchRes['pointName'])?$fetchRes['pointName']:'';
            $res_array['pointAddress']=isset($fetchRes['pointAddress'])?$fetchRes['pointAddress']:'';
	        $res_array['stateName']= isset($fetchRes['stateName'])?$fetchRes['stateName']:'';
	        $res_array['countryName']=isset($fetchRes['countryName'])?$fetchRes['countryName']:'';
	        $res_array['countryCode']=isset($countryCode)?$countryCode:'';
	        $res_array['mobileNumber']=isset($mobile)?$mobile:'';
	        $status=true;
	        $message="success";

		}else
		{
			$status= false;
			$message='invalid pick up point.';
		}

	}else{
		$status =false;
		$message ="Invalid user id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/*get shopping cart delivery address information api
	& change shopping cart delivery address information
	by:NTC 19102016
*/
function custom_cart_delivery_address_api($userId){
	global $db;
	if($userId>0 ){
		
		$deliveryTypeRS=$db->pdoQuery("select id,deliveryOption,pick_point from tbl_custom_orders where paymentStatus='n' and userId=".$userId. " GROUP BY deliveryOption ")->results();
		$deliveryType =$deliveryTypeRS[0]['deliveryOption'];
		$pick_point = $deliveryTypeRS[0]['pick_point'];

		if($deliveryType=='d' || $deliveryType=='n')
		{
			$fetchRes = $db->pdoQuery('Select u.cityId,u.stateId,u.countryId,u.salute,u.firstName,u.lastName, u.address,ct.cityName,co.countryName,s.stateName,u.code,u.mobileNumber from tbl_users as u 
                LEFT JOIN tbl_city as ct ON(u.cityId = ct.id)
                LEFT JOIN tbl_state as s ON(u.stateId = s.id)
                LEFT JOIN tbl_country as co ON(u.countryId = co.id)
                WHERE u.id = '.$userId.'')->result();
            if(!empty($fetchRes))
			{
				
				$res_array['salute']=isset($fetchRes['salute'])?$fetchRes['salute']:'';
				$res_array['firstName']=isset($fetchRes['firstName'])?$fetchRes['firstName']:'';
				$res_array['lastName']=isset($fetchRes['lastName'])?$fetchRes['lastName']:'';
				$res_array['countryName']=isset($fetchRes['countryName'])?$fetchRes['countryName']:'';
	            $res_array['state']= isset($fetchRes['stateName'])?$fetchRes['stateName']:'';
	            $res_array['city']=isset($fetchRes['cityName'])?$fetchRes['cityName']:'';
	            $res_array['address']=isset($fetchRes['address'])?$fetchRes['address']:'';
	            $res_array['countryCode']=  isset($fetchRes['code'])?$fetchRes['code']:'';
	            $res_array['cityId']=  isset($fetchRes['cityId'])?$fetchRes['cityId']:0;
	            $res_array['stateId']=  isset($fetchRes['stateId'])?$fetchRes['stateId']:0;
	            $res_array['countryId']=  isset($fetchRes['countryId'])?$fetchRes['countryId']:0;
	            $res_array['mobileNumber']=isset($fetchRes['mobileNumber'])?$fetchRes['mobileNumber']:'';
				$status= true;
				$message='success';
			}else{
				$status= false;
				$message='Address not available for current user.';
			}	
		}else if($deliveryType=='p')
		{			
			 $fetchRes = $db->pdoQuery('Select p.pointName,p.pointAddress,co.countryName,s.stateName from tbl_pick_points as p 
                LEFT JOIN tbl_state as s ON(p.stateId = s.id)
                LEFT JOIN tbl_country as co ON(p.countryId = co.id)
                WHERE p.id = '.$pick_point.'')->result();
            
            $countryCode= getTableValue('tbl_users','code',array('id'=>$userId));
            $mobile= getTableValue('tbl_users','mobileNumber',array('id'=>$userId));
            $salute= getTableValue('tbl_users','salute',array('id'=>$userId));
            $firstName= getTableValue('tbl_users','firstName',array('id'=>$userId));
            $lastName= getTableValue('tbl_users','lastName',array('id'=>$userId));

            $res_array['salute']=$salute;
            $res_array['firstName']=$firstName;
            $res_array['lastName']=$lastName;
            $res_array['pointAddress']=isset($fetchRes['pointAddress'])?$fetchRes['pointAddress']:'';
            $res_array['pointName']=isset($fetchRes['pointName'])?$fetchRes['pointName']:'';
	        $res_array['stateName']= isset($fetchRes['stateName'])?$fetchRes['stateName']:'';
	        $res_array['countryName']=isset($fetchRes['countryName'])?$fetchRes['countryName']:'';
	        $res_array['countryCode']=isset($countryCode)?$countryCode:'';
	        $res_array['mobileNumber']=isset($mobile)?$mobile:'';
	        $status=true;
	        $message="success";

		}else
		{
			$status= false;
			$message='Invalid pick up point.';
		}

	}else{
		$status =false;
		$message ="Invalid user id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/* apply coupon code
	by:NTC 19102016
*/
function cart_scratch_coupon_code_api($userId,$couponCode,$curCode)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0){
		if($couponCode!='' ){
			$today = date('Y-m-d');
			$query = "select id,discount from tbl_coupons where is_active = 'y' and coupon_code = '".$couponCode."' AND start_date <= '".$today."' AND end_date >= '".$today."'";
			$coupon = $db->pdoQuery($query);
			$totalRow = $coupon->affectedRows();
			$fetchRes = $coupon->result();

			if($totalRow == 0){
				$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();

				foreach ($fetchRes as $key => $orders) {
					$db->update("tbl_orders",array("discountAmount"=>0,"is_coupon_used"=>"n","couponId"=>0),array("id"=>$orders['id']));
				}
				$status=false;
				$message ='invalid code.';
			}else{
				$discount = $fetchRes['discount'];
				$couponId = $fetchRes['id'];

				$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();

				//$productPrice = getTableValue('tbl_orders','productPrice',array("id"=>$id));

				foreach ($fetchRes as $key => $orders) {
					$totalAmount = ($orders['productPrice'] * $orders['quantity']) + ($orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']);
					$discountPrice = ($totalAmount * $discount) / 100;
					
					$productPrice = $orders['productPrice'] * $orders['quantity'];
					$finalAmount += ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $discountPrice;

					//Update discount in custom order table
					$db->update("tbl_orders",array("discountAmount"=>strval($discountPrice),"is_coupon_used"=>"y","couponId"=>$couponId),array("id"=>$orders['id']));

					$discountPriceTotal += $discountPrice;
					$i++;
				}

				$res_array['discountPrice'] = number_format(convertCurrency($currencyId,$discountPriceTotal),2);
				$res_array['finalAmount'] = number_format(convertCurrency($currencyId,$finalAmount),2);
				$res_array['finalAmountInDoller']=isset($finalAmount)?$finalAmount:'0.00';
				$res_array['discountInPercentage']=$discount;
				$res_array['currencyCode']=$curCode;
				$res_array['currencySign'] =$currencySign;

				$status=true;
				$message="Coupon code applied successfully.";				
			}

		}else{
			$status =false;
			$message ="Coupon code not available.";
		}
	}else
	{
		$status =false;
		$message ="User id not available.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/* change delivery address api
	by:NTC 19102016
*/
function change_delivery_address_api($request_data)
{
	global $db;
	extract($request_data);
	$deliveryType = isset($deliveryType)?$deliveryType:'d';
	$userId = isset($userId)?$userId:0;
	$pickupPointId = isset($pickupPointId)?$pickupPointId:0;
	$cityId = isset($cityId)?$cityId:0;
	$stateId = isset($stateId)?$stateId:0;
	$countryId = isset($countryId)?$countryId:0;
	$address = isset($address)?$address:'';
	$zipCode = isset($zipCode)?$zipCode:'';
	$salute =isset($salute)?$salute:'';
	$firstName =isset($firstName)?$firstName:'';
	$lastName =isset($lastName)?$lastName:'';
	$code=isset($countryCode)?$countryCode:'';
	$mobileNumber =isset($mobileNumber)?$mobileNumber:'';
	$isNewAddress= isset($isNewAddress)?$isNewAddress:'n';
	if($userId>0)
	{
		if($deliveryType=='d')
		{
			$db->update('tbl_orders',array('deliveryOption'=>$deliveryType), array('userId'=>$userId));

			if($isNewAddress=='y')
			{	
				if($address!='' && $firstName!='' && $lastName!='' && $stateId>0 && $countryId>0 && $zipCode!='')
				{
					$db->update('tbl_users',array('salute'=>$salute,'firstName'=>$firstName,'lastName'=>$lastName,'mobileNumber'=>$mobileNumber,'cityId'=>$cityId,'stateId'=>$stateId,'countryId'=>$countryId,'address'=>$address,'zipCode'=>$zipCode,'code'=>$code), array('id'=>$userId));
					$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();	
					foreach ($fetchRes as $key => $orders) {
								
						$productPrice = $orders['productPrice']*$orders['quantity'];
						$finalShipping = getd2dShippingAmount_api($productPrice,$userId);
						$dutiesAmount = getDutiesAmount_api($orders['productPrice'],$userId,$countryId);
						$adminCharge = getAdminCharge_api($orders['productPrice'],$userId,$countryId);

						$db->update('tbl_orders',array("shippingAmount"=>strval($finalShipping),"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$orders['id'])); 
					}
					$fetchRes = $db->pdoQuery('SELECT * from tbl_users where id ='.$userId.' ')->result();

					if (isset($fetchRes['id']) && $fetchRes['id'] > 0) {
						$uId=$fetchRes['id'];
						$res_array['userId'] = (int)$fetchRes['id'];
						$res_array['salute'] = $fetchRes['salute'];
						$res_array['firstName'] = $fetchRes['firstName'];
						$res_array['lastName'] = $fetchRes['lastName'];
						$res_array['email'] = base64_decode($fetchRes['email']);
						$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
						$res_array['address']=$fetchRes['address'];
						$res_array['zipCode']=$fetchRes['zipCode'];
						$res_array['countryId']=$fetchRes['countryId'];
						$res_array['countryName'] =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
						$res_array['stateId']=$fetchRes['stateId'];
						$res_array['stateName']=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
						$res_array['cityId']=$fetchRes['cityId'];
						$res_array['cityName']=getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
						$res_array['countryCode']=$fetchRes['code'];
						$res_array['mobile']=$fetchRes['mobileNumber'];
						$res_array['secret']=$fetchRes['secret'];
						$res_array['gen']=$fetchRes['gender'];
						$res_array['member']=$fetchRes['createdDate'];
						$res_array['ip']=$fetchRes['ipaddress'];
						$res_array['birthDate']=$fetchRes['birthDate'];
						$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
					}

					$status =true;
					$message="Delivery address changed successfully.";
				}else
				{
					$status=false;
					$message="Please fill all values";
				}

			}else{
				$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();
				$countryId='';	
				foreach ($fetchRes as $key => $orders) {
							
					$productPrice = $orders['productPrice']*$orders['quantity'];
					$finalShipping = getd2dShippingAmount_api($productPrice,$userId);
					$dutiesAmount = getDutiesAmount_api($orders['productPrice'],$userId,$countryId);
					$adminCharge = getAdminCharge_api($orders['productPrice'],$userId,$countryId);

					$db->update('tbl_orders',array("shippingAmount"=>strval($finalShipping),"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$orders['id'])); 
				}
				$status =true;
				$message="Delivery address changed successfully.";
			}	

		}else if($deliveryType=='p')
		{
			if($pickupPointId>0){

				$pickId = $pickupPointId;
				$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pickId));
				
				$pick_point = getTableValue('tbl_pick_points','id',array("stateId"=>$stateId));

				$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();

				$a =array();
				$i=0;
				foreach ($fetchRes as $key => $orders) {

					$productPrice = $orders['productPrice']*$orders['quantity'];
					$finalShipping = getPickShippingAmount_api($productPrice,$stateId,$userId);

					$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pickId));
					$dutiesAmount = getDutiesAmount_api($orders['productPrice'],$userId,$countryId);
					$adminCharge = getAdminCharge_api($orders['productPrice'],$userId,$countryId);
					$db->update('tbl_orders',array("shippingAmount"=>strval($finalShipping),"pick_point"=>$pickId,'deliveryOption'=>$deliveryType,"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$orders['id']));
				}
				$res_array =$a;
				$status =true;
				$message="Pickup point changed successfully.";
			}else
			{
				$status =true;
				$message="Invalid pickup point Id.";
			}
		}else
		{
			$status=false;
			$message="Invalid delivery type";
		}
	}else
	{
		$status =false;
		$message ="User id not available.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/* get user account setting information api
	by:NTC 19102016
*/
 function getAccountSettig_api($userId)
 {
 	 global $db;
	if($userId> 0){
	 	$userData = $db->pdoQuery("SELECT u.id,u.firstName,u.address,u.zipCode,u.countryId,u.stateId,u.cityId,ct.cityName,s.stateName,c.countryName,n.is_active,no.newProductPosted,no.amountAddedInWallet,no.receiveReplayFromAdmin,no.newPrormoPosted,no.orderStatusByAdmin,no.reminder FROM tbl_users AS u 
				LEFT JOIN tbl_city AS ct ON u.cityId = ct.id  
				LEFT JOIN tbl_state AS s ON u.stateId = s.id 
				LEFT JOIN tbl_country AS c ON u.countryId = c.id 
				LEFT JOIN tbl_newsletter_subscriber AS n ON n.email=u.email
				LEFT JOIN tbl_notifications AS no ON no.userId=u.id 
				WHERE u.id = ?",array($userId))->result();
	 	
			$id = isset($userData['id'])? $userData['id'] : '';	
			$address = isset($userData['address'])? $userData['address'] : '' ;	

			$zipCode = isset($userData['zipCode'])? $userData['zipCode'] : '';
			$countryid =is_null($userData['countryId'])?'':$userData['countryId'];
			$stateid = is_null($userData['stateId'])?'':$userData['stateId'];
			$cityid = is_null($userData['cityId'])?'':$userData['cityId'];
			$firstName =  isset($userData['firstName'])? $userData['firstName'] : '';
			$country =$userData['countryName'];
			$states = $userData['stateName'];
			$city = $userData['cityName'];
			$newslettersubscriber =$userData['is_active']=='y'?'y':'n';
			
			$newProductPosted = $userData['newProductPosted']=='y'?'y':'n';
			$amountAddedInWallet = $userData['amountAddedInWallet']=='y'?'y':'n';
			$receiveReplayFromAdmin = $userData['receiveReplayFromAdmin']=='y'?'y':'n';
			$newPrormoPosted = $userData['newPrormoPosted']=='y'?'y':'n';
			$orderStatusByAdmin = $userData['orderStatusByAdmin']=='y'?'y':'n';
			$remindersetbyme = $userData['reminder']=='y'?'y':'n';
		
			$res_array['userId']=$id;
			$res_array['address']=$address;
			$res_array['zipCode']=$zipCode;
			$res_array['countryId']=$countryid;
			$res_array['stateId']=$stateid;
			$res_array['cityid']=$cityid;
			$res_array['firstName']=$firstName;
			$res_array['country']=is_null($country)?'':$country;
			$res_array['states']=is_null($states)?'':$states;
			$res_array['city']=is_null($city)?'':$city;
			$res_array['newslettersubscriber']=$newslettersubscriber;
			$res_array['newProductPosted']=$newProductPosted;
			$res_array['amountAddedInWallet']=$amountAddedInWallet;
			//$res_array['receiveReplayFromAdmin']=$receiveReplayFromAdmin;
			$res_array['newPrormoPosted']=$newPrormoPosted;
			$res_array['orderStatusByAdmin']=$orderStatusByAdmin;
			$res_array['remindersetbyme']=$remindersetbyme;

			$status =true;
			$message='success';

	}else
	{
		$status =false;
		$message ="User id not available.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

 }   
/* delete cart item api
	by:NTC 26102016
*/
function change_cart_item_api($request_data)
{ 
	global $db; 
	$cartId =isset($request_data['cartId'])?$request_data['cartId']:0;
	$pId =isset($request_data['pId'])?$request_data['pId']:0;
	$userId =isset($request_data['userId'])?$request_data['userId']:0;
	if($cartId>0)
	{
		if($userId>0)
			{	
				$pId =getTableValue('tbl_cart','productId',array('id'=>$cartId));
				$db->delete('tbl_cart',array("id"=>$cartId,"userId"=>$userId));
				$db->delete('tbl_orders',array("productId"=>$pId,"userId"=>$userId,"paymentStatus"=>"n"));
				$status =true;
				$message ="Cart Item removed successfully.";
			}else{
				$status =false;
				$message ='Required user id';
			}
		
	}else
	{	
			$status =false;
			$message ='Required cart id.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}
/* checkout product cart  api
	by:NTC 26102016
*/
function cart_checkout_api($request_data,$curCode)
{ 
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	$userId=isset($request_data['userId'])?$request_data['userId']:0;
	$transaction_id =isset($request_data['txn_id'])?$request_data['txn_id']:'';
	$transaction_type =isset($request_data['txn_type'])?$request_data['txn_type']:'';
	$amount =isset($request_data['amount'])?$request_data['amount']:0;
		
	if($userId>0  && $transaction_type!='' && $amount>0)
	{
		$purchaseAproval =getTableValue('tbl_users',"buyStatus",array('id'=>$userId));
		if($purchaseAproval=="" || is_null($purchaseAproval) || $purchaseAproval=='n')
		{
			$status=false;
			$message ="Please wait for purchase approval.";
			$res_array['status']=$status;
			$res_array['message']=$message;
			return json_encode($res_array);
			exit;
		}
		

		if($transaction_id=='' && $transaction_type=='p')
		{
				$status=false;
				$message="transaction id required.";
				$res_array['status']=$status;
				$res_array['message']=$message;
				return json_encode($res_array);
				exit();	
		}
		//check total amount and payment amount
			$fetchRes = $db->pdoQuery("select id,productId,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_orders where userId = ".$userId." and paymentStatus = 'n' ")->results();
			$productPrice = $shippingAmount = $dutiesAmount = $adminCharge = $discountPrice = $paidAmount =0;
			$finalGrossAmount=0;
			foreach ($fetchRes as $key => $orders) {
							
				$productPrice = $orders['productPrice'] * $orders['quantity'];
				$finalAmount = ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $orders['discountAmount'];
				$finalGrossAmount +=$finalAmount;
				
			}
			
			//end check
			
		if($transaction_type=='w')
		{	
			if(round($amount,2)!=round($finalGrossAmount,2) || $finalGrossAmount<=0)
			{	
				$status=false;
				$message="Your paid amount not equal to order amount.";
				$res_array['status']=$status;
				$res_array['message']=$message;


				return json_encode($res_array);
				exit();
			}
			$transaction_id='TXN'.strtoupper(uniqid());
			$walletamt =getTableValue('tbl_users','creditAmount',array('id'=>$userId));
			if(intval($walletamt)>=intval($amount))
			{
				$newcamt =$walletamt - $amount;
				$db->update('tbl_users',array('creditAmount'=>strval($newcamt)),array('id'=>$userId));
				
			}else
			{
				$status=false;
				$message="Insufficient wallet balance.";
				$res_array['status']=$status;
				$res_array['message']=$message;

				return json_encode($res_array);
				exit();
			}
		}
		if($transaction_type=='p')
		{
				$ch = curl_init();
				$clientId = PAYPAL_CLIENT_ID; //client Id
				$secret = PAYPAL_CLIENT_SECRET;//client secrete key
				curl_setopt($ch, CURLOPT_URL, PAYPAL_APP_URL_OAUTH2);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
				$result = curl_exec($ch);
				$accessToken = null;
				if (empty($result))
				{
					$res_array['status']=false;
					$res_array['message']='Invalid access token';
					return $res_array;
					exit;
				}else {
					$json = json_decode($result);
					$accessToken = $json->access_token;
				}
				curl_close($ch);
				
				$curl = curl_init(PAYPAL_APP_URL_PAYMENT.$transaction_id."");
				curl_setopt($curl, CURLOPT_POST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array(
					'Authorization: Bearer ' . $accessToken,
					'Accept: application/json',
					'Content-Type: application/json'
				));
				$response = curl_exec($curl);
			        //print_r($response);exit;
				$result = json_decode($response,true);
				extract($result);
				$transaction_id	= $transactions[0]['related_resources'][0]['sale']['id'];
				$paypalAmount 	= (float)$transactions[0]['amount']['total'];
				$currency 	= $transactions[0]['amount']['currency'];
				$paymentStatus 	= $transactions[0]['related_resources'][0]['sale']['state'];
				$date= date('Y-m-d H:i:s');

				if(round($paypalAmount,2)!=round($finalGrossAmount,2) || $paymentStatus != 'completed')
				{
					$status=false;
					$message="Your paid amount not equal to order amount.";
					$res_array['status']=$status;
					$res_array['message']=$message;
					$res_array['paypalamount']=$paypalAmount;
					$res_array['finalGrossAmount']=round($finalGrossAmount,2);
					$res_array['paymentStatus']=$paymentStatus;
					
					return json_encode($res_array);
					exit();
				}
		}
			//Update order table
			$fetchRes = $db->pdoQuery("select id,productId,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_orders where userId = ".$userId." and paymentStatus = 'n' ")->results();
			$productPricem = $shippingAmount = $dutiesAmount = $adminCharge = $discountPrice = $paidAmount ='';
			foreach ($fetchRes as $key => $orders) {

				//Delete cart 
				$db->delete("tbl_cart",array("userId"=>$userId));

				//Update product quantity
				$product = array();
				$oldQuantity = getTableValue("tbl_product_deals","quantity",array("id"=>$orders['productId']));
				$product['quantity'] = $oldQuantity - $orders['quantity'];
				$db->update('tbl_product_deals',$product,array("id"=>$orders['productId']));

				$productPrice = $orders['productPrice'] * $orders['quantity'];
				$finalAmount = ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $orders['discountAmount'];

				$order = array();
				$order['paidAmount'] = strval($finalAmount);
				$order['paymentStatus'] = 'y';
				$order['transactionId'] = $transaction_id;
				$order['paymentGateway']=$transaction_type;
				$order['deliveryStatus']='p';
				$order['createdDate'] = date('Y-m-d H:i:s');
				$db->update('tbl_orders',$order,array("id"=>$orders['id']));

				//For mail data
				$productName .= getTableValue("tbl_product_deals","productName",array("id"=>$orders['productId'])).',';
				$quantity .= $orders['quantity'].',';
				$productPricem += $orders['productPrice'];
				$shippingAmount += $orders['shippingAmount'];
				$dutiesAmount += $orders['dutiesAmount'];
				$adminCharge += $orders['adminCharge'];
				$discountAmount += $orders['discountAmount'];
				$paidAmount += $finalAmount;
			}

			//Send mail to admin
			$firstName = getTableValue('tbl_users','firstName',array("id"=>$userId));
			$contArray = array(
				"USER_NM"=>$firstName,
				"PRODUCT_NM"=>$productName,
				"QUANTITY"=>$quantity,
				"PRICE"=>$currencySign.' '.number_format(convertCurrency($currencyId,$productPricem),2),
				"DUTIES"=>$currencySign.' '.number_format(convertCurrency($currencyId,$dutiesAmount),2),
				"ADMIN_CHARGE"=>$currencySign.' '.number_format(convertCurrency($currencyId,$adminCharge),2),
				"SHIPPING"=>$currencySign.' '.number_format(convertCurrency($currencyId,$shippingAmount),2),
				"DISCOUNT"=>$currencySign.' '.number_format(convertCurrency($currencyId,$discountAmount),2),
				"PAID_AMOUNT"=>$currencySign.' '.number_format(convertCurrency($currencyId,$paidAmount),2)
			);
			sendMail(ADMIN_EMAIL,"order_admin",$contArray);

			//Send mail to buyer
			$email = getTableValue('tbl_users','email',array("id"=>$userId));
			sendMail(base64_decode($email),"order_buyer",$contArray);

			//For referral module
			$fees = $db->pdoQuery("select referral from tbl_admin_amount where id = 1")->result();
			$referrals = $db->pdoQuery("Select r.id,r.userId,u.firstName,u.email,u.referralAmount FROM tbl_referral_users as r
				LEFT JOIN tbl_users as u ON ( r.userId = u.id )
				WHERE r.email = '".base64_decode($email)."' and r.isPurchase = 'n' and r.paidStatus = 'n'")->result();

			if($referrals['id'] > 0){

				//update referral amount
				$referralAmount = $productPricem * $fees['referral'] / 100;
				$oldReferralAmount = $referrals['referralAmount'];
				$finalReferralAmount = $oldReferralAmount + $referralAmount;
				$oldCreditAmount =getTableValue('tbl_users','creditAmount',array('id'=>$userId));
				$finalCreditAmount = $oldCreditAmount + $referralAmount;

				$db->update('tbl_users',array('referralAmount'=>strval($finalReferralAmount),'creditAmount'=>strval($finalCreditAmount)),array('id'=>$referrals['userId']));

				//update referral status
				$db->update('tbl_referral_users',array('isPurchase'=>'y','paidStatus'=>'y'),array('id'=>$referrals['id']));

				//send email notification to
				$username =getTableValue('tbl_users','firstName',array('id'=>$userId));
				$contArray = array(
					"USER_NM"=>$referrals['firstName'],
					"BONUS"=>$currencySign.number_format(convertCurrency($currencyId,$referralAmount),2),
					"REFERRAL_USER_NM"=>$username
				);
				sendMail(base64_decode($referrals['email']),"referral_bonus",$contArray);
			}
			//For transaction history
			$txn = array();
			$txn['user_id'] = $userId;
			$txn['transaction_id'] = $transaction_id;
			$txn['transaction_type'] = 'p';
			$txn['payment_gateway'] =$transaction_type=='p'?'p':'w';
			$txn['paid_amount'] = strval($paidAmount);
			$db->insert('tbl_payment_history',$txn);
			$status=true;
			$message="Payment successfully.";
	}else
	{	
			$status =false;
			$message ='Required fields not filled';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}

/* get order list api
	by:NTC 27102016
*/
function get_order_list($keyword,$page,$limit,$userId,$curCode,$status,$fromDate,$toDate)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	$start  = 0;
	$limit =$limit * $page;
	if($userId>0)
	{
			$whereCon = '';
			
			//For filters
			if($keyword != ''){
				$whereCon .= "p.productName LIKE '%".$keyword."%'";	
			}else{
				$whereCon .= "1 = 1";
			}
			//For category
			if(isset($fromDate) && $fromDate != '' && isset($toDate) && $toDate != ''){
				$whereCon .=" AND (DATE(o.createdDate) BETWEEN '".date('Y-m-d',strtotime($fromDate))."' AND '".date('Y-m-d',strtotime($toDate))."') ";
			}

			//For sub category
			if($status != ''){
				$whereCon .=" AND o.deliveryStatus = '".$status."'";
			}

			//For total row count
		 $queryCount = "SELECT o.id  FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  WHERE $whereCon AND o.userId = ".$userId." AND o.id_delete = 'n' AND o.paymentStatus = 'y' order by o.id desc";

			$resultCount = $db->pdoQuery($queryCount);
	 		$totalRowCount = $resultCount->affectedRows();
	 		
				   $query = "SELECT DISTINCT o.id,p.id as productId,p.productName,o.orderId,o.productPrice,o.quantity,o.deliveryStatus,o.transactionId,o.createdDate FROM tbl_orders as o LEFT JOIN tbl_product_deals as p ON(p.id = o.productId) WHERE $whereCon AND o.userId =$userId AND o.id_delete = 'n' AND o.paymentStatus = 'y' order by o.id desc
					  LIMIT $start , $limit";
			$result = $db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$totalRow = $result->affectedRows();
	 		
	 		$i=0;
	 		$res_rows=array();
	 		
	 		foreach ($fetchRes as $value) {
	 			$totalAmount = (($value['productPrice'] * $value['quantity']) + $value['shippingAmount'] + $value['dutiesAmount'] + $value['adminCharge']) - $value['discountAmount'];
	 			$pid=$value['productId'];
	 			//$image =checkImage('product/'.$value['productId'].'/'.getTableValue('tbl_product_image','name',array("productId"=>$pid)));
	 			$imagepath = DIR_UPD.'product/'.$value['productId'].'/'.getTableValue('tbl_product_image','name',array("productId"=>$pid));
	 			if(filesize($imagepath)>0)
	 			{
	 				$image =checkImage('product/'.$value['productId'].'/'.getTableValue('tbl_product_image','name',array("productId"=>$pid)));
	 			}else 
	 			{
	 				$image =SITE_UPD.'no_image_thumb.png';
	 			}
	 			$res_rows[$i]['productImage']=$image;
	 			$res_rows[$i]['productName']=$value['productName'];
	 			$res_rows[$i]['transactionId']=$value['transactionId'];
	 			$res_rows[$i]['oId']=$value['id'];
	 			$res_rows[$i]['orderId']=$value['orderId'];
	 			$res_rows[$i]['totalAmount']=number_format(convertCurrency($currencyId,$totalAmount),2);
	 			$res_rows[$i]['createdDate']=date('m-d-Y',strtotime($value['createdDate']));
	 			if($value['deliveryStatus'] == 'd'){
					$ostatus = 'Delivered';
				}else if($value['deliveryStatus'] == 'r'){
					$ostatus = 'Returned';
				}else if($value['deliveryStatus'] == 'p'){
					$ostatus = 'Pending';
				}else if($value['deliveryStatus'] == 's'){
					$ostatus = 'Shipped';
				}
				$res_rows[$i]['status']= $ostatus;

	 			$i++;
	 		}
	 		$res_array['orderlist']=$res_rows;
	 		$res_array['total_records']=$totalRowCount;
	 		$narecords =(intval($totalRowCount) - intval($limit));
	 		$res_array['next_available_records']= $narecords >0?$narecords:0;
			$res_array['currencySign'] =$currencySign;
			$res_array['currencyCode'] =$curCode;
			$status=true;
	 		$message="success";

	}else
	{
		$status =false;
		$message ='Invalid user id.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}

/*  delete order api
	by:NTC 27102016
*/
function delete_order($userId,$orderId)
{
	global $db;
	
	if($userId>0 )
	{
		if($orderId!=''){
		
		$db->update('tbl_orders',array('id_delete'=>'y'),array('id'=>$orderId,'userId'=>$userId));
		$res_array['orderId']=$orderId;
		$status=true;
	 	$message="Order Deleted successfully.";
	 }else
	 {
	 	$status =false;
		$message ='Invalid order id.';
	 }

	}else
	{
		$status =false;
		$message ='Invalid user id.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}

/*  order detail api
	by:NTC 28102016
*/
function order_detail_api($userId,$orderId,$curCode)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0 )
	{
		if($orderId!=''){
		
			$query = "SELECT o.*,p.id as productId,p.productName,p.isDiscount,p.discountPrice,p.actualPrice,p.storeId,c.categoryName,sc.subcategoryName FROM tbl_orders as o
				  LEFT JOIN tbl_product_deals as p ON(o.productId = p.id)
				  LEFT JOIN tbl_categories as c ON(p.categoryId = c.id)
				  LEFT JOIN tbl_subcategory as sc ON(p.subcategoryId = sc.id)
				  where o.id = '".$orderId."'";
			$orders = $db->pdoQuery($query)->result();
			//product details
			
			$productName =$orders['productName'];
			$categoryName =$orders['categoryName'];
			$subcategoryName =$orders['subcategoryName'];
			$storeName =getTableValue('tbl_stores','storeName',array('id'=>$orders['storeId']));
			$quantity = $orders['quantity'];
			$date =date('m/d/Y',strtotime($orders['createdDate']));
			$price=$orders['productPrice'];
			$imageName = getTableValue("tbl_product_image","name",array("productId"=>$orders['productId']));
			$filepath =DIR_UPD."product/".$orders['productId'].'/'.$imageName;
			
			if(filesize($filepath)>0)
			{	
				$imgSrc = checkImage("product/".$orders['productId'].'/'.$imageName);

			}else
			{
				$imgSrc =SITE_UPD."no_image_thumb.png";
			}
			
			$productDetails =array();
			$productDetails['image']=$imgSrc;
			$productDetails['productId']=$orders['productId'];
			$productDetails['orderId']=$orderId;
			$productDetails['orderNumber']=getTableValue('tbl_orders','orderId',array('id'=>$orderId));

			$productDetails['productName']=$productName;
			$productDetails['categoryName']=$categoryName;
			$productDetails['subcategoryName']=$subcategoryName;
			$productDetails['storeName']=$storeName;
			$productDetails['quantity']=$quantity;
			$productDetails['date']=$date;
			$productDetails['price']=number_format(convertCurrency($currencyId,$price),2);

			$res_array['productDetails'] =$productDetails;

			//For shipping detail

			$deliverydetails =array();
			$deliveryOption = $orders['deliveryOption'] == 'p'?'Pick Point':'Door To Door Delivery';
			$shipping = (($orders['deliveryStatus'] == 's')?'Shipped':(($orders['deliveryStatus'] == 'd')?'Delivered':(($orders['deliveryStatus'] == 'p')?'Pending':'Returned')));
			

			$address = getDeliveryAddress_api($userId,$orders['deliveryOption'],$orders['pick_point']);
			if($orders['deliveryOption'] == 'p'){
				$addressTitle = 'Pick point address';
			}else if($orders['deliveryOption'] == 'd'){
				$addressTitle = 'Delivery address';
			}
				//For return button
				$isReturnable = false;
				if($orders['deliveryStatus'] != 'r' && $orders['paymentStatus'] == 'y' && $orders['deliveryStatus']=='d'){
					$isReturnable = true;
				}

			//For delivery days
			$deliveryDays = getDeliveryDays($orders['deliveryOption'],$userId,$orders['pick_point']);
			$deliverydetails['isReturnable']=$isReturnable;
			$deliverydetails['deliveryDays']=$deliveryDays;
			$deliverydetails['deliveryOption']=$deliveryOption;
			$deliverydetails['deliveryAddress']=$address;
			$deliverydetails['shippingStatus']=$shipping;

			$res_array['deliverydetails']=$deliverydetails;
			//Discount price
			$amountdetails =array();
			$actualPrice = '';
			if($orders['isDiscount'] == 'y'){
				$price = number_format($orders['actualPrice'],2);
				$actualPrice = $price;
			}
			//For pricing
			$price = convertCurrency($currencyId,$orders['productPrice']);
			$totalPrice = convertCurrency($currencyId,$orders['productPrice']*$orders['quantity']);
			$dutiesAmount = convertCurrency($currencyId,$orders['dutiesAmount']);
			$adminCharge = convertCurrency($currencyId,$orders['adminCharge']);
			$shippingAmount = convertCurrency($currencyId,$orders['shippingAmount']);
			$discountAmount = convertCurrency($currencyId,$orders['discountAmount']);

			$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;

			$orderstatus = (($orders['order_status']=='a')?'Accepted':(($orders['order_status'] == 'r')?'Rejected':'Pending'));

			$amountdetails['actualPrice']=$actualPrice==''?'':number_format($actualPrice*$orders['quantity'],2);
			$amountdetails['price']=number_format($totalPrice,2);
			$amountdetails['dutiesAmount']=number_format($dutiesAmount,2);
			$amountdetails['adminCharges']=number_format($adminCharge,2);
			$amountdetails['shippingAmount']=number_format($shippingAmount,2);
			$amountdetails['discountAmount']=number_format($discountAmount,2);
			$amountdetails['totalAmount']=number_format($totalAmount,2);
			$amountdetails['orderStatus']=$orderstatus;
			$amountdetails['paymentStatus']=$orders['paymentStatus'];
			$amountdetails['currencySign']=$currencySign;
			$amountdetails['currencyCode']=$curCode;
				
			$res_array['amountdetails']=$amountdetails;
	 		$status=true;
	 		$message="success";
	 }else
	 {
	 	$status =false;
		$message ='Invalid order id.';
	 }

	}else
	{
		$status =false;
		$message ='Invalid user id.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}
/*  product deal add to cart api
	by:NTC 29102016
*/
function add_cart_api($request_data)
{
	global $db;
	extract($request_data);
	$userId = isset($userId)?$userId:0;
	$productId =intval(isset($productId)?$productId:0);
	$quantity =isset($quantity)?$quantity:0;
	$totalQty =getTableValue('tbl_product_deals','quantity',array('id'=>$productId));
	
	if($userId>0)
	{	
		
		if($productId>0){
			if($quantity>0){
				if($totalQty>=intval($quantity))
				{
					$alreadyExist = getTotalRows('tbl_cart',array("productId"=>$productId,"userId"=>$userId),'id');
					if($alreadyExist==0){

						$objpost = new stdClass();
						$objpost->userId = $userId;
						$objpost->productId =$productId;
						$objpost->quantity =$quantity;
						$objpost->createdDate =date('Y-m-d H:i:s');
						$cart_id = $db->insert('tbl_cart', (array)$objpost)->lastInsertId();

						$res_array['cartid'] =$cart_id;
						$status=true;
						$message ='Item added in cart successfully.';
					}else
					{
						$status=false;
						$message="Already in cart";
					}
				}else
				{
					$status=false;
					$message="Please check stock, doesn't add quantity more then stock.";
				}
			}else{
				$status=false;
				$message="Quantity required.";
			}	
		}else
		{
			$status=false;
			$message="Product id required.";
		}

	}else
	{
		$status =false;
		$message ='Invalid user id.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*  return order api
	by:NTC 29102016
*/
function return_order_api($request_data,$filedata){
	global $db;
	$userId =isset($request_data['userId'])?$request_data['userId']:0;
	$productId =isset($request_data['productId'])?$request_data['productId']:0;
	$orderId =isset($request_data['orderId'])?$request_data['orderId']:0;
	$subject =isset($request_data['subject'])?$request_data['subject']:'';
	$message =isset($request_data['message'])?$request_data['message']:'';
	$imagename =isset($filedata["imageName"]["name"])?$filedata["imageName"]["name"]:'';

	$returnId=0;
	if($userId>0 && $productId>0 && $orderId>0 && $subject!='' && $message!=''){

		$objpost = new stdClass();
		$objpost->userId = $userId;
		$objpost->productId =$productId;
		$objpost->orderId =$orderId;
		$objpost->subject=$subject;
		$objpost->message=$message;
		$objpost->createdDate =date('Y-m-d H:i:s');
		$returnId =$db->insert('tbl_return_request', (array)$objpost)->lastInsertId();
		if($returnId>0)
		{
			//for update order delivery status
			$db->update('tbl_orders', array('deliveryStatus'=>'r'), array('id'=>$orderId));
			//For update pending amount
			$amount = getTableValue('tbl_orders','paidAmount',array('id'=>$orderId));
			$oldPending = getTableValue('tbl_users','pendingAmount',array('id'=>$userId));
			$finalPending = strval($oldPending + $amount);
			$db->update('tbl_users',array('pendingAmount'=>$finalPending),array('id'=>$userId));
			
			//For images
			 $uploads_dir = DIR_UPD."returnImage";
		     if(!file_exists($uploads_dir)){
			 	mkdir($uploads_dir,0777,true);
			 }
			 $fileerror =array();
			 $i=0;
			   //start upload image
			 foreach ($filedata as $key => $value) 
			 {
	
				if (count($filedata[$key]["name"])>1) 
				{
			 			for($i=0;$i<count($filedata[$key]['name']);$i++)
			 			{
			 				 if ($filedata[$key]['error'][$i] == UPLOAD_ERR_OK) 
			 				 {
			 				 	 $tmp_name =$filedata[$key]["tmp_name"][$i];
			 		     		 $name = $filedata[$key]["name"][$i];
			 		     		 move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 		     		  $valArray = array("returnId"=>$returnId,"imageName"=>$name,"createdDate"=>date("Y-m-d h:i:s"));
				 					$db->insert("tbl_return_image", $valArray);	
			 		     	}else
			 		     	{
			 		     		$fileerror[$i]=$filedata[$key]['error'][$i];
			 		     		$i++;
			 		     	}

			 			}

			 	}else
			 	{
			 		if ($filedata[$key]['error'] == UPLOAD_ERR_OK) 
			 		{
			 			   $tmp_name=$filedata[$key]["tmp_name"];
			 			   $name = $filedata[$key]["name"];
			 			   move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 			    $valArray = array("returnId"=>$returnId,"imageName"=>$name,"createdDate"=>date("Y-m-d h:i:s"));
				 			$db->insert("tbl_return_image", $valArray);

			 		}else
			 		{
			 			$fileerror[$i]=$filedata[$key]['error'];
			 			$i++;
			 		}
			 	}	
			}
			//end upload image

			//start move before uploaded image

				foreach ($filedata as $key => $value) 
			 	{
					if (count($filedata[$key]["name"])>1) 
					{
						if ($filedata[$key]['name'][0] == '') 
				 		{
				 			$files = scandir($uploads_dir."/d".$orderId,1);
				 			foreach ($files as $file) {
				 				if(!($file=='.' || $file=='..'))
				 				{
				 					//$uploads_dir."/d".$orderId."/".$file;
				 					copy($uploads_dir."/d".$orderId."/".$file,$uploads_dir."/".$file);
				 					unlink($uploads_dir."/d".$orderId."/".$file);
				 					$valArray = array("returnId"=>$returnId,"imageName"=>$file,"createdDate"=>date("Y-m-d h:i:s"));
					 				$db->insert("tbl_return_image", $valArray);
								}
				 				
				 			}
				 			rmdir($uploads_dir."/d".$orderId);
				 		}
				 	}else
				 	{
				 		if ($filedata[$key]['name'] == '') 
				 		{
				 			$files = scandir($uploads_dir."/d".$orderId,1);
				 			foreach ($files as $file) {
				 				if(!($file=='.' || $file=='..'))
				 				{
				 					//$uploads_dir."/d".$orderId."/".$file;
				 					copy($uploads_dir."/d".$orderId."/".$file,$uploads_dir."/".$file);
				 					unlink($uploads_dir."/d".$orderId."/".$file);
				 					$valArray = array("returnId"=>$returnId,"imageName"=>$file,"createdDate"=>date("Y-m-d h:i:s"));
					 				$db->insert("tbl_return_image", $valArray);
								}
				 				
				 			}
				 			rmdir($uploads_dir."/d".$orderId);
				 		}
				 	}	
			}

			//end move before uploaded image
			if(count($filedata)<=0)
			{
				$files = scandir($uploads_dir."/d".$orderId,1);
				 			foreach ($files as $file) {
				 				if(!($file=='.' || $file=='..'))
				 				{
				 					//$uploads_dir."/d".$orderId."/".$file;
				 					copy($uploads_dir."/d".$orderId."/".$file,$uploads_dir."/".$file);
				 					unlink($uploads_dir."/d".$orderId."/".$file);
				 					$valArray = array("returnId"=>$returnId,"imageName"=>$file,"createdDate"=>date("Y-m-d h:i:s"));
					 				$db->insert("tbl_return_image", $valArray);
								}
				 				
				 			}
				 			rmdir($uploads_dir."/d".$orderId);
			}
			 
			 $res_array['fileerror']=$fileerror;
			 $status=true;
			 $message='Order return request successfully.';

		}else
		{
			$status =false;
			$message ='Issue occured.';
		}
	}else
	{
		$status =false;
		$message ='Please provide all required details.';

	}
	$res_array['returnId']=$returnId;
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
//only return single image upload for product_deals
function return_order_image_api($request_data,$filedata)
{   global $db;
	extract($request_data);
	if($orderId>0)
		{
			$imageurl='';
			//For images
			//$res_array['filedata']=$filedata;
			$uploads_dir = DIR_UPD."returnImage/".'d'.$orderId;
			$site_upload=SITE_UPD."returnImage/".'d'.$orderId;
		     if(!file_exists($uploads_dir)){
			 	mkdir($uploads_dir,0777,true);
			 }
			 $fileerror =array();
			 $i=0;
			 
			//start upload image
			 foreach ($filedata as $key => $value) 
			 {
	
				if (count($filedata[$key]["name"])>1) 
				{
			 			for($i=0;$i<count($filedata[$key]['name']);$i++)
			 			{
			 				 if ($filedata[$key]['error'][$i] == UPLOAD_ERR_OK) 
			 				 {
			 				 	 $tmp_name =$filedata[$key]["tmp_name"][$i];
			 		     		 $name = $filedata[$key]["name"][$i];
			 		     		 move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 		     		 $imageurl=$site_upload.'/'.$name;
			 		     		 
			 		     	}else
			 		     	{
			 		     		$fileerror[$i]=$filedata[$key]['error'][$i];
			 		     		$i++;
			 		     	}

			 			}

			 	}else
			 	{
			 		if ($filedata[$key]['error'] == UPLOAD_ERR_OK) 
			 		{
			 			   $tmp_name=$filedata[$key]["tmp_name"];
			 			   $name = $filedata[$key]["name"];
			 			   move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 			   $imageurl=$site_upload.'/'.$name;
			 		}else
			 		{
			 			$fileerror[$i]=$filedata[$key]['error'];
			 			$i++;
			 		}
			 	}	
			}
			//end upload image
			$res_array['fileerror']=$fileerror;
			$status=true;
			$message='Image uploaded.';

		}else
		{
			$status =false;
			$message ='Custom order id required.';
		}
		$res_array['orderId']=$orderId;
		$res_array['imageurl']=$imageurl;
		$res_array['status']=$status;
		$res_array['message']=$message;
		return json_encode($res_array);
}
//only return single image upload for custom order
function return_custom_order_image_api($request_data,$filedata)
{  global $db;
	extract($request_data);
	if($orderId>0)
		{
			$imageurl='';
			//For images
			//$res_array['filedata']=$filedata;
			$uploads_dir = DIR_UPD."returnImage/".'c'.$orderId;
			$site_upload=SITE_UPD."returnImage/".'c'.$orderId;
		     if(!file_exists($uploads_dir)){
			 	mkdir($uploads_dir,0777,true);
			 }
			 $fileerror =array();
			 $i=0;
			 
			//start upload image
			 foreach ($filedata as $key => $value) 
			 {
	
				if (count($filedata[$key]["name"])>1) 
				{
			 			for($i=0;$i<count($filedata[$key]['name']);$i++)
			 			{
			 				 if ($filedata[$key]['error'][$i] == UPLOAD_ERR_OK) 
			 				 {
			 				 	 $tmp_name =$filedata[$key]["tmp_name"][$i];
			 		     		 $name = $filedata[$key]["name"][$i];
			 		     		 move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 		     		 $imageurl =$site_upload.'/'.$name;
			 		     		 
			 		     	}else
			 		     	{
			 		     		$fileerror[$i]=$filedata[$key]['error'][$i];
			 		     		$i++;
			 		     	}

			 			}

			 	}else
			 	{
			 		if ($filedata[$key]['error'] == UPLOAD_ERR_OK) 
			 		{
			 			   $tmp_name=$filedata[$key]["tmp_name"];
			 			   $name = $filedata[$key]["name"];
			 			   move_uploaded_file($tmp_name, "$uploads_dir/$name");
			 			   $imageurl =$site_upload.'/'.$name;
			 		}else
			 		{
			 			$fileerror[$i]=$filedata[$key]['error'];
			 			$i++;
			 		}
			 	}	
			}
			//end upload image

			$res_array['fileerror']=$fileerror;
			$status=true;
			$message='Image uploaded.';

		}else
		{
			$status =false;
			$message ='Custom order id required.';
		}
		$res_array['imageurl']=$imageurl;
		$res_array['orderId']=$orderId;
		$res_array['status']=$status;
		$res_array['message']=$message;
		return json_encode($res_array);
}


/*  get payment history api
	by:NTC 01112016
*/
function payment_history_api($userId,$page,$limit,$curCode)
{
	$curCode='USD';
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	//scroll to page and display more limits data
	$start  = 0;
	$limit =$limit * $page;
	if($userId>0 )
	{
			//For total row count
			$queryCount = "SELECT user_id,transaction_id,transaction_type,payment_gateway,paid_amount,created_date FROM tbl_payment_history WHERE user_id = ".$userId." order by created_date desc";

			$resultCount = $db->pdoQuery($queryCount);
	 		$totRows = $resultCount->affectedRows();

			$query = "SELECT user_id,transaction_id,transaction_type,payment_gateway,paid_amount,created_date FROM tbl_payment_history WHERE user_id = ".$userId." order by created_date desc 
				LIMIT ".$start." , ".$limit."";

			$result = $db->pdoQuery($query);
	 		$fetchRes = $result->results();
	 		$totalRow = $result->affectedRows();
	 		$i=0;
	 		$res_rows = array();
	 		foreach ($fetchRes as  $value) {
	 			$res_rows[$i]['transactionId']=$value['transaction_id'];
	 			$res_rows[$i]['transactionType']=  $value['transaction_type']=='p'?'Product amount':'Deposit fund';
	 			$paymentGateway ='';
	 			switch ($value['payment_gateway']) {
	 				case 'p':
	 					$paymentGateway='Paypal';
	 					break;
	 				case 'w':
	 					$paymentGateway='Wallet';
	 					break;	
	 				case 've':
	 					$paymentGateway='Verve';
	 					break;
	 				case 'pg':
	 					$paymentGateway='Paga';
	 					break;	
	 				case 'vi':
	 					$paymentGateway='Visa';
	 					break;	
	 				}

	 			$res_rows[$i]['paymentGateway']=$paymentGateway;
	 			$res_rows[$i]['paidAmount']= number_format(convertCurrency($currencyId, $value['paid_amount']),2);
	 			$res_rows[$i]['createdDate']=date('m-d-Y',strtotime($value['created_date']));
	 			$i++;
	 		}
	 		$res_array['paymenthistory']=$res_rows;
	 		$res_array['total_records']=$totRows;
			$narecords =(intval($totRows) - intval($limit));
			$res_array['next_available_records']= $narecords >0?$narecords:0;
			$res_array['currencySign']= $currencySign;
			$res_array['currencyCode']= $curCode;
			$status=true;
			$message="success";
	}else
	{
		$status =false;
		$message ='User id required.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/* contact us api
	by:NTC 01112016
*/
function contact_us_api($request_data)
{
	global $db;
	extract($request_data);
	$firstName =isset($firstName)?$firstName:'';
	$lastName =isset($lastName)?$lastName:'';
	$email=isset($email)?$email:'';
	$countryId=isset($countryId)?$countryId:'';
	$stateId=isset($stateId)?$stateId:'';
	$cityId=isset($cityId)?$cityId:'';
	$subject=isset($subject)?$subject:'';
	$desc= isset($message)?$message:'';

	if($firstName!='' && $lastName!='' && $email!='' && $countryId!='' && $stateId!='' && $cityId!='' && $subject!='' && $desc!='')
	{
		$data = array();
    	$data['first_name'] = $firstName;
    	$data['last_name'] = $lastName;
    	$data['email'] = base64_encode($email);
    	$data['country_id'] = $countryId;
    	$data['state_id'] = $stateId;
    	$data['city_id'] = $cityId;
    	$data['subject'] = $subject;
    	$data['message'] = $desc;
    	$db->insert('tbl_contactus',$data);

    	//Send mail to admin
    	$countryName = getTableValue('tbl_country','countryName',array('id'=>$countryId));
    	$stateName = getTableValue('tbl_state','stateName',array('id'=>$stateId));
    	$cityName = getTableValue('tbl_city','cityName',array('id'=>$cityId));

    	$arrayCont = array(
    		'USER_NM'=>$firstName.' '.$lastName,
    		'EMAIL'=>$email,
    		'COUNTRY'=>$countryName,
    		'STATE'=>$stateName,
    		'CITY'=>$cityName,
    		'SUBJECT'=>$subject,
    		'MESSAGE'=>$desc
    	);

		sendMail(ADMIN_EMAIL, 'contact_us_message', $arrayCont);

		$status=true;
		$message='Message send successfully.';
	}else
	{	$res_array['data']=$request_data;
		$status=flase;
		$message='Please provide all require information.';
	}
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*get custom shopping cart information api
	by:NTC 03112016
*/
function custom_cart_list_api($userId,$curCode,$orderId)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	
	if($userId>0 && $orderId!=''){
		//start dscount remove
			$db->update("tbl_orders",array("discountAmount"=>0,"is_coupon_used"=>"n","couponId"=>0),array("paymentStatus"=>'n','userId'=>$userId));
		//end discount remove
		$query = "SELECT o.id orderId,o.productPrice,o.quantity,o.productName FROM tbl_custom_orders as o where o.paymentStatus = 'n' AND o.order_status='a' AND  o.userId = ".$userId." and o.id =". $orderId ."";
		
		$result = $db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$totalRow =$result->affectedRows();
 		if($totalRow<=0){
			
			$status=false;
			$message ='Active order not available.';//$message="Cart empty.";
		
		}else{
			$items =array();
			$i=0;
			foreach($fetchRes as $value){
				$orderId =$value['orderId'];
				$productPrice = convertCurrency($currencyId,isset($value['productPrice'])?$value['productPrice']:0);
				$totalAmount = convertCurrency($currencyId,(isset($value['productPrice'])?$value['productPrice']:0)*(isset($value['quantity'])?$value['quantity']:0));
				$productName= isset($value['productName'])?$value['productName']:0;
				$quantity =isset($value['quantity'])?$value['quantity']:0;
				
				$items[$i]['orderId']=$orderId;
				$items[$i]['productName']=$productName;
				$items[$i]['quantity']=$quantity;
				$items[$i]['productPrice']=number_format($productPrice,2);
				$items[$i]['totalAmount']=number_format($totalAmount,2);
				$i++;

			}	
					
			$query = "SELECT * FROM tbl_custom_orders as o where o.paymentStatus = 'n' AND o.order_status='a'  AND o.userId = ".$userId." and o.id=".$orderId."";
			$fetchRes = $db->pdoQuery($query)->results();

			foreach ($fetchRes as $key => $orders) {
				//For pricing in currency
				$price += convertCurrency($currencyId,$orders['productPrice']);
				$totalPrice += convertCurrency($currencyId,$orders['productPrice']*$orders['quantity']);
				$dutiesAmount += convertCurrency($currencyId,$orders['dutiesAmount']);
				$adminCharge += convertCurrency($currencyId,$orders['adminCharge']);
				$shippingAmount += convertCurrency($currencyId,$orders['shippingAmount']);
				$discountAmount += convertCurrency($currencyId,$orders['discountAmount']);
				//end pricing in currency
				//for in doller
				$totalPriced += $orders['productPrice']*$orders['quantity'];
				$dutiesAmountd += $orders['dutiesAmount'];
				$adminCharged += $orders['adminCharge'];
				$shippingAmountd += $orders['shippingAmount'];
				$discountAmountd += $orders['discountAmount'];
				//end in doller

			}
			$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;
			$amountInDoller =$totalPriced + $dutiesAmountd + $adminCharged + $shippingAmountd - $discountAmountd;

			$status=true;
			$message='success';
			$res_array['cartItems']=$items;
			$res_array['totalPrice']=number_format($totalPrice,2);
			$res_array['dutiesAmount']=number_format($dutiesAmount,2);
			$res_array['adminCharge']=number_format($adminCharge,2);
			$res_array['shippingAmount']=number_format($shippingAmount,2);
			$res_array['discountAmount']=number_format($discountAmount,2);
			$res_array['totalAmount']=number_format($totalAmount,2);
			$res_array['amountInDoller']=round($amountInDoller,2);
			$res_array['currencyCode']=$curCode;
			$res_array['currencySign']=$currencySign;

		}
	}else{
		$status =false;
		$message ="Invalid user id or order id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/* change delivery address for custom api
	by:NTC 03112016
*/
function custom_change_delivery_address_api($request_data)
{
	global $db;
	extract($request_data);
	$deliveryType = isset($deliveryType)?$deliveryType:'d';
	$userId = isset($userId)?$userId:0;
	$pickupPointId = isset($pickupPointId)?$pickupPointId:0;
	$cityId = isset($cityId)?$cityId:0;
	$stateId = isset($stateId)?$stateId:0;
	$countryId = isset($countryId)?$countryId:0;
	$address = isset($address)?$address:'';
	$zipCode = isset($zipCode)?$zipCode:'';
	$salute =isset($salute)?$salute:'';
	$firstName =isset($firstName)?$firstName:'';
	$lastName =isset($lastName)?$lastName:'';
	$mobileNumber =isset($mobileNumber)?$mobileNumber:'';
	$code=isset($countryCode)?$countryCode:'';
	$isNewAddress= isset($isNewAddress)?$isNewAddress:'n';
	if($userId>0)
	{

		if($deliveryType=='d')
		{
			$db->update('tbl_custom_orders',array('deliveryOption'=>$deliveryType), array('userId'=>$userId));

			if($isNewAddress=='y')
			{	
				if($address!='' && $firstName!='' && $lastName!=''  && $stateId>0 && $countryId>0 && $zipCode!='')
				{
					$db->update('tbl_users',array('salute'=>$salute,'firstName'=>$firstName,'lastName'=>$lastName,'mobileNumber'=>$mobileNumber,'cityId'=>$cityId,'stateId'=>$stateId,'countryId'=>$countryId,'address'=>$address,'zipCode'=>$zipCode), array('id'=>$userId));
					$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_custom_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();
					$countryId='';
					foreach ($fetchRes as $key => $orders) {
								
						$productPrice = $orders['productPrice']*$orders['quantity'];
						$finalShipping = getd2dShippingAmount_api($productPrice,$userId);
						$dutiesAmount = getDutiesAmount_api($orders['productPrice'],$userId,$countryId);
						$adminCharge = getAdminCharge_api($orders['productPrice'],$userId,$countryId);

						$db->update('tbl_custom_orders',array("shippingAmount"=>strval($finalShipping),"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$orders['id'])); 
					}

					$fetchRes = $db->pdoQuery('SELECT * from tbl_users where id ='.$userId.' ')->result();

				if (isset($fetchRes['id']) && $fetchRes['id'] > 0) {
					$uId=(int)$fetchRes['id'];
					$res_array['userId'] = (int)$fetchRes['id'];
					$res_array['salute'] = $fetchRes['salute'];
					$res_array['firstName'] = $fetchRes['firstName'];
					$res_array['lastName'] = $fetchRes['lastName'];
					$res_array['email'] = base64_decode($fetchRes['email']);
					$res_array['userImg'] = checkImage('profile/'.$uId.'/',$fetchRes['profileImage']);
					$res_array['address']=$fetchRes['address'];
					$res_array['zipCode']=$fetchRes['zipCode'];
					$res_array['countryId']=$fetchRes['countryId'];
					$res_array['countryName'] =getTableValue('tbl_country','countryName',array('id'=>$fetchRes['countryId']));
					$res_array['stateId']=$fetchRes['stateId'];
					$res_array['stateName']=getTableValue('tbl_state','stateName',array('id'=>$fetchRes['stateId']));
					$res_array['cityId']=$fetchRes['cityId'];
					$res_array['cityName']=getTableValue('tbl_city','cityName',array('id'=>$fetchRes['cityId']));
					$res_array['countryCode']=$fetchRes['code'];
					$res_array['mobile']=$fetchRes['mobileNumber'];
					$res_array['secret']=$fetchRes['secret'];
					$res_array['gen']=$fetchRes['gender'];
					$res_array['member']=$fetchRes['createdDate'];
					$res_array['ip']=$fetchRes['ipaddress'];
					$res_array['birthDate']=$fetchRes['birthDate'];
					$res_array['paypalEmail']=base64_decode($fetchRes['paypalEmail']);
				}

					$status =true;
					$message="success";
				}else
				{
					$status=false;
					$message="Please fill all values";
				}

			}else{
				$countryId='';
				$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_custom_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();

				foreach ($fetchRes as $key => $orders) {
							
					$productPrice = $orders['productPrice']*$orders['quantity'];

					$finalShipping = getd2dShippingAmount_api($productPrice,$userId);
					$dutiesAmount = getDutiesAmount_api($orders['productPrice'],$userId,$countryId);
					$adminCharge = getAdminCharge_api($orders['productPrice'],$userId,$countryId);
					$db->update('tbl_custom_orders',array("shippingAmount"=>strval($finalShipping),"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$orders['id'])); 
				}
				$status =true;
				$message="success";
			}	

		}else if($deliveryType=='p')
		{
			if($pickupPointId>0){

				$pickId = $pickupPointId;
				$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pickId));
				
				$pick_point = getTableValue('tbl_pick_points','id',array("stateId"=>$stateId));

				$fetchRes = $db->pdoQuery("select id,productPrice,dutiesAmount,adminCharge,discountAmount,quantity from tbl_custom_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();
				$i=0;
				foreach ($fetchRes as $key => $orders) {

					$productPrice = $orders['productPrice']*$orders['quantity'];
					$finalShipping = getPickShippingAmount_api($productPrice,$stateId,$userId);
					$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pickupPointId));
					$dutiesAmount = getDutiesAmount_api($orders['productPrice'],$userId,$countryId);
					$adminCharge = getAdminCharge_api($orders['productPrice'],$userId,$countryId);
					$db->update('tbl_custom_orders',array("shippingAmount"=>strval($finalShipping),"pick_point"=>$pickId,'deliveryOption'=>$deliveryType,"dutiesAmount"=>strval($dutiesAmount),"adminCharge"=>strval($adminCharge)),array("id"=>$orders['id']));
				}
				
				$status =true;
				$message="success";
			}else
			{
				$status =true;
				$message="Invalid pickup point Id.";
			}
		}else
		{
			$status=false;
			$message="Invalid delivery type";
		}


	}else
	{
		$status =false;
		$message ="User id not available.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/* apply custom order coupon code
	by:NTC 03112016
*/
function custom_cart_scratch_coupon_code_api($userId,$couponCode,$curCode,$orderId)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0 && $orderId!=''){
		if($couponCode!='' ){
			$today = date('Y-m-d');
			$query = "select id,discount from tbl_coupons where is_active = 'y' and coupon_code = '".$couponCode."' AND start_date <= '".$today."' AND end_date >= '".$today."'";
			$coupon = $db->pdoQuery($query);
			$totalRow = $coupon->affectedRows();
			$fetchRes = $coupon->result();

			if($totalRow == 0){
				$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity from tbl_custom_orders where paymentStatus = 'n' AND userId = ".$userId."")->results();

				foreach ($fetchRes as $key => $orders) {
					$db->update("tbl_custom_orders",array("discountAmount"=>0,"is_coupon_used"=>"n","couponId"=>0),array("id"=>$orders['id']));
				}
				$status=false;
				$message ='invalid code.';
			}else{
				$discount = $fetchRes['discount'];
				$couponId = $fetchRes['id'];

				$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity from tbl_custom_orders where paymentStatus = 'n' AND order_status='a' AND userId = ".$userId." and id =".$orderId."")->results();

				//$productPrice = getTableValue('tbl_orders','productPrice',array("id"=>$id));

				foreach ($fetchRes as $key => $orders) {
					$totalAmount = ($orders['productPrice'] * $orders['quantity']) + ($orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']);
					$discountPrice = ($totalAmount * $discount) / 100;
					
					$productPrice = $orders['productPrice'] * $orders['quantity'];
					$finalAmount += ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $discountPrice;

					//Update discount in custom order table
					$db->update("tbl_custom_orders",array("discountAmount"=>strval($discountPrice),"is_coupon_used"=>"y","couponId"=>$couponId),array("id"=>$orders['id']));

					$discountPriceTotal += $discountPrice;
					$i++;
				}

				$res_array['discountPrice'] = number_format(convertCurrency($currencyId,$discountPriceTotal),2);
				$res_array['finalAmount'] = number_format(convertCurrency($currencyId,$finalAmount),2);
				$res_array['finalAmountInDoller']=number_format(isset($finalAmount)?$finalAmount:'0.00',2);
				$res_array['discountInPercentage']=$discount;
				$res_array['currencyCode']=$curCode;
				$res_array['currencySign'] =$currencySign;

				$status=true;
				$message="Coupon code applied successfully.";
				
			}

		}else{
			$status =false;
			$message ="Coupon code not available.";
		}
	}else
	{
		$status =false;
		$message ="User id or order id not available.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);

}
/* checkout custom order cart  api
	by:NTC 03112016
*/
function custom_cart_checkout_api($request_data,$curCode,$orderId)
{ 
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	$userId=isset($request_data['userId'])?$request_data['userId']:0;
	$transaction_id =isset($request_data['txn_id'])?$request_data['txn_id']:'';
	$transaction_type =isset($request_data['txn_type'])?$request_data['txn_type']:'';
	$amount =isset($request_data['amount'])?$request_data['amount']:0;
	$orderId=isset($request_data['oId'])?$request_data['oId']:0;
	if($userId>0  && $transaction_type!='' && (float)$amount>0 && $orderId!='')
	{
		$purchaseAproval =getTableValue('tbl_users',"buyStatus",array('id'=>$userId));
		if($purchaseAproval=='n')
		{
			$status=false;
			$message ="Please wait for purchase approval.";
			$res_array['status']=$status;
			$res_array['message']=$message;
			return json_encode($res_array);
			exit;
		}
		if($transaction_id=='' && $transaction_type=='p')
		{
				$status=false;
				$message="transaction id required.";
				$res_array['status']=$status;
				$res_array['message']=$message;

				return json_encode($res_array);
				exit();	
		}

		//check total amount and payment amount
			$fetchRes = $db->pdoQuery("select id,productName,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_custom_orders where userId = ".$userId." and id=".$orderId." and paymentStatus = 'n' AND order_status='a' ")->results();
			$productPrice = $shippingAmount = $dutiesAmount = $adminCharge = $discountPrice = $paidAmount ='';
			$finalGrossAmount=0;
			foreach ($fetchRes as $key => $orders) {
							
				$productPrice = $orders['productPrice'] * $orders['quantity'];
				$finalAmount = ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $orders['discountAmount'];
				$finalGrossAmount +=$finalAmount;
			}
			//end check
		if($transaction_type=='w')
		{
			if(round($amount,2)!=round($finalGrossAmount,2) || $finalGrossAmount<=0)
			{
				$status=false;
				$message="Your paid amount not equal to order amount.";
				$res_array['status']=$status;
				$res_array['message']=$message;

				return json_encode($res_array);
				exit();
			}
			$transaction_id='TXN'.strtoupper(uniqid());
			$walletamt =getTableValue('tbl_users','creditAmount',array('id'=>$userId));
			if(intval($walletamt)>=intval($amount))
			{
				$newcamt =$walletamt - $amount;
				$db->update('tbl_users',array('creditAmount'=>strval($newcamt)),array('id'=>$userId));
				
			}else
			{
				$status=false;
				$message="Insufficient wallet balance.";
				$res_array['status']=$status;
				$res_array['message']=$message;

				return json_encode($res_array);
				exit();
			}
		}
		if($transaction_type=='p')
		{
				$ch = curl_init();
				$clientId = PAYPAL_CLIENT_ID; //client Id
				$secret = PAYPAL_CLIENT_SECRET;//client secrete key
				curl_setopt($ch, CURLOPT_URL, PAYPAL_APP_URL_OAUTH2);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_USERPWD, $clientId . ":" . $secret);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
				$result = curl_exec($ch);
				$accessToken = null;
				if (empty($result))
				{
					$res_array['status']=false;
					$res_array['message']='Invalid access token';
					return $res_array;
					exit;
				}else {
					$json = json_decode($result);
					$accessToken = $json->access_token;
				}
				curl_close($ch);
				
				$curl = curl_init(PAYPAL_APP_URL_PAYMENT.$transaction_id."");
				curl_setopt($curl, CURLOPT_POST, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_HEADER, false);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array(
					'Authorization: Bearer ' . $accessToken,
					'Accept: application/json',
					'Content-Type: application/json'
				));
				$response = curl_exec($curl);
			      //print_r($response);exit;
				$result = json_decode($response,true);
				extract($result);
				$transaction_id	= $transactions[0]['related_resources'][0]['sale']['id'];
				$paypalAmount 	= (float)$transactions[0]['amount']['total'];
				$currency 	= $transactions[0]['amount']['currency'];
				$paymentStatus 	= $transactions[0]['related_resources'][0]['sale']['state'];
							
				if(round($paypalAmount,2)!=round($finalGrossAmount,2) || $paymentStatus != 'completed' )
				{
					$status=false;
					$message="Your paid amount not equal to order amount.";
					$res_array['status']=$status;
					$res_array['message']=$message;

					return json_encode($res_array);
					exit();
				}
		}

			//Update custom order table
			$fetchRes = $db->pdoQuery("select id,productName,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_custom_orders where userId = ".$userId." and id=".$orderId." and  paymentStatus = 'n' AND order_status='a' ")->results();
			$productPrice = $shippingAmount = $dutiesAmount = $adminCharge = $discountPrice = $paidAmount ='';
			foreach ($fetchRes as $key => $orders) {
							
				$productPrice = $orders['productPrice'] * $orders['quantity'];
				$finalAmount = ($productPrice + $orders['shippingAmount'] + $orders['dutiesAmount'] + $orders['adminCharge']) - $orders['discountAmount'];

				$order = array();
				$order['paidAmount'] = strval($finalAmount);
				$order['paymentStatus'] = 'y';
				$order['transactionId'] = $transaction_id;
				$order['createdDate'] = date('Y-m-d H:i:s');
				$order['paymentGateway']=$transaction_type;
				$order['deliveryStatus']='p';
				$db->update('tbl_custom_orders',$order,array("id"=>$orders['id']));

				//For mail data
				$productName .= $orders['productName'].',';
				$quantity .= $orders['quantity'].',';
				$productPricem += $orders['productPrice'];
				$shippingAmount += $orders['shippingAmount'];
				$dutiesAmount += $orders['dutiesAmount'];
				$adminCharge += $orders['adminCharge'];
				$discountAmount += $orders['discountAmount'];
				$paidAmount += $finalAmount;

			}

			//Send mail to admin
			$firstName = getTableValue('tbl_users','firstName',array("id"=>$userId));
			$contArray = array(
				"USER_NM"=>$firstName,
				"PRODUCT_NM"=>$productName,
				"QUANTITY"=>$quantity,
				"PRICE"=>$currencySign.' '.number_format(convertCurrency($currencyId,$productPricem),2),
				"DUTIES"=>$currencySign.' '.number_format(convertCurrency($currencyId,$dutiesAmount),2),
				"ADMIN_CHARGE"=>$currencySign.' '.number_format(convertCurrency($currencyId,$adminCharge),2),
				"SHIPPING"=>$currencySign.' '.number_format(convertCurrency($currencyId,$shippingAmount),2),
				"DISCOUNT"=>$currencySign.' '.number_format(convertCurrency($currencyId,$discountAmount),2),
				"PAID_AMOUNT"=>$currencySign.' '.number_format(convertCurrency($currencyId,$paidAmount),2)
			);
			sendMail(ADMIN_EMAIL,"order_admin",$contArray);

			//Send mail to buyer
			$email = getTableValue('tbl_users','email',array("id"=>$userId));
			sendMail(base64_decode($email),"order_buyer",$contArray);

			//For referral module
			$fees = $db->pdoQuery("select referral from tbl_admin_amount where id = 1")->result();
			$referrals = $db->pdoQuery("Select r.id,r.userId,u.firstName,u.email,u.referralAmount FROM tbl_referral_users as r
				LEFT JOIN tbl_users as u ON ( r.userId = u.id )
				WHERE r.email = '".base64_decode($email)."' and r.isPurchase = 'n' and r.paidStatus = 'n'")->result();

			if($referrals['id'] > 0){

				//update referral amount
				$referralAmount = $productPricem * $fees['referral'] / 100;
				$oldReferralAmount = $referrals['referralAmount'];
				$finalReferralAmount = $oldReferralAmount + $referralAmount;
				$oldCreditAmount =getTableValue('tbl_users','creditAmount',array('id'=>$userId));
				$finalCreditAmount = $oldCreditAmount + $referralAmount;

				$db->update('tbl_users',array('referralAmount'=>strval($finalReferralAmount),'creditAmount'=>strval($finalCreditAmount)),array('id'=>$referrals['userId']));

				//update referral status
				$db->update('tbl_referral_users',array('isPurchase'=>'y','paidStatus'=>'y'),array('id'=>$referrals['id']));

				//send email notification to
				$username =getTableValue('tbl_users','firstName',array('id'=>$userId));
				$contArray = array(
					"USER_NM"=>$referrals['firstName'],
					"BONUS"=>$currencySign.number_format(convertCurrency($currencyId,$referralAmount),2),
					"REFERRAL_USER_NM"=>$username
				);
				sendMail(base64_decode($referrals['email']),"referral_bonus",$contArray);
			}
			//For transaction history
			$txn = array();
			$txn['user_id'] = $userId;
			$txn['transaction_id'] = $transaction_id;
			$txn['transaction_type'] = 'p';
			$txn['payment_gateway'] =$transaction_type=='p'?'p':'w';
			$txn['paid_amount'] = strval($paidAmount);
			$db->insert('tbl_payment_history',$txn);
			$status=true;
			$message="Payment successfully.";
	}else
	{	
			$status =false;
			$message ='Required fields not filled';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}

function product_deal_buy_api($request_data,$curCode)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));	extract($request_data);
	$userId = isset($userId)?$userId:0;
	$productId =intval(isset($productId)?$productId:0);
	$quantity =isset($quantity)?$quantity:0;
	$totalQty =getTableValue('tbl_product_deals','quantity',array('id'=>$productId));
	
	if($userId>0)
	{	
		if($productId>0){
			if($quantity>0){
				if($totalQty>=intval($quantity))
				{
					$product = $db->pdoQuery("Select p.id,p.isDiscount,p.actualPrice,p.discountPrice from tbl_product_deals as p where p.id = ".$productId."")->results();
					$data = array();
			
					foreach ($product as $key => $value) 
					{
						if($value['isDiscount'] == 'y')
						{
							$productPrice = $value['discountPrice'];
						}else{
							$productPrice = $value['actualPrice'];
						}	
						$alreadyExist = getTableValue("tbl_orders","id",array("userId"=>$userId,"productId"=>$value['id'],"paymentStatus"=>"n"));
						$data['userId'] = $userId;
						$data['orderId'] = 'ORDER'.time().strtoupper(genrateRandom(4));
						$data['productId'] = $value['id'];
						$data['productPrice'] = strval($productPrice);
						$data['quantity'] = $quantity;
						$totalProductPrice = strval($productPrice * $quantity);
						
						$data['createdDate']=date('Y-m-d H:i:s');
					    if($alreadyExist > 0)
						{
								
							//For update shipping amount
							$deliveryOption = getTableValue('tbl_orders','deliveryOption',array('id'=>$alreadyExist));
								$countryId='';
								if($deliveryOption == 'd'){
									$productPrice = $data['productPrice']*$data['quantity'];
									$shippingAmount = getd2dShippingAmount_api($productPrice,$userId);
									$dutiesAmount = getDutiesAmount_api($totalProductPrice,$userId,$countryId);
									$adminCharge = getAdminCharge_api($totalProductPrice,$userId,$countryId);
									$data['dutiesAmount'] = strval($dutiesAmount);
									$data['adminCharge'] = strval($adminCharge);
									$data['shippingAmount'] = strval($shippingAmount);
								}else if($deliveryOption == 'p'){
									$pick_point = getTableValue('tbl_orders','pick_point',array('id'=>$alreadyExist));
									$stateId = getTableValue('tbl_pick_points','stateId',array('id'=>$pick_point));
									$countryId = getTableValue('tbl_pick_points','countryId',array('id'=>$pick_point));
									$productPrice = $data['productPrice']*$data['quantity'];
									$shippingAmount = getPickShippingAmount($productPrice,$stateId);
									$dutiesAmount = getDutiesAmount_api($totalProductPrice,$userId,$countryId);
									$adminCharge = getAdminCharge_api($totalProductPrice,$userId,$countryId);
									$data['dutiesAmount'] = strval($dutiesAmount);
									$data['adminCharge'] = strval($adminCharge);
									$data['shippingAmount'] = strval($shippingAmount);
								}

								$db->update("tbl_orders",$data,array("id"=>$alreadyExist));
								$oid=$alreadyExist;
						}else{
								$data['deliveryOption']='d';
								$oid =$db->Insert("tbl_orders",$data)->lastInsertId();	
						}
					}
					//response data start
					$query = "SELECT o.*,p.productName,p.weight FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  where o.paymentStatus = 'n' AND o.userId = ".$userId." ";
		
					$result = $db->pdoQuery($query);
			 		$fetchRes = $result->results();
			 		$totalRow =$result->affectedRows();
			 		if($totalRow<=0){
						
						$status=false;
						$message="pending order not found.";
						
					}else{
						$items =array();
						$i=0;
						foreach($fetchRes as $value){
							$orderId =$value['orderId'];
							$productPrice = convertCurrency($currencyId,isset($value['productPrice'])?$value['productPrice']:0);
							$totalAmount = convertCurrency($currencyId,(isset($value['productPrice'])?$value['productPrice']:0)*(isset($value['quantity'])?$value['quantity']:0));
							$productName= isset($value['productName'])?$value['productName']:0;
							$quantity =isset($value['quantity'])?$value['quantity']:0;

							$items[$i]['orderId']=$orderId;
							$items[$i]['productName']=$productName;
							$items[$i]['quantity']=$quantity;
							$items[$i]['productPrice']=number_format($productPrice,2);
							$items[$i]['totalAmount']=number_format($totalAmount,2);
							$i++;

						}	
								
						$query = "SELECT * FROM tbl_orders as o where o.paymentStatus = 'n'  AND o.userId = ".$userId."";
						$fetchRes = $db->pdoQuery($query)->results();
							$dutiesAmount=0;
							$totalPriced=0;
							$adminCharge=0;
							$shippingAmount=0;
							$discountAmount=0;
						foreach ($fetchRes as $key => $orders) {
							//For pricing in currency
							$price += convertCurrency($currencyId,$orders['productPrice']);
							$totalPrice += convertCurrency($currencyId,$orders['productPrice']*$orders['quantity']);
							$dutiesAmount += convertCurrency($currencyId,$orders['dutiesAmount']);
							$adminCharge += convertCurrency($currencyId,$orders['adminCharge']);
							$shippingAmount += convertCurrency($currencyId,$orders['shippingAmount']);
							$discountAmount += convertCurrency($currencyId,$orders['discountAmount']);
							//end pricing in currency
							//for in doller
							$totalPriced += $orders['productPrice']*$orders['quantity'];
							$dutiesAmountd += $orders['dutiesAmount'];
							$adminCharged += $orders['adminCharge'];
							$shippingAmountd += $orders['shippingAmount'];
							$discountAmountd += $orders['discountAmount'];
							//end in doller

						}
						$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;
						$amountInDoller =$totalPriced + $dutiesAmountd + $adminCharged + $shippingAmountd - $discountAmountd;

						$res_array['orders']=$items;
						$res_array['totalPrice']=number_format($totalPrice,2);
						$res_array['dutiesAmount']=number_format($dutiesAmount,2);
						$res_array['adminCharge']=number_format($adminCharge,2);
						$res_array['shippingAmount']=number_format($shippingAmount,2);
						$res_array['discountAmount']=number_format($discountAmount,2);
						$res_array['totalAmount']=number_format($totalAmount,2);
						$res_array['amountInDoller']=round($amountInDoller,2);
						$res_array['currencyCode']=$curCode;
						$res_array['currencySign']=$currencySign;
					}
					//response data end

					$status=true;
					$message="success";
				
				}else
				{
					$status=false;
					$message="Please check stock, doesn't add quantity more then stock.";
				}
			}else{
				$status=false;
				$message="Quantity required.";
			}	
		}else
		{
			$status=false;
			$message="Product id required.";
		}

	}else
	{
		$status =false;
		$message ='Invalid user id.';

	}
	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}

function cart_checkoutlist_api($request_data,$userId,$curCode){
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	
	if($userId>0){
		//start dscount remove
		$db->update("tbl_orders",array("discountAmount"=>0,"is_coupon_used"=>"n","couponId"=>0),array("paymentStatus"=>'n','userId'=>$userId));
		//end discount remove
		$query = "SELECT o.*,p.productName,p.weight,p.quantity as maxQuntity FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  where o.paymentStatus = 'n' AND o.userId = ".$userId."";
		
		$result = $db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$totalRow =$result->affectedRows();
 		if($totalRow<=0){
			
			$status=false;
			$message="Pending order not found.";
			
		}else{
			$items =array();
			$i=0;
			foreach($fetchRes as $value){
				
				$orderId =$value['orderId'];
				$productPrice = number_format(convertCurrency($currencyId,isset($value['productPrice'])?$value['productPrice']:0),2);
				$quantity =isset($value['quantity'])?$value['quantity']:0;
				$totalAmount =number_format($productPrice * $quantity,2);
				$productName= isset($value['productName'])?$value['productName']:0;
				$items[$i]['orderId']=$orderId;
				$items[$i]['productName']=$productName;
				$items[$i]['quantity']=$quantity;
				$items[$i]['maxQuntity']=$value['maxQuntity']==''?0:$value['maxQuntity'];
				$items[$i]['productPrice']=$productPrice;
				$items[$i]['totalAmount']=$totalAmount;
				$i++;

			}	
					
			$query = "SELECT * FROM tbl_orders as o where o.paymentStatus = 'n'  AND o.userId = ".$userId."";
			$fetchRes = $db->pdoQuery($query)->results();
			$dutiesAmount=0;
			$adminCharge=0;
			$totalPrice=0;
			$shippingAmount=0;
			$discountAmount=0;
			foreach ($fetchRes as $key => $orders) {
				//For pricing in currency
				$price += convertCurrency($currencyId,$orders['productPrice']);
				$totalPrice += convertCurrency($currencyId,$orders['productPrice']*$orders['quantity']);
				$dutiesAmount += convertCurrency($currencyId,$orders['dutiesAmount']);
				$adminCharge += convertCurrency($currencyId,$orders['adminCharge']);
				$shippingAmount += convertCurrency($currencyId,$orders['shippingAmount']);
				$discountAmount += convertCurrency($currencyId,$orders['discountAmount']);
				//end pricing in currency
				//for doller amount
				$totalPriced += $orders['productPrice']*$orders['quantity'];
				$dutiesAmountd += $orders['dutiesAmount'];
				$adminCharged += $orders['adminCharge'];
				$shippingAmountd += $orders['shippingAmount'];
				$discountAmountd += $orders['discountAmount'];
				//end doller amount

			}
			$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;
			$amountInDoller =$totalPriced + $dutiesAmountd + $adminCharged + $shippingAmountd - $discountAmountd;

			$status=true;
			$message='success';
			$res_array['checkoutlist']=$items;
			$res_array['totalPrice']=number_format($totalPrice,2);
			$res_array['dutiesAmount']=number_format($dutiesAmount,2);
			$res_array['adminCharge']=number_format($adminCharge,2);
			$res_array['shippingAmount']=number_format($shippingAmount,2);
			$res_array['discountAmount']=number_format($discountAmount,2);
			$res_array['totalAmount']=number_format($totalAmount,2);
			$res_array['amountInDoller']=number_format($amountInDoller,2);
			$res_array['currencyCode']=$curCode;
			$res_array['currencySign']=$currencySign;
		}
	}else{
		$status =false;
		$message ="Invalid user id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;
	return json_encode($res_array);
}
/*  transaction details api
	by:NTC 10112016
*/
function transaction_detail_api($userId,$txnId,$curCode)
{
	global $db,$currencySign,$currencyId;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0 )
	{
		if($txnId!=''){
		
			$query = "SELECT o.*,p.id as productId,p.productName,c.categoryName,sc.subcategoryName FROM tbl_orders as o
				  LEFT JOIN tbl_product_deals as p ON(o.productId = p.id)
				  LEFT JOIN tbl_categories as c ON(p.categoryId = c.id)
				  LEFT JOIN tbl_subcategory as sc ON(p.subcategoryId = sc.id)
				  where o.transactionId = '".$txnId."'";
			$fetchRes = $db->pdoQuery($query)->results();
		
			//product details
			if(!empty($fetchRes))
			{
				$i=0;
				$productDetails =array();
				$amountdetails =array();
				$totalPriced=0;
				$dutiesAmountd=0;
				$adminCharged=0;
				$shippingAmountd=0;
				$totalAmountd=0;
				$discountAmountd=0;
				foreach ($fetchRes  as $key=> $orders) {

					$productName =$orders['productName'];
					$quantity = $orders['quantity'];
					$date =date('m/d/Y',strtotime($orders['createdDate']));
					$price=$orders['productPrice'];
					$imageName = getTableValue("tbl_product_image","name",array("productId"=>$orders['productId']));
					$filepath =DIR_UPD."product/".$orders['productId'].'/'.$imageName;
			
					if(filesize($filepath)>0)
					{	
						$imgSrc = checkImage("product/".$orders['productId'].'/'.$imageName);
					}else
					{
						$imgSrc =SITE_UPD."no_image_thumb.png";
					}
					$productDetails[$i]['image']=$imgSrc;
					$productDetails[$i]['orderId']=$orders['orderId'];
					$productDetails[$i]['productName']=$orders['productName'];
					$productDetails[$i]['quantity']=$quantity;
					$productDetails[$i]['purchaseDate']=$date;
					$productDetails[$i]['price']=number_format(convertCurrency($currencyId,$price),2);

					//amount details
					
					$totalPrice = convertCurrency($currencyId,$orders['productPrice']*$orders['quantity']);
					$dutiesAmount = convertCurrency($currencyId,$orders['dutiesAmount']);
					$adminCharge = convertCurrency($currencyId,$orders['adminCharge']);
					$shippingAmount = convertCurrency($currencyId,$orders['shippingAmount']);
					$discountAmount = convertCurrency($currencyId,$orders['discountAmount']);
					
					$totalAmount = $totalPrice + $dutiesAmount + $adminCharge + $shippingAmount - $discountAmount;
					$totalPriced=$totalPriced+$totalPrice;
					$dutiesAmountd=$dutiesAmountd+$dutiesAmount;
					$adminCharged=$adminCharged+$adminCharge;
					$shippingAmountd=$shippingAmountd+$shippingAmount;
					$totalAmountd=$totalAmountd+$totalAmount;
					$discountAmountd=$discountAmountd+$discountAmount;
					//$orderstatus = (($orders['order_status']=='a')?'Accepted':(($orders['order_status'] == 'r')?'Rejected':'Pending'));
					$amountdetails['totalproductprice']=number_format($totalPriced,2);
					$amountdetails['dutiesAmount']=number_format($dutiesAmountd,2);
					$amountdetails['adminCharges']=number_format($adminCharged,2);
					$amountdetails['shippingAmount']=number_format($shippingAmountd,2);
					$amountdetails['discountAmount']=number_format($discountAmountd,2);
					$amountdetails['totalAmount']=number_format($totalAmountd,2);
					//$amountdetails['orderStatus']=$orderstatus;
					$amountdetails['paymentStatus']=$orders['paymentStatus'];
					
					$i++;
				}
				
				$res_array['productDetails'] =$productDetails;
				$res_array['amountdetails']=$amountdetails;
				
				//For shipping detail
				$deliverydetails =array();
				$deliveryOption = $orders['deliveryOption'] == 'p'?'Pick Point':'Door To Door Delivery';
				$shipping="";

				if($orders['deliveryStatus']=='s')
				{
					$shipping="Shipped";
				}elseif($orders['deliveryStatus']=='d')
				{
					$shipping ="Delivered";
				}elseif($orders['deliveryStatus']=='p')
				{
					$shipping ="Pending";
				}elseif($orders['deliveryStatus']=='r')
				{
					$shipping="Returned";
				}
				$address="";
				$address = getDeliveryAddress_api($userId,$orders['deliveryOption'],$orders['pick_point']);
				if($orders['deliveryOption'] == 'p'){
					$addressTitle = 'Pick point address';
				}else if($orders['deliveryOption'] == 'd'){
					$addressTitle = 'Delivery address';
				}
					//For return button
					$isReturnable = false;
					if($orders['deliveryStatus'] != 'r' && $orders['paymentStatus'] == 'y' && $orders['deliveryStatus']=='d'){
						$isReturnable = true;
					}

				//For delivery days
				$deliveryDays = getDeliveryDays($orders['deliveryOption'],$userId,$orders['pick_point']);
				$deliverydetails['isReturnable']=$isReturnable;
				$deliverydetails['deliveryDays']=$deliveryDays;
				$deliverydetails['deliveryOption']=$deliveryOption;
				$deliverydetails['deliveryAddress']=$address;
				$deliverydetails['shippingStatus']=$shipping;
				$res_array['deliverydetails']=$deliverydetails;
		 		$status=true;
		 		$message="success";
		 	}else
		 	{
		 		$status =false;
				$message ='Transaction id not valid.';
		 	}
	 }else
	 {
	 	$status =false;
		$message ='Invalid transaction id.';
	 }

	}else
	{
		$status =false;
		$message ='Invalid user id.';
	}
	$res_array['currencySign']=$currencySign;
	$res_array['currencyCode']=$curCode;
	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);
}
function currencyList_API()
{
		global $db; 
		$res = $db->pdoQuery("select code,currency,sign from tbl_currency where isactive='y'")->results();
		$res_array['currencylsit']=$res;
		$res_array['status']=true;
		$res_array['message']="success";
		return json_encode($res_array);
}	
//total payable amount  for custom order
function custom_cart_totalpay_amount_api($userId,$orderId,$curCode)
{
	global $db;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0){
		$query = "SELECT o.id orderId,o.productPrice,o.quantity,o.discountAmount,o.productName FROM tbl_custom_orders as o where o.paymentStatus = 'n' AND o.order_status='a' AND  o.userId = ".$userId." and o.id =". $orderId ."";
		
		$result = $db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$totalRow =$result->affectedRows();
 		if($totalRow<=0){
			
			$status=false;
			$message ='Active order not available.';//$message="Cart empty.";
		
		}else{
			$items =array();
			$i=0;
			foreach($fetchRes as $value){
				$orderId =$value['orderId'];
				$quantity =isset($value['quantity'])?$value['quantity']:0;
				$productPrice = isset($value['productPrice'])?$value['productPrice']:0;
				
				$totalAmount = $productPrice *(isset($value['quantity'])?$value['quantity']:0);
				$productName= isset($value['productName'])?$value['productName']:0;
								
				$items[$i]['orderId']=$orderId;
				$items[$i]['productName']=$productName;
				$items[$i]['quantity']=$quantity;
				$items[$i]['productPrice']= number_format(convertCurrency($currencyId,$productPrice),2);
				$items[$i]['totalAmount']=number_format(convertCurrency($currencyId,$totalAmount),2);
				$i++;
			}
			$res_array['items']=$items;	
		}
		$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_custom_orders where paymentStatus = 'n' AND order_status='a' AND userId = ".$userId." and id =".$orderId."")->results();
			$finalAmount=0;
				foreach ($fetchRes as $key => $orders) 
				{
					$totalPrice += $orders['productPrice'] * $orders['quantity'];
					$dutiesAmount += $orders['dutiesAmount'];
					$adminCharge += $orders['adminCharge'];
					$shippingAmount += $orders['shippingAmount'];
					$discountAmount += $orders['discountAmount'];
				
				}
				$finalAmount =	($totalPrice+$dutiesAmount+$adminCharge+$shippingAmount)-$discountAmount;
				if($finalAmount>0){
					$res_array['totalPrice']=number_format(convertCurrency($currencyId,$totalPrice),2);
					$res_array['dutiesAmount']=number_format(convertCurrency($currencyId,$dutiesAmount),2);
					$res_array['adminCharge']=number_format(convertCurrency($currencyId,$adminCharge),2);
					$res_array['shippingAmount']=number_format(convertCurrency($currencyId,$shippingAmount),2);
					$res_array['discountAmount']=number_format(convertCurrency($currencyId,$discountAmount),2);
					$res_array['finalAmount']=number_format(convertCurrency($currencyId,$finalAmount),2);
					$res_array['finalAmountInDoller']=(string)round(isset($finalAmount)?$finalAmount:'0.00',2);
					$res_array['currencyCode']=$curCode;
					$res_array['currencySign']= $currencySign;
					$purchaseAproval =getTableValue('tbl_users',"buyStatus",array('id'=>$userId));
				
					$res_array['purchaseAproval']=$purchaseAproval;
					
					$status=true;
					$message="success";
				}else
				{
					$status=false;
					$message="doen't found any data,please check order.";
				}
	}else
	{
				$status=false;
				$message="Invalid user id and order id.";
	}

		$res_array['status']=$status;
		$res_array['message']=$message;
		return json_encode($res_array);
}
//total payable amount for cart
function cart_totalpay_amount_api($userId,$curCode)
{
	global $db;
	$currencySignn = getTableValue('tbl_currency','sign',array('code'=>$curCode));
	$currencySign =empty($currencySignn)?$currencySign:$currencySignn;	
	$currencyId = getTableValue('tbl_currency','id',array('sign'=>$currencySign));
	if($userId>0)
	{
		
		$query = "SELECT o.*,p.productName,p.weight,p.quantity as `maxQuantity` FROM tbl_orders as o 
					  LEFT JOIN tbl_product_deals as p ON(p.id = o.productId)
					  where o.paymentStatus = 'n' AND o.userId = ".$userId."";
		
		$result = $db->pdoQuery($query);
 		$fetchRes = $result->results();
 		$totalRow =$result->affectedRows();
 		if($totalRow<=0){
			
			$status=false;
			$message="pending order not found.";
			
		}else{
			$items =array();
			$i=0;
			$outofstockitem= array();
			$oi=0;
			foreach($fetchRes as $value){
				$orderId =$value['orderId'];
				$productPrice = isset($value['productPrice'])?$value['productPrice']:0;
				$quantity =isset($value['quantity'])?$value['quantity']:0;

				$totalAmount =$productPrice * $quantity;
				$productName= isset($value['productName'])?$value['productName']:0;
				$maxqty =isset($value['maxQuantity'])?$value['maxQuantity']:0;
				if($quantity>$maxqty)
				{
						$outofstockitem[$oi]['orderId']=$orderId;
						$outofstockitem[$oi]['productName']=$productName;
						$oi++;
				}
				$items[$i]['orderId']=$orderId;
				$items[$i]['productName']=$productName;
				$items[$i]['quantity']=$quantity;
				$items[$i]['productPrice']=number_format(convertCurrency($currencyId,$productPrice),2);
				$items[$i]['totalAmount']=number_format(convertCurrency($currencyId,$totalAmount),2);
				$i++;
		}
			$res_array['items'] =$items;
			$res_array['outofstockitem']=$outofstockitem;	
		
		}			
		$fetchRes = $db->pdoQuery("select id,productPrice,shippingAmount,dutiesAmount,adminCharge,quantity,discountAmount from tbl_orders where paymentStatus = 'n'  AND userId = ".$userId."")->results();
				$finalAmount=0;
				foreach ($fetchRes as $key => $orders) 
				{
					$totalPrice += $orders['productPrice'] * $orders['quantity'];
					$dutiesAmount += $orders['dutiesAmount'];
					$adminCharge += $orders['adminCharge'];
					$shippingAmount += $orders['shippingAmount'];
					$discountAmount += $orders['discountAmount'];
				}
				$finalAmount =	($totalPrice+$dutiesAmount+$adminCharge+$shippingAmount)-$discountAmount;
				
				if($finalAmount>0){
					$res_array['totalPrice']=number_format(convertCurrency($currencyId,$totalPrice),2);
					$res_array['dutiesAmount']=number_format(convertCurrency($currencyId,$dutiesAmount),2);
					$res_array['adminCharge'] =number_format(convertCurrency($currencyId,$adminCharge),2);
					$res_array['shippingAmount']=number_format(convertCurrency($currencyId,$shippingAmount),2);
					$res_array['discountAmount']=number_format(convertCurrency($currencyId,$discountAmount),2);
					$res_array['finalAmount']=number_format(convertCurrency($currencyId,$finalAmount),2);
					$res_array['finalAmountInDoller']=(string) round(isset($finalAmount)?$finalAmount:'0.00',2);
					$res_array['currencyCode']=$curCode;
					$res_array['currencySign']= $currencySign;
					$status=true;
					$message="success";
					$purchaseAproval =getTableValue('tbl_users',"buyStatus",array('id'=>$userId));
					
					$res_array['purchaseAproval']=$purchaseAproval;
					
				}else
				{
					$status=false;
					$message="doen't found any data,please check order.";
				}
	}else
	{
				$status=false;
				$message="Invalid user id.";
	}

		$res_array['status']=$status;
		$res_array['message']=$message;
		return json_encode($res_array);
}
//add cart to store product
function add_store_cart_api($userId,$storeId,$storecart,$categoryId,$subcategoryId)
{
	global $db;
	
	if($userId>0)
	{
		if($storeId>0)
		{
			if(count($storecart)>0)
			{
				$cartproduct =$storecart['product'];
				foreach ($cartproduct as $item) {
					//add store product in product deal table 
					$strsource=array('\t','\n');
					$strreplace =array(' ',' ');
					$data =new stdClass();
					$data->productName =str_replace($strsource, $strreplace, $item['name']);
					$data->quantity = intval($item['quantity']);
					$data->actualPrice = $item['price'];
					$data->categoryId=$categoryId;
					$data->subcategoryId=$subcategoryId;
					$data->isDiscount= 'n';
					$data->isActive ='y';
					$data->createdDate = date('Y-m-d H:i:s');
					$data->productType ='s';
					$data->storeId=intval($storeId);
					$id=$db->insert("tbl_product_deals",(array)$data)->getLastInsertId();

					//add product to cart
					$cartdata = array();
					$cartdata['userId']=intval($userId);
					$cartdata['productId']=$id;
					$cartdata['quantity']=intval($item['quantity']);
					$cartdata['createddate']=date('Y-m-d H:i:s');
					$db->insert("tbl_cart", $cartdata);

					//For remote images save to local
					
					 $uploads_dir = DIR_UPD."product/".$id;
				     if(!file_exists($uploads_dir)){
					 	mkdir($uploads_dir,0777,true);
					 }	
					 if($item['image']!='')
					 {
					 	//$image = file_get_contents($item['image']);
					 	$image=getImageFromStore($item['image']);
					 	$ext =pathinfo($item['image'],PATHINFO_EXTENSION);
					 	$filename =time().".".$ext;
					 	//file_put_contents($uploads_dir."/".$filename, $image);
					 	$f = fopen($uploads_dir."/".$filename, 'w');
						fwrite($f, $image);
						fclose($f);
					 }
					 //add store images in product image table
					 $dataimage =array();
					 $dataimage['productId']=$id;
					 $dataimage['name']=$filename;
					 $dataimage['createdDate']=date('Y-m-d H:i:s');
					 $db->insert('tbl_product_image',$dataimage);
				}

				$status=true;
				$message="Item added successfully.";
			}else
			{
				$status=false;
				$message="Empty cart not allowed.";				
			}
		}else
		{
			$status=false;
			$message="Invalid store id.";		
		}
	}else
	{
		$status=false;
		$message="Invalid user id.";
	}

	$res_array['status']=$status;
	$res_array['message']=$message;

	return json_encode($res_array);

}
//get admin paypal information
function adminpaypal_API()
{
		global $db;
		$row=$db->pdoQuery("select `value` from tbl_site_settings where constant='ADMIN_PAYPAL'")->result();
		$row1=$db->pdoQuery("select `value` from tbl_site_settings where constant='PAYPAL_URL'")->result();
		$row2=$db->pdoQuery("select `value` from tbl_site_settings where constant='PAYPAL_CLIENT_ID'")->result();
		
		$adminpaypal =$row['value'];
		$paypalurl=$row1['value'];
		$paypalClientId=$row2['value'];
		
		$res_array['adminpaypal'] =$adminpaypal;
		$res_array['paypalurl']=$paypalurl;
		$res_array['paypalClientId']=$paypalClientId;
		
		$res_array['status']=true;
		$res_array['message']='success';
		return json_encode($res_array);
}
?>