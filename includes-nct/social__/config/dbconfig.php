<?php
@session_start();
require_once("functions.php");//for database connection and all this
define('SERVER_NAME',$_SERVER["SERVER_NAME"]);
if(SERVER_NAME == 'localhost' || SERVER_NAME=='127.0.0.1'|| SERVER_NAME=='localhost:80' ) 
{
	define('DB_SERVER', 'localhost');
	define('DB_USERNAME', 'root');
	define('DB_PASSWORD', '');
	define('DB_DATABASE', 'fb');
}
else
{
	define('DB_SERVER', 'mysql.googiehost.com');
	define('DB_USERNAME', 'u908279281_nct');
	define('DB_PASSWORD', 'nct123456');
	define('DB_DATABASE', 'u908279281_nct');

}
$link = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db(DB_DATABASE,$link);

?>