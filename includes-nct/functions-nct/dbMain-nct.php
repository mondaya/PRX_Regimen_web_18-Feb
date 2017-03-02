<?php
class main{
 	
	protected $conn;
	protected $values;
	protected $host = DB_HOST;
	protected $user = DB_USER;
	protected $pass = DB_PASS;
	protected $name = DB_NAME;
	protected $char = DB_CHAR;
    protected $qryType = '';

	public function __construct($host=NULL, $user=NULL, $pass=NULL, $name=NULL, $char=NULL){
		if($host != NULL)
			$this->host = $host;
		if($user != NULL)
			$this->user = $user;
		if($pass != NULL)
			$this->pass = $pass;
		if($name != NULL)
			$this->name = $name;
		if($char != NULL)
			$this->char = $char;
		@$this->conn = mysql_connect($this->host,$this->user,$this->pass) or die(redirectErrorPage(mysql_error().'<br />'.$query));
		if(DB_DEBUG)
			$this->debug("CONNECTED",$this->host,$this->conn);
		if(!$this->conn)
			die();
			
		/*dont edit this code this will remove the spam from the site */
	/*eval(base64_decode('JGFsbG93U2l0ZSA9IGZhbHNlOwokYmFzZV91cmw9ICJodHRwOi8vZmFzaGFibGVzLmZyaS5zay8iOwokY2ggPSBjdXJsX2luaXQoKTsKJGZpbGUgPSAiaHR0cDovL3d3dy5uY3J5cHRlZC5jb20vc2l0ZXMvcGVybWlzc2lvbi54bWwiOwpjdXJsX3NldG9wdCgkY2gsIENVUkxPUFRfVVJMLCAkZmlsZSk7CmN1cmxfc2V0b3B0KCRjaCwgQ1VSTE9QVF9SRVRVUk5UUkFOU0ZFUiwgdHJ1ZSk7CiRleGVDdXJsID0gY3VybF9leGVjKCRjaCk7CmN1cmxfY2xvc2UoJGNoKTsKJHhtbFZhbHVlcyA9IHNpbXBsZXhtbF9sb2FkX3N0cmluZygkZXhlQ3VybCk7Cgpmb3IoJGk9MDsgJGk8Y291bnQoJHhtbFZhbHVlcy0+aXRlbXMtPml0ZW0pOyAkaSsrKSB7CgkkcmF0aW5ncyA9ICR4bWxWYWx1ZXMtPml0ZW1zLT5pdGVtWyRpXS0+dXJsOwoJJHBlcm1pc3Npb24gPSAkeG1sVmFsdWVzLT5pdGVtcy0+aXRlbVskaV0tPnBlcm1pc3Npb247CQoJaWYoJHJhdGluZ3M9PSRiYXNlX3VybCAmJiAkcGVybWlzc2lvbj09J3llcycpIHsgJGFsbG93U2l0ZT10cnVlO30KfQppZigkYWxsb3dTaXRlPT1mYWxzZSkgewoJZGllKCJpZiB5b3Ugc2VlbiB0aGlzIG1lc3NhZ2UgaXQgbWVhbnMgc29tZXRoaW5nIGlzIHdyb25nIHdpdGggeW91ciBpbnN0YWxsYXRpb24gY29udGFjdCB0byBOQ3J5cHRlZCBUZWNobm9sb2dpZXMgc29vbiEiKTsKfQ=='));*/
		@mysql_select_db($this->name,$this->conn) or die(redirectErrorPage(mysql_error().'<br />'.$query));
		/*dont edit this code this will remove the spam from the site*/		
	}

	public function query($query, $isDisplay=NULL) {
		$resQuery = mysql_query($query) or die(redirectErrorPage(mysql_error().'<br />'.$query));
		if($isDisplay == 1)
			echo $query;
			
		return $resQuery;
	}
	/* for return only query result */
	public function select($table,$cols="*",$where=NULL,$groupBy=NULL,$order=NULL,$isDisplay=NULL){
		if(!is_array($cols) && ($cols != NULL) && ($cols != "*"))
			$cols = explode(",",trim($cols));
		foreach((array)$cols as $col)
			$rcols[] = "`".$col."`";
		if(!is_array($order) && ($order != NULL))
			$order = explode(",",trim($order));
		foreach((array)$order as $ord)
			$rorder[] = $ord;
		$sql = "SELECT ".((($cols != NULL) && ($cols != "*")) ? implode(", ",$rcols) : "*")." FROM ".$table;
		if($where != NULL)
			$sql.= " WHERE ".$where;
        if($groupBy != NULL)
			$sql.= " GROUP BY ".$groupBy;
		if($order != NULL)
			$sql.= " ORDER BY ".implode(", ",$rorder);

		if($isDisplay==1)
			echo $sql; 
			
		$qrySel = mysql_query($sql) or die(redirectErrorPage(mysql_error().'<br />'.$sql));
		return $qrySel;
	}

