<?php

//Filename: zine_ed.php
//Purpose: creates all the html for drag an drop,
//moving of pages to/from zine

error_reporting(E_ALL);

//get the range of issues and provide link to each
$max_issue = $II->getLatestIssueNum();
if($max_issue < 1) {
	//start editing issue 1
	$max_issue = 1;
}

//get the issue we are currently desiring to edit
if(isset($_GET['issue'])) {
	$cur_issue = $_GET['issue'];
} else {
	$cur_issue = $max_issue;
}

//start editing issue $max_issue and list other issues
echo "<div id='issue_nav'><span class='fl'>Edit issue:&nbsp;</span><ul>";
for($i = 1; $i <= $max_issue; $i++) {
	echo "<a href='?issue=".$i."'><li>#".$i.'</li></a>';
}
echo "<a href='?issue=".($max_issue+1)."'><li>+</li></a></ul></div>";

//HEADING
echo "<h2>Editing issue <span id='theissue'>".$cur_issue."</span><span id='fmsg'> </span></h2>";
echo "<div>Drag the pages to reorder them. Double click to modify page.</div>";

//PAGE ZOOM OVERLAY
echo "<div id='zmwin'></div>\n";

//ZINE DIV
echo "<div id='zine_zone'>";
$pages_with_article = $II->printPagesForIssue($cur_issue);
echo "</div>";

//PAGES AVAILABLE DIV + SEARCH
echo "<div class='fr'>Search: <input type='text' id='page_search'></div>";
echo "<div id='pages_zone'>";
$II->printDragThumbs('');
echo "</div>";
?>

<script>

var HG = {};
//JSLING vars below
//var $ = {};
//var document;
//var setTimeout;
//comment out JSLINT vars above

HG.validdrag = false;
//SEARCH FUNCTION
HG.pageSearch = function () {
	var kids, nkids, re, i;
	
	//get all the pages in the pages_zone
	kids = $('#pages_zone').children();
	nkids = kids.size();
	
	//check each one for a match with this.value
	re = new RegExp($('#page_search').val(), 'i');
	
	for (i = 0; i < nkids; i = i + 1) {
		if (re.test(kids[i].getAttribute('srch'))) {
			$('#' + kids[i].id).show(300);
		} else {
			$('#' + kids[i].id).hide(300);
		}
	}
};

//DRAG and DROP FUNCTIONS
HG.DNDdragstart = function (e) {
	e.dataTransfer.setData('text', this.id);
	HG.validdrag = true;
};

HG.DNDdragend = function () {
	HG.validdrag = false;
};

HG.DNDdragenter = function () {
	this.style.borderStyle = 'dashed';
};

HG.DNDdragover = function (e) {
	e.preventDefault();
};

HG.DNDdragleave = function () {
	this.style.borderStyle = 'solid';
};

