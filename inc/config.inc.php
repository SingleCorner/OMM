<?php
//*******Part1 User Defined Variales***********//

/* Project Name */
$AppID = "OMM";

/* Database Address */
$DB_HOST = "localhost";

/* Database Username */
$DB_USER = "WebApp_OMM";

/* Database Password */
$DB_PASSWD = "NYRmEGW2TJF26BdU";

/* Database Name */
$DATABASE = "WebApp_OMM";

/* User IP Address */
if(empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ip_addr = $_SERVER["REMOTE_ADDR"];
}else{
	$ip_addr = "";
}

/* Login timeout */
$sys_timeout = '1800';//Logout without operating in 5 minutes


//*******Part2  Variable -> Constant************//

/* Prevent the program has change extremely */

define("__APP_ID__",$AppID);
define("__DB_HOST__",$DB_HOST);
define("__DB_USER__",$DB_USER);
define("__DB_PASSWD__",$DB_PASSWD);
define("__DB__",$DATABASE);
define("__IP_ADDR__",$ip_addr);
define("__SYS_TIMEOUT__",$sys_timeout);


//*******Part3  Other file************//

/* Judge the security access */
require_once(__ROOT__."/inc/access.judge.php");

/* Operating the database */
require_once(__ROOT__."/inc/sql.class.php");

/* Filter and Converter */
require_once(__ROOT__."/inc/feature.php");

?>
