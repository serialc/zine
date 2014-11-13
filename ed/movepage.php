<?php
//Filename: movepage.php
//Purpose: Called through AJAX to move a page and update issues index
include('inc/constants.php');
include('inc/functions.php');

$issue = $_GET['is'];
$pageloc = $_GET['pg'];
$article = $_GET['art'];

# try and move

if( $result = $II->movePage($issue, $pageloc, $article) ) {
	echo 'success';
} else {
    echo 'fail';
}

?>