HG.DNDdrop = function (e) {
	var pid, target, that, imgclone, theclone;

	//perform the following actions regardless of whether the drop succeeds or fails
	e.preventDefault();
	this.style.borderStyle = 'solid';
	//check if the drop is valid
	pid = e.dataTransfer.getData('text');
		
	if (!pid || !HG.validdrag) {
		//something is wrong - do not proceed with drop
		return;
	}
	
	//prep values for ajax GET
	target = {
		i: $('#theissue').html(),
		p: this.id.slice(2),
		art: pid + '.png'
	};
	
	//if we are placing the article back on the 'shelf' reset placement to 0:0
	if (this.id === 'pages_zone') {
		target.i = 0;
		target.p = 0;
	}
	
	//test if this is necessary
	that = this;
	
	//try and update the issue index master file
	//use ajax to update issue index
	$.ajax({
		type: 'GET',
		url: 'movepage.php',
		cache: false,
		error: function (msg) {
			//flash the failure message
			$('#fmsg').html("<span class='fnotef'>Failed move</span>").children().fadeIn('fast');
			setTimeout(function () {$('#fmsg').children().fadeOut('slow'); }, 3000);
		},
		success: function (msg) {
			//msg should be 'success'
			if (msg !== 'success') {
				//flash the failure message
				$('#fmsg').html("<span class='fnotef'>Failed move</span>").children().fadeIn('fast');
				setTimeout(function () {$('#fmsg').children().fadeOut('slow'); }, 3000);
				return;
			}
			//flash the success message
			$('#fmsg').html("<span class='fnotes'>Success</span>").children().fadeIn('fast');
			setTimeout(function () {$('#fmsg').children().fadeOut('slow'); }, 1000);
			
			//clone img
			imgclone = $('#' + pid).clone();
		
			//remove img
			$('#' + pid).remove();
			
			//place img in new location
			imgclone.appendTo('#' + that.id);
	
			//and add events to the new clone
			theclone = document.getElementById(pid);
			theclone.addEventListener('dragstart', HG.DNDdragstart, false);
			theclone.addEventListener('dragend', HG.DNDdragend, false);
			theclone.addEventListener('dblclick', HG.ZoomWin, false);
			
			//do a page search to check if it should be visible if placed in the zine_zone
			HG.pageSearch();
		},
		data: 'is=' + target.i + '&pg=' + target.p + '&art=' + target.art
	});
};

//Make it so double clicking on a page shows a larger version of the page
HG.ZoomWin = function () {	//set the overlay background to be the same height as the body
	$('#zmwin').css('height', $('body').css('height'));
	//show the img
	$('#zmwin').toggle().html("<div id='imghld'><img src='pages/<?php echo SMALLPIC; ?>/" + this.id + ".png'><a href='index.php?page=pinfo&pid=" + this.id + "'>Edit page info</a></div>");
};

//initialize events
HG.init = (function () {
	var kids, nkids, pages_zone, i;
	
	//bind keystrokes to updating the pages visible
	$('#page_search').bind('keyup', HG.pageSearch);

	//make all pages draggable
	$('.zipa').attr('draggable', 'true');

	//give all pages a draggable event
	kids = $('#pages_zone').children();
	nkids = kids.size();

	for (i = 0; i < nkids; i = i + 1) {
		kids[i].addEventListener('dragstart', HG.DNDdragstart, false);
		kids[i].addEventListener('dragend', HG.DNDdragend, false);
	}

	//give the pages_zone droppable ability
	pages_zone = document.getElementById('pages_zone');
	pages_zone.addEventListener('dragenter', HG.DNDdragenter, true);
	pages_zone.addEventListener('dragover', HG.DNDdragover, true);
	pages_zone.addEventListener('drop', HG.DNDdrop, true);
	pages_zone.addEventListener('dragleave', HG.DNDdragleave, true);

	//Make zine page areas droppable
	kids = $('#zine_zone').children();
	nkids = kids.size();

	//go through each page of the zine (~32)
	for (i = 0; i < nkids; i = i + 1) {
		kids[i].addEventListener('dragenter', HG.DNDdragenter, false);
		kids[i].addEventListener('dragover', HG.DNDdragover, false);
		kids[i].addEventListener('drop', HG.DNDdrop, false);
		kids[i].addEventListener('dragleave', HG.DNDdragleave, false);
	}

	//give all img articles already placed in zine_zone the dragstart event listener
	kids = $('#zine_zone img');
	nkids = kids.size();

	for (i = 0; i < nkids; i = i + 1) {
		kids[i].addEventListener('dragstart', HG.DNDdragstart, false);
		kids[i].addEventListener('dragend', HG.DNDdragend, false);
	}

	//bind page images to double click for larger version
	$('.zipa img').bind('dblclick', HG.ZoomWin);
	$('#pages_zone img').bind('dblclick', HG.ZoomWin);
	$('#zmwin').bind('click', function () { $(this).hide(); });
}());

</script>
