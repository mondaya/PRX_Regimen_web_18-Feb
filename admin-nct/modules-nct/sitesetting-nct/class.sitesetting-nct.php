<?php
class SiteSetting extends Home {
	function __construct() {
		parent::__construct();
		$this->table = 'tbl_site_settings';
	}
	public function _index() {
		$content = '';
		$sqlSetting = $this->db->select($this->table,"*")->results();

			foreach($sqlSetting as $k=>$setrow)
			{
		
				$required='';
				$mend_sign='';
				
				if($setrow["type"]=="filebox" && $setrow["value"]=="")
				{
					$required = "required ";
					$mend_sign = MEND_SIGN;
				}
				if($setrow["type"]=="filebox" && !empty($setrow["value"]))
				{	
					$setrow["value"]=$this->fields->img(array("onlyField"=>true,"src"=>"".SITE_IMG.$setrow["value"]."", "width"=>"".(($setrow["constant"]=="SITE_FAVICON")?"50px":"200px").""));
				}
				
				if($setrow["required"]==1){$required = "required ";$mend_sign = MEND_SIGN;}
				
				$content.=$this->$setrow["type"](array("label"=>$mend_sign.$setrow["label"].":","value"=>$setrow["value"],"class"=>$required.$setrow["class"],"name"=>$setrow["id"]));
								
				if(!empty($setrow['hint'])){
					$content.=$this->fields->displayBox(array("label"=>"&nbsp;","value"=>$setrow['hint']));
				}
			}

			$content.=$this->fields->buttonpanel_start().
			$this->fields->button(array("onlyField"=>true,"name"=>"submitSetForm", "type"=>"submit", "class"=>"green", "value"=>"Submit", "extraAtt"=>"")).
			$this->fields->button(array("onlyField"=>true,"name"=>"cn", "type"=>"button", "class"=>"btn-toggler", "value"=>"Cancel", "extraAtt"=>"onclick=\"location.href='".SITE_ADM_MOD."home-nct/'\"")).
			$this->fields->buttonpanel_end();
			
		return $content;
	}
	public function textbox($text){
		
		$text['label'] = isset($text['label']) ? $text['label'] : 'Enter Text Here: ';
		$text['value'] = isset($text['value']) ? $text['value'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
		$text['class'] = isset($text['class']) ? 'form-control '.trim($text['class']) : 'form-control';
        $text['extraAtt'] = isset($text['extraAtt']) ? $text['extraAtt'] : '';

		$content = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/textbox-nct.tpl.php");
		$main_content = $main_content->parse();
		
		$fields = array("%CLASS%","%NAME%","%ID%","%VALUE%","%EXTRA%","%LABEL%");
		$fields_replace = array($text['class'],$text['name'],$text['name'],$text['value'],$text['extraAtt'],$text['label']);
		$content=str_replace($fields,$fields_replace,$main_content);
		return $content;

	}
	public function filebox($text){
		
		$text['label'] = isset($text['label']) ? $text['label'] : 'Enter Text Here: ';
		$text['value'] = isset($text['value']) ? $text['value'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
		$text['class'] = isset($text['class']) ? 'form-control '.trim($text['class']) : 'form-control';
        $text['extraAtt'] = isset($text['extraAtt']) ? $text['extraAtt'] : '';
		$text["help"] = isset($text["help"])?$text["help"]:"";
		$text["helptext"] = ($text["help"]!="")?'<p class="help-block">'.$text["help"].'</p>':"";

		$content = NULL;
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/filebox-nct.tpl.php");
		$main_content = $main_content->parse();
		
		$fields = array("%CLASS%","%NAME%","%ID%","%VALUE%","%EXTRA%","%LABEL%","%HELPTEXT%");
		$fields_replace = array($text['class'],$text['name'],$text['name'],$text['value'],$text['extraAtt'],$text['label'],$text["helptext"]);

		$content=str_replace($fields,$fields_replace,$main_content);
		return $content;

	}
	public function textArea($text){
        $text['label'] = isset($text['label']) ? $text['label'] : 'Enter Password Here: ';
		$text['value'] = isset($text['value']) ? $text['value'] : '';
		$text['name'] = isset($text['name']) ? $text['name'] : '';
		$text['class'] = isset($text['class']) ?"form-control ".$text['class'] : 'form-control';
        $text['extraAtt'] = isset($text['extraAtt']) ? ' '.$text['extraAtt'] : '';
        $text['onlyField'] = isset($text['onlyField']) ? $text['onlyField'] : false;
        
       if($text["onlyField"]==true){
			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module.'/textarea_onlyfield.tpl.php');
        }
        else{
			$main_content = new Templater(DIR_ADMIN_TMPL.$this->module.'/textarea.tpl.php');
        }
		$main_content=$main_content->parse();
		$fields = array("%CLASS%","%NAME%","%ID%","%VALUE%","%EXTRA%","%LABEL%");
		$fields_replace = array($text['class'],$text['name'],$text['name'],$text['value'],$text['extraAtt'],$text['label']);
		return str_replace($fields,$fields_replace,$main_content);
		
	}
	public function getPageContent(){
		$final_result = NULL;
		
		$main_content = new Templater(DIR_ADMIN_TMPL.$this->module."/".$this->module.".tpl.php");
		$main_content->breadcrumb = $this->getBreadcrumb();
		$main_content->getForm = $this->_index();
		
		$final_result = $main_content->parse();
		return $final_result;
	}
}
?>  