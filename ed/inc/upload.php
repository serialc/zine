<?php
//Filename: upload.php
//Function: handles the uploading and process of hang pages/pictures

if(isset($_POST['submit'])) {
	$author = $_POST['author'];
	$title = $_POST['title'];
	$tags = $_POST['tags'];

	echo '<h2>Submitted page</h2>';
	echo '<p>Author: '.$author.'<br>';
	echo 'Title: '.$title.'<br>';
	echo 'Tags: '.$tags.'<br>';
	echo 'Page image: '.$_FILES['picupload']['name'].'<br>';
	echo 'File size: '.$_FILES['picupload']['size'].'</p>';
	
	//check image format/size
	$errors = false;
	$img_info = '';
	
	//get image sizes from constans file
	$LpicD = preg_split("/x/", FULLPIC);
	$MpicD = preg_split("/x/", MEDIUMPIC);
	$SpicD = preg_split("/x/", SMALLPIC);
	$TpicD = preg_split("/x/", THUMBPIC);
	
	//echo(nl2br(print_r($_FILES, true))); //Testing

	//check type/value/submission
	if($_FILES['picupload']['name'] == '') {
		//no file input
		echo "<p class='error'>You must submit an image!</p>";
		$errors = true;
	} else {
		$img_info = getimagesize($_FILES['picupload']['tmp_name']);
		
		//echo(nl2br(print_r($img_info, true))); //Testing

		//is it a png?
		if($img_info['mime'] != 'image/png') {
			//incorrect input format
			echo "<p class='error'>Incorrect image format - it must be a png! Mime type is: ".$img_info['mime']."</p>";
			
			$errors = true;
		//is it the right size
		} else if($img_info[0] != $LpicD[0] || $img_info[1] != $LpicD[1]) {
			//invalid dimension
			echo "<p class='error'>Incorrect image size - it must be ".FULLPIC."!</p>";
			$errors = true;
		} else {
			echo "<p>Image format and size accepted. It should appear below.</p>";
		}
	}
	
	//check that destination directories exist
	if(!is_dir('pages/'.FULLPIC)) {
		mkdir('pages/'.FULLPIC, 0755);
	}
	if(!is_dir('pages/'.MEDIUMPIC)) {
		mkdir('pages/'.MEDIUMPIC, 0755);
	}
	if(!is_dir('pages/'.SMALLPIC)) {
		mkdir('pages/'.SMALLPIC, 0755);
	}
	if(!is_dir('pages/'.THUMBPIC)) {
		mkdir('pages/'.THUMBPIC, 0755);
	}
	
	
	if(!$errors) {
		$new_page_name = date('Y-m-d-G-i-s') . '.png';
		$full_img_dest = 'pages/'.FULLPIC.'/'.$new_page_name;
		move_uploaded_file($_FILES['picupload']['tmp_name'], $full_img_dest);
		$full_img = imagecreatefrompng($full_img_dest);
		
		//make smaller image sizes
		$image_m = imagecreatetruecolor($MpicD[0], $MpicD[1]);
		$image_s = imagecreatetruecolor($SpicD[0], $SpicD[1]);
		$image_t = imagecreatetruecolor($TpicD[0], $TpicD[1]);

		imagecopyresampled($image_m, $full_img, 0, 0, 0, 0, $MpicD[0], $MpicD[1], $LpicD[0], $LpicD[1]);
		imagepng($image_m, 'pages/'.MEDIUMPIC.'/'.$new_page_name, 0);
		
		imagecopyresampled($image_s, $full_img, 0, 0, 0, 0, $SpicD[0], $SpicD[1], $LpicD[0], $LpicD[1]);
		imagepng($image_s, 'pages/'.SMALLPIC.'/'.$new_page_name, 0);
		
		imagecopyresampled($image_t, $full_img, 0, 0, 0, 0, $TpicD[0], $TpicD[1], $LpicD[0], $LpicD[1]);
		imagepng($image_t, 'pages/'.THUMBPIC.'/'.$new_page_name, 0);
		
		//update issue.txt file with all detials
		$II->addNewPage($author, $title, $tags, $new_page_name);

		echo "<a href='pages/".FULLPIC.'/'.$new_page_name."'><img src='pages/".THUMBPIC.'/'.$new_page_name."'></a>";
	}
}

?>
<h2>Upload a page/article</h2>
<p>Currently only images are used to upload an article. This insures that what you submit is pixel perfect without having to address typesetting issues.</p>
<p>Format Specifications:<br>
1. Must be a png type image.<br>
2. Dimensions: 1200 width, 1750 height (pixels).<br>
3. Colour or black & white (B&W) is fine but we will print in B&W and distribute online PDFs in colour.<br>
4. Here is a blank correctly sized template to build on: <a href='../imgs/template.png'>template.png</a>
</p>

<form enctype="multipart/form-data" action="<?php echo $_SERVER['PHP_SELF'] ?>?page=upload" method="post">
<p><label>Author:</label><input type='text' name='author'> <span class='hint'>Your real name or an alias<span></p>
<p><label>Title:</label><input type='text' name='title'> <span class='hint'>Simply used to refer to your page<span></p>
<p><label>Tags:</label><input type='text' name='tags'> <span class='hint'>news, pic, map, review, music, restaurant, ...<span></p>
<p><label>File:</label><input name='picupload' type='file'></p>
<p><input class='button' type='submit' name='submit' value='Submit page'></p>
</form>
