<?php
    
    $reqAuth=true;
    require_once("../../../includes-nct/config-nct.php");
    require_once("class.sitesetting-nct.php");
        $objPost = new stdClass();
	$winTitle = 'Site Settings - '.SITE_NM;
    $headTitle = 'Site Settings';
    $metaTag = getMetaTags(array("description"=>"Admin Panel",
        "keywords"=>'Admin Panel',
        "author"=>SITE_NM));
    $breadcrumb = array("Site Settings");    
    $module = 'sitesetting-nct';
    if(isset($_FILES) && !empty($_FILES)){
        foreach($_FILES as $a=>$b)
        {
            $selField = array('type');
            $selWhere = array('id'=>$a);
            $type1Sql = $db->select("tbl_site_settings",$selField,$selWhere)->results();
            foreach($type1Sql as $c=>$b)
            {
                $type1=$b["type"];
                
            }
            if($type1=="filebox")
            {
                $type = $_FILES[$a]["type"];
                $fileName = $_FILES[$a]["name"];
                $TmpName = $_FILES[$a]["tmp_name"];
                if($type=="image/jpeg" || $type=="image/png" || $type=="image/gif" || $type=="image/x-png" || $type=="image/jpg" || $type=="image/x-png" || $type=="image/x-jpeg" || $type=="image/pjpeg")
                {
                    
                    $fileName=GenerateThumbnail($fileName,DIR_IMG,$TmpName,array(array('height'=>53,'width'=>240)));
                    $dataArr = array("value"=>$fileName);
                    $dataWhere = array("id"=>$a);
                    $db->update('tbl_site_settings', $dataArr, $dataWhere);

//                    move_uploaded_file($TmpName,DIR_IMG.$fileName);
                }
            }
        }
    }
    if(isset($_POST["submitSetForm"]))
    {
        extract($_POST);
        foreach($_POST as $k=>$v)
        {
            if((int)$k)
            {
                if($v!="")
                {
                    $v=closetags($v);
                    $sData = array("value"=>$v);
                    $sWhere = array("id"=>$k);
                    $db->update("tbl_site_settings",$sData,$sWhere);
                    if($k==2){
                        $data = array("uEmail"=>$v);
                        $where = array("id"=>"1","adminType"=>"s");
                        $db->update("tbl_admin",$data,$where);
                    }
                }
            }
        }
        $activity_array = array("id"=>"1","module"=>$module,"activity"=>'edit');
        add_admin_activity($activity_array);
        $_SESSION["msgType"] = disMessage(array('type'=>'suc','var'=>'recEdited'));
        redirectPage(SITE_ADM_MOD.$module);
    }

    chkPermission($module);
    $objUser = new SiteSetting();    
    //extract($objUser->data);
    
    $pageContent = $objUser->getPageContent();
    require_once(DIR_ADMIN_TMPL."parsing-nct.tpl.php");
?>