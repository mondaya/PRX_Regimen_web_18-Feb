<?php
	$reqAuth = true;
	require_once("../../includes-nct/config-nct.php");
	require_once("class.customOrder.php");
	
	$searchText = isset($_GET["searchText"]) && $_GET["searchText"]!='-' ? $_GET["searchText"] : (isset($_POST["searchText"]) && $_POST["searchText"]!='-'? $_POST["searchText"] : '');
	$searchText = str_replace("'",'',$searchText);
	
	$cateId = isset($_POST["cateId"]) && $_POST["cateId"]!='' ? $_POST["cateId"] : (isset($_GET["cateId"]) && $_GET["cateId"]!=''? $_GET["cateId"] : 0);
	$subCateId = isset($_POST["subCateId"]) && $_POST["subCateId"]!='' ? $_POST["subCateId"] : (isset($_GET["subCateId"]) && $_GET["subCateId"]!=''? $_GET["subCateId"] : 0);
	$page = isset($_POST['page']) && $_POST['page']!=''?$_POST['page']:1;

	$module = 'customOrder-nct';
	$table = 'tbl_product_deals';
	
	$winTitle = 'Place custom order -' .SITE_NM;
    $headTitle = 'Place custom order';

    $metaTag = getMetaTags(array("description"=>$winTitle,
			"keywords"=>$headTitle,
			"author"=>SITE_NM));

    if(isset($_POST['orderSubmit'])){
    	//echo '<pre>';
    	//print_r($_POST);exit;
    	extract($_POST);
    	$countryId='';
        if(!empty($productName) && !empty($productUrl) && !empty($productPrice) && !empty($quantity) && !empty($size) && !empty($color)){
    			$i = 0;
    			foreach ($productName as $product) {
    				
                    $data['userId'] = $sessUserId;
                    $data['orderId'] = 'ORDER'.time().strtoupper(genrateRandom(4));
    				$data['productName'] = $productName[$i];
    				$data['productUrl'] = $productUrl[$i];
    				$data['productPrice'] = $productPrice[$i];
    				$data['quantity'] = $quantity[$i];
    				$data['size'] = $size[$i];
    				$data['color'] = $color[$i];
                    $totalProductPrice = $productPrice[$i] * $quantity[$i];
                    $dutiesAmount = getDutiesAmount($totalProductPrice,$countryId);
                    $adminCharge = getAdminCharge($totalProductPrice,$countryId);
                    $data['dutiesAmount'] = strval($dutiesAmount);
                    $data['adminCharge'] = strval($adminCharge);
                    $data['createdDate'] = date('Y-m-d H:i:s');
                    
    				$db->insert('tbl_custom_orders',$data);
    				$i++;
    			}

    			$_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'Order placed successfully'));
    			redirectPage(SITE_URL.'myCustomOrder');

    	}
    }
		
	
	
	$mainObj = new customOrder($module);
	

	$pageContent = $mainObj->getPageContent();
 	
 	$fields = array(
 	);

	$fields_replace = array(
	);

	$pageContent=str_replace($fields,$fields_replace,$pageContent);
	
	require_once(DIR_TMPL."parsing-nct.tpl.php");	
?>