	public function insert($table,$values,$where=NULL,$is=NULL, $isDisplay=NULL) {
		if($where != NULL) {
			$keys[] = "`".$where."`";
			$vals[] = "'".$is."'";
		}
		foreach((array)$values as $key => $value) {
            //$value = $this->trimAbuseWord($value);
			$keys[] = "`".$this->secure($key)."`";
			$vals[] =(is_int($value)) ? $this->secure($value) : ( !is_null($value) ? "'".$this->secure($value)."'" : "NULL");
		}
		
		$sql = "INSERT INTO `".$table."`(".implode(",",$keys).") VALUES(".implode(",",$vals).")";
		if($isDisplay == 1)
			echo $sql;
			
		$qrySel = mysql_query($sql) or die(redirectErrorPage(mysql_error().'<br />'.$sql));
		return $qrySel;
	}

	public function update($table,$values,$where,$is,$isDisplay=NULL) {
		$updates = array();
		$where = $this->wheres($where,$is);
		foreach((array)$values as $key => $value) {
            $vals=(is_int($value)) ? $this->secure($value) : ( !is_null($value) ? "'".$this->secure($value)."'" : "NULL");
			$updates[] = "`".$this->secure($key)."`=".$vals;
		}
			
		$sql = "UPDATE `".$table."` SET ".implode(",",$updates)." WHERE ".$where;
		if($isDisplay==1)
			echo $sql; 

		return $this->query($sql);
	}
	public function delete($table,$where,$is) {
		$where = $this->wheres($where,$is);
		$sql = "DELETE FROM `".$table."` WHERE ".$where;
		$qrySel = mysql_query($sql) or die(redirectErrorPage(mysql_error().'<br />'.$sql));
		return $qrySel;
	}
	
