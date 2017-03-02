<?php
class SubAdmin extends Home {

    public $page_name;
    public $page_title;
    public $meta_keyword;
    public $meta_desc;
    public $page_desc;
    public $isActive;

    public $data = array();

    public function __construct($id=0, $searchArray=array(), $type='') {
        $this->data['id'] = $this->id = $id;
        $this->table = 'tbl_admin';
        $this->type = ($this->id > 0 ? 'edit' : 'add');
        $this->searchArray = $searchArray;
        parent::__construct();

        if($this->id>0){
            $qrySel = $this->db->pdoQuery("SELECT id,uName,uEmail,uPass,ipAddress,adminType,status
                                            FROM tbl_admin
                                            WHERE id=?",array($this->id))->result();
            $fetchRes = $qrySel;

            $this->data['uName'] = $this->uName = $fetchRes['uName'];
            $this->data['status'] = $this->status = $fetchRes['status'];
            $this->data['uEmail'] = $this->uEmail = $fetchRes['uEmail'];
            $this->data['uPass'] = $this->uPass = '';
            $this->data['ipAddress'] = $this->ipAddress = $fetchRes['ipAddress'];
            $this->data['adminType'] = $this->adminType = $fetchRes['adminType'];

            $qrySel_permission = $this->db->pdoQuery("SELECT ap.permission,ar.pagenm
                                            FROM tbl_admin_permission as ap LEFT JOIN tbl_adminrole as ar ON ar.id=ap.page_id
                                            WHERE admin_id=?",array($this->id))->results();
            foreach($qrySel_permission as $fetchRes_permission){
                $this->data[$fetchRes_permission['pagenm']] = $this->$fetchRes_permission['pagenm'] = explode(',',$fetchRes_permission['permission']);
            }
        }else{
            $this->data['uName'] = $this->uName = '';
            $this->data['uEmail'] = $this->uEmail = '';
            $this->data['uPass'] = $this->uPass = '';
            $this->data['ipAddress'] = $this->ipAddress = '';
            $this->data['adminType'] = $this->adminType = '';
        }
        switch($type){
            case 'add' : {
                $this->data['content'] = (in_array('add',$this->Permission))?$this->getForm():'';
                break;
            }
            case 'edit' : {
                $this->data['content'] = (in_array('edit',$this->Permission))?$this->getForm():'';
                break;
            }
            case 'view' : {
                $this->data['content'] = (in_array('view',$this->Permission))?$this->viewForm():'';
                break;
            }
            case 'view_activity' : {
                $this->data['content'] =  (in_array('view',$this->Permission))?$this->viewActivityForm():'';
                break;
            }
            case 'activity_datagrid' : {
                $this->data['content'] =  (in_array('view',$this->Permission))?json_encode($this->activity_datagrid()):'';
                break;
            }
            case 'delete' : {
                $this->data['content'] =  (in_array('delete',$this->Permission))?json_encode($this->dataGrid()):'';
                break;
            }
            case 'datagrid': {
                $this->data['content'] =  (in_array('module',$this->Permission))?json_encode($this->dataGrid()):'';
            }
        }

    }
    public function viewForm(){
        $con = '';
        $query = $this->db->pdoQuery("SELECT a.constant,ar.title,ar.id from tbl_admin_permission as ap left join tbl_subadmin_action as a on FIND_IN_SET(a.id,ap.permission) left join tbl_adminrole as ar on(ap.page_id=ar.id) where admin_id = ".$this->id." and permission > 0")->results();

        $con .= '<p style="width:300px;">';
        foreach ($query as $fetchRes) {
            $con .= $fetchRes['title'].' : '.$fetchRes['constant'].'</br>';
        }
        $con .= '</p>';


        $content = $this->fields->displayBox(array("label"=>"User Name&nbsp;:","value"=>$this->uName)).
        $this->fields->displayBox(array("label"=>"Email&nbsp;:","value"=>$this->uEmail)).
        $this->fields->displayBox(array("label"=>"Assigned Permissions&nbsp;:","value"=>$con));
        return $content;
    }
    public function viewActivityForm(){

        $main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/view_activity_datatable-nct.tpl.php");
        $main_content=$main_content->parse();
        return $main_content;
    }
    public function getForm() {
        $content = '';
        $content .=    $this->fields->form_start(array("name"=>"frmCont","extraAtt"=>"novalidate='novalidate'")).
            $this->fields->textBox(array("label"=>"".MEND_SIGN."User Name: ","name"=>"txt_uname","class"=>"logintextbox-bg required","value"=>$this->data['uName'])).
            $this->fields->textBox(array("label"=>"".MEND_SIGN."Email: ","name"=>"txt_email","class"=>"logintextbox-bg required","value"=>$this->data['uEmail'])).
            $this->fields->password(array("label"=>"".(($this->type=='add')?MEND_SIGN:"")."Password: ","name"=>"txt_password","class"=>"logintextbox-bg ".(($this->type=="add")?"required":"")."","value"=>$this->data['uPass']));
            if($this->type!='add'){
        $content .=    $this->fields->displayBox(array("label"=>"&nbsp;","name"=>"uname","class"=>"logintextbox-bg hint","value"=>"please fill up password field to change password."));
            }
        $content .=    $this->fields->displayBox(array("label"=>"<b>Modules</b>","name"=>"uname","class"=>"logintextbox-bg","value"=>"<b>Set User Accessibility</b>"));
            $fetchAction=$this->SubadminAction();
            $qryRes= $this->adminPageList();
            $i=0;
            foreach($qryRes as $fetchRes){

            $content .= $this->fields->checkBox(array("label"=>$fetchRes['title'].": ","name"=>'actions['.$fetchRes['pagenm'].'][]',"class"=>"radioBtn-bg chk_".$fetchRes['pagenm']."_".$i." chk_group","value"=>$this->$fetchRes['pagenm'],"values"=>array_intersect($fetchAction,$fetchRes['page_action_id']),"extraAtt"=>"data-page='".$fetchRes['pagenm']."' data-page_id='".$i."' "));
            $i++;
            }

        $content .=    $this->fields->radio(array("label"=>"Status: ","name"=>"status","class"=>"radioBtn-bg required","value"=>($this->status != '' ? $this->status:'y'),"values"=>array("y"=>"Active","n"=>"Inactive")));

        $content .= $this->fields->hidden(array("name"=>"type","class"=>"logintextbox-bg required","value"=>$this->type)).
                    $this->fields->hidden(array("name"=>"id","class"=>"logintextbox-bg required","value"=>$this->id)).
                    $this->fields->buttonpanel_start().
            $this->fields->button(array("onlyField"=>true,"name"=>"submitAddForm", "type"=>"submit", "class"=>"green", "value"=>"Submit", "extraAtt"=>"")).
            $this->fields->button(array("onlyField"=>true,"name"=>"cn", "type"=>"button", "class"=>"btn-toggler", "value"=>"Cancel", "extraAtt"=>"")).
            $this->fields->buttonpanel_end();
            $content .= $this->fields->form_end();

        return sanitize_output($content);
    }


    public function dataGrid() {
        $content = $operation = $whereCond = $totalRow = NULL;
        $result = $tmp_rows = $row_data = array();
        extract($this->searchArray);
        $chr = str_replace(array('_', '%'), array('_', '%'),$chr );
        $whereCond = ' where adminType!="s" and id!="'.$this->adminUserId.'" and status!="t"';

        if(isset($chr) && $chr != '') {
            $whereCond .= " and (uName LIKE '%".$chr."%')";
        }

        if(isset($sort))
            $sorting = $sort.' '. $order;
        else
            $sorting = 'id DESC';
        
        $qry = "SELECT `id`, `uName`, `uEmail`, `adminType`, `created_date`, `updated_date`, `status`
                                       FROM tbl_admin
                                       ".$whereCond;
        
        $qrySel1 = $this->db->pdoQuery($qry." order by ".$sorting."")->results();

        $qrySel = $this->db->pdoQuery($qry." order by ".$sorting." limit ".$offset." ,".$rows." ")->results();

        $totalRow = count($qrySel1);
        
        foreach($qrySel as $fetchRes) {
            $status = ($fetchRes['status']=="y") ? "checked" : "";
            $switch  =$this->fields->toggel_switch(array("action"=>"ajax.".$this->module.".php?id=".$fetchRes['id']."","check"=>$status));
            $operation = (in_array('edit',$this->Permission))?$this->fields->link(array("href"=>"ajax.".$this->module.".php?action=edit&id=".$fetchRes['id']."","class"=>"btn default btn-xs black btnEdit","value"=>'<i class="fa fa-edit"></i>&nbsp;Edit')):'';
            $operation .=(in_array('delete',$this->Permission))?'&nbsp;&nbsp;'.$this->fields->link(array("href"=>"ajax.".$this->module.".php?action=delete&id=".$fetchRes['id']."","class"=>"btn default btn-xs red btn-delete","value"=>'<i class="fa fa-trash-o"></i>&nbsp;Delete')):'';

            $operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->fields->link(array("href"=>"ajax.".$this->module.".php?action=view&id=".$fetchRes['id']."","class"=>"btn default blue btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View')):'';
        // View ctivity
        //    $operation .=(in_array('view',$this->Permission))?'&nbsp;&nbsp;'.$this->fields->link(array("href"=>"ajax.".$this->module.".php?action=view_activity&id=".$fetchRes['id']."","class"=>"btn btn-warning btn-xs btn-viewbtn","value"=>'<i class="fa fa-laptop"></i>&nbsp;View Activity')):'';

            $uName = (isset($fetchRes["uName"]) && $fetchRes["uName"]!='')?$fetchRes["uName"]:'N/A';
            $uEmail = (isset($fetchRes["uEmail"])  && $fetchRes["uEmail"]!='')?$fetchRes["uEmail"]:'N/A';
            $created_date = (isset($fetchRes["created_date"])  && $fetchRes["created_date"]!='')?$fetchRes["created_date"]:'N/A';
            $updated_date = (isset($fetchRes["updated_date"])  && $fetchRes["updated_date"]!='')?$fetchRes["updated_date"]:'N/A';
            $membership_plan =(isset($fetchRes["title"])  && $fetchRes["title"]!='')?$fetchRes["title"]:'N/A';
            $final_array =  array($uName,$uEmail,$created_date,$updated_date);
            if(in_array('status',$this->Permission)){
                $final_array =  array_merge($final_array, array($switch));
            }
            if(in_array('edit',$this->Permission) || in_array('delete',$this->Permission) || in_array('view',$this->Permission) ){
                $final_array =  array_merge($final_array, array($operation));
            }
            $row_data[] = $final_array;
        }
        $result["sEcho"]=$sEcho;
        $result["iTotalRecords"] = (int)$totalRow;
        $result["iTotalDisplayRecords"] = (int)$totalRow;
        $result["aaData"] = $row_data;
        return $result;

    }
    public function activity_datagrid() {

        $content = $operation = $whereCond = $totalRow = NULL;
        $result = $tmp_rows = $row_data = array();
        extract($this->searchArray);

        $qryRes=$this->db->pdoQuery("SELECT a1.uName,sa.title,sa.constant,ar.title as page_title,ar.pagenm,ar.table_name,ar.table_field,a.created_date,a.entity_id,a.entity_action
                                    FROM tbl_admin_activity as a
                                    LEFT JOIN tbl_subadmin_action as sa ON sa.id=a.activity_type
                                    LEFT JOIN tbl_adminrole as ar ON ar.id=a.page_id
                                    LEFT JOIN tbl_admin as a1 ON a1.id=a.admin_id
                                    WHERE a.admin_id='".$this->id."' ORDER BY a.id DESC  limit ".$offset." ,".$rows."")->results();
        $totalRow = count($qryRes);
        foreach($qryRes as $fetchRes) {

            $static_action = array('import','export');
            $message = (in_array($fetchRes['constant'],$static_action))?'Records ':'Record ';
            if($fetchRes['table_name']!="" && $fetchRes['table_field']!="" && $fetchRes['pagenm']!='sitesetting-nct' && !in_array($fetchRes['constant'],$static_action)){
            /*    $qryRes_sub=$this->db->pdoQuery("SHOW KEYS FROM ".$fetchRes['table_name']." WHERE Key_name =  'PRIMARY'")->showQuery();
                */
                $message = "\"".getTableValue($fetchRes['table_name'],$fetchRes['table_field'],array("id"=>$fetchRes['entity_id'])).'" record ';
            }

            $from_array = array('delete','view','status','export');

            $constant = $fetchRes['constant'];
            $fetchRes['constant'] = ($fetchRes['constant']=='status')?(($fetchRes['entity_action']=='a')?'activate':'deactivate'):$fetchRes['constant'];
            $message .= $fetchRes['constant'].'ed '.((in_array($fetchRes['constant'],$from_array))?'from':'to').' ';
            $message .= $fetchRes['page_title'].' module';

            $message=str_replace(array('deleteed','activateed','deactivateed'),array('deleted','activated','deactivated'),$message);

            $row_data[] = array($message,$fetchRes['created_date']);
        }

        $result["sEcho"]=$sEcho;
        $result["iTotalRecords"] = (int)$totalRow;
        $result["iTotalDisplayRecords"] = (int)$totalRow;
        $result["aaData"] = $row_data;
        return $result;

    }
    public function getPageContent(){
        $final_result = NULL;
        $main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
        $main_content->breadcrumb = $this->getBreadcrumb();
        $main_content->getForm = $this->getForm();
        $final_result = $main_content->parse();
        return $final_result;
    }
}