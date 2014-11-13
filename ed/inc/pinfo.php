<?php

//Filename: pinfo.php
//Purpose: shows all the info on a pic and allows editing and deletion,

//echo(nl2br(print_r($_POST, TRUE))); //TESTING

if(isset($_POST['delete'])) {
//delete the page from the master file and remove pics from all the folders

	if($II->deletePage($_POST['pid'])) {
		echo "<h2>Page permanetly deleted</h2>";
	}

} else {

//update page file with new
if(isset($_POST['submit'])) {

	if($II->updatePage($_POST['pid'], $_POST['author'], $_POST['title'], $_POST['tags'])) {
		echo "<h2>Page updated</h2>";
	}
}

//show the form

//get the issue we are currently desiring to edit
$page = $_GET['pid'];
$pArray = $II->getPageInfo($page);

//open pinfo div
echo "<div id='pinfo'>";

//HEADING
//echo "<img src='pages/" . THUMBPIC . "/" . $page . ".png'>";
echo "<img src='pages/" . SMALLPIC . "/" . $page . ".png'>";
echo "<h2>Edit <em>". $pArray[1] . "<em></h2>";

?>
<form enctype='multipart/form-data' action='<?php echo $_SERVER['PHP_SELF']; ?>?page=pinfo&pid=<?php echo $page; ?>' method='post'>
<p><label>Author:</label><input type='text' name='author' value='<?php echo $pArray[0] ?>'></p>
<p><label>Title:</label><input type='text' name='title' value='<?php echo $pArray[1] ?>'></p>
<p><label>Tags:</label><input type='text' name='tags' value='<?php echo $pArray[2] ?>'></p>
<input type='hidden' name='pid' value='<?php echo $page; ?>.png'>
<p><input class='button' type='submit' name='submit' value='Submit edits'></p>
<p><input class='button' type='submit' name='delete' value='Delete page'></p>

</div><!-- end of pinfo -->

<?php

}

?>