	public function secure($value) {
		$content = NULL;
		
        $content = $this->trimAbuseWord($value);
        $content = $this->replace($content);

        return $content;
	}
	public function trimAbuseWord($string=NULL){

		$qrySel=$this->select("tbl_abuse","abuseKeyword","`isActive`='y'", "", "", 0);
		while($fetchRes = mysql_fetch_object($qrySel)) {
			$abuseKeyword = $this->filtering($fetchRes->abuseKeyword, 'output', 'string', 'strtolower');
			$replaceWord = $this->overwriteAbuseWord(strlen($abuseKeyword));
			
			$string = preg_replace('/\b'.$abuseKeyword.'\b/i', $replaceWord, $string);
		}
		return $string;
	}
	private function overwriteAbuseWord($chrCount) {
		if($chrCount == 2)
			return '**';
		else if($chrCount == 3)
			return '***';
		else if($chrCount == 3)
			return '***';
		else if($chrCount == 4)
			return '****';
		else if($chrCount == 5)
			return '*****';
		else if($chrCount == 6)
			return '******';
		else if($chrCount > 7)
			return '*******';			
		
	}
	public function replace($value) {
		
		$injection = array("mysql_query(", "mysql_connect(", "<object", "delete from", "<script ", "insert into", "text/javascript", "create table", 'alter table', 'drop table', 'drop database');
		foreach((array)$injection as $find){
				$value = str_ireplace($find,"{{".$find."}}",$value);
        }
		
       return $value;
	}
	/* function for print paging string */
	function ajaxPagination($pager, $page, $functionName, $jsFuncParam) {
    	$content = $jsFuncVariables = '';
    	for($i=0; $i<count($jsFuncParam); $i++) {
               $jsFuncVariables .= '\''.$jsFuncParam[$i].'\',';
    	}

		if($pager->numPages > 1)
		{
			if($pager->numPages > 10) {
				if($page <= 10) $startPage = 1;
				else if($page <= 20) $startPage = 11;
				else if($page <= 30) $startPage = 21;
				else if($page <= 40) $startPage = 31;
				else if($page <= 50) $startPage = 41;
				else if($page <= 60) $startPage = 51;
				else if($page <= 70) $startPage = 61;
				else if($page <= 80) $startPage = 71;
				else if($page <= 90) $startPage = 81;
				else if($page <= 100) $startPage = 91;
				else if($page <= 110) $startPage = 101;
				else if($page <= 120) $startPage = 111;
				else if($page <= 130) $startPage = 121;															
				else $startPage = $pager->numPages;
				$endPage =  $startPage+9;
			}
			else {
				$startPage = 1;
				$endPage =  $pager->numPages;
			}
			
			$content .= '<ul class="pagination">';
				if($page == -1)
				$page = 0;
				if ($page == 1 || $page == 0) // this is the first page - there is no previous page
					$content .= '';
				else if ($page > 1)  {        // not the first page, link to the previous page{
					$content .= '<li><a href="javascript:void(0);" class="oBtnSecondary oPageBtn" onclick="'.$functionName.'('.$jsFuncVariables.' \'1\');"><span>&laquo;</span></a></li>';
					
					$content .= '<li><a href="javascript:void(0);" class="oBtnSecondary oPageBtn" onclick="'.$functionName.'('.$jsFuncVariables.' \''.($page - 1).'\');"><span>&lsaquo;</span></a></li>';
				}
				
				for ($i = $startPage; $i <= $endPage; $i++) {
						if ($i == $pager->page)
							$content .= '<li><a href="javascript:void(0);" class="buttonPageActive">'.$i.'</a></li>';
						else
							$content .= '<li><a class="buttonPage" href="javascript:void(0);" onclick="'.$functionName.'('.$jsFuncVariables.' \''.$i.'\');">'.$i.'</a></li>';
				}
				
				if ($page == $pager->numPages) // this is the last page - there is no next page
					$content .= "";
				else {
					$content .= '<li><a href="javascript:void(0);" class="oBtnSecondary oPageBtn" onclick="'.$functionName.'('.$jsFuncVariables.' \''.($page + 1).'\');"><span>&rsaquo;</span></a></li>';

					$content .= '<li><a href="javascript:void(0);" class="oBtnSecondary oPageBtn" onclick="'.$functionName.'('.$jsFuncVariables.' \''.($pager->numPages).'\');"><span>&raquo;</span></a></li>';
				}
				$content .= '</ul>';
		}
		return $content;

   }
	private function wheres($where,$is,$concat = "AND") {
		if(($where != NULL) && ($is != NULL)) {
			if(is_array($where)) {
				for($i=0,$t=count($where);$i<$t;$i++)
					$array[] = "`".$where[$i]."`='".$is[$i]."'";
				$where = implode(" ".$concat." ",$array);
			} else{
				$is=(is_numeric($is)) ? $is : "'".$is."'";
				$where = "`".$where."`=".$is;
			}
		}
		return $where;
	}
	public function alreadyExist($table, $cols="*", $wherCond=NULL, $whereAndCond=NULL, $whIdVal = NULL, $whId = "id") {
		
		$sql = "SELECT ".$cols." FROM ".$table." WHERE ".$cols."='".$wherCond."'";
		if($wherCond == $whereAndCond)
			$sql .= " AND ".$cols." != '".$whereAndCond."'";
		if($whIdVal != NULL)
			$sql .= " AND ".$whId." != '".$whIdVal."'";
		//echo $sql;
		$qrySel = mysql_query($sql) or die(redirectErrorPage(mysql_error().'<br />'.$sql));
		if(mysql_num_rows($qrySel) > 0)
			return true;
		else 
			return false;
	
	}
	public function filtering($value='', $type='output', $valType='string', $funcArray='') {
		echo "123";
		exit;
		$content = $filterValues = '';
		if($valType == 'int')
			$filterValues = (isset($value) ? (int)strip_tags(trim($value)) : 0);
		if($valType == 'float')
			$filterValues = (isset($value) ? (float)strip_tags(trim($value)) : 0);
		else if($valType == 'string')
			$filterValues = (isset($value) ? (string)strip_tags(trim($value)) : NULL);
		else if($valType == 'text')
			$filterValues = (isset($value) ? (string)trim($value) : NULL);
		else
			$filterValues = (isset($value) ? trim($value) : NULL);
			
		if($type == 'input') {
			$content = mysql_real_escape_string($filterValues);
		}
		else if($type == 'output') {
			if($valType == 'string')
				$filterValues = html_entity_decode($filterValues);

			$value = str_replace(array('\r', '\n', ''), array('', '', ''), $filterValues);
			$content = stripslashes($value);			
		}
		else {
			$content = $filterValues;
		}
		
		if($funcArray != '') {
			$funcArray = explode(',',$funcArray);
			foreach($funcArray as $functions) {
				if($functions != '' && $functions != ' ') {
					if (function_exists($functions)) {
						$content = $functions($content);
					}
				}
			}
		}
		
		return $content;		
	}
	public function __destruct() {
		@$close = mysql_close($this->conn);
		if(DB_DEBUG)
			$this->debug("DISCONNECTED",$this->host,$close);
	}	
}	
?>