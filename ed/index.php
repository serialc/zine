<?php
error_reporting(E_ALL);
date_default_timezone_set('Europe/Paris');

$page = '';
if(isset($_GET['page'])) {
	$page = $_GET['page'];
}
//echo 'p'.$page.'p';

include('inc/constants.php');
include('inc/functions.php');

include('inc/header.inc');

switch ($page) {
	case 'upload':
		include('inc/upload.php');
	break;

	case 'pinfo':
		include('inc/pinfo.php');
	break;
	
	default:
		include('inc/zine_ed.php');
}

include('inc/footer.inc');

?>
