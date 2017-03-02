<?php
$content = NULL;
require_once("../../includes-nct/config-nct.php");
require_once("class.orderDetail-nct-nct.php");
$module = 'orderDetail-nct-nct';

$action = isset($_GET["action"]) ? trim($_GET["action"]) : (isset($_POST["action"]) ? trim($_POST["action"]) : '');

?>