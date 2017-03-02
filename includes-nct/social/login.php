<?php 
/* -----------------------------------------------------------------------------------------
   IdiotMinds - http://idiotminds.com
   -----------------------------------------------------------------------------------------
*/
require 'Social.php';

$Social_obj= new Social();
if(isset($_GET['facebook'])){
	$Social_obj->facebook();
}

?>
<!-- after authentication close the popup -->
<script type="text/javascript">
window.close();
</script>