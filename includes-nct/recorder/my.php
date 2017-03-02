<?php
   require_once('../config-nct.php');
   if(!isset($_REQUEST['filename']))
   {
     exit('No file');
   }
   $upload_path = DIR_UPD;
   $filename = $_REQUEST['filename'];
   
   $fp = fopen($upload_path."audio/".$filename.".wav", "wb");
   
   fwrite($fp, file_get_contents('php://input'));
   
   fclose($fp);
   
   exit('done');

?>