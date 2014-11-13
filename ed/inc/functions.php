<?php
//Filename: functions.inc
//Function: utility functions

//issue index file contains lines such as:
//Cyrille:Big mess:orange, circles, mess:0:0:2011-04-18-10-17-56.png
//meaning:
//Author:Title:Tags:Zine Issue:Zine Page:Page/Article name

//object to work with ISSUEINDEX

class IssueIndex {

	private $ii = '';

	function __construct() {
		//read the file into a class variable array
		$this->ii = $this->readIssueFile();
		//echo nl2br(print_r($this->ii,true));
	}

	private function rewriteIssueIndex() {
		$output = '';
		foreach($this->ii as $fn => $page) {
			$output .= $page[0].':'.$page[1].':'.$page[2].':'.$page[3].':'.$page[4].':'.$fn."\n";
		}

        if(is_writable(ISSUEINDEX)) {
            $fh = fopen(ISSUEINDEX, 'w');
            $success = fwrite($fh, $output);
            fclose($fh);
            return $success;
        }
        echo "Please enable writing for the " . ISSUEINDEX . " file.";
        return False;
	}
	
	public function getLatestIssueNum() {
		$max = 0;
		foreach($this->ii as $page) {
			if($page[3] > $max) {
				$max = $page[3];
			}
		}
		return $max;
	}
	
	public function addNewPage($author, $title, $tags, $fname) {
		$fh = fopen(ISSUEINDEX, 'a');
		fwrite($fh, "\n".$author.':'.$title.':'.$tags.':0:0:'.$fname);
		fclose($fh);
	}

	public function printDragThumbs($search) {
        $is_empty = True;
		foreach($this->ii as $fn => $page) {
			if($page[3] == 0 && $page[4] == 0) {
                $is_empty = False;
				echo "<img id='".rtrim($fn, '.png')."' srch='".$page[0].','.$page[1].','.$page[2]."' title='".$page[1]."' src='pages/".THUMBPIC."/".$fn."'>";
			}
		}
        if( $is_empty ) {
            echo "<div id='thumb_bg'><a href='?page=upload'>Upload more pages</a></div>";
        }
	}
	
	public function printPagesForIssue($issue) {
		$pages_with_article = array();
		foreach($this->ii as $fn => $page) {
			if($page[3] == $issue) {
				$pages_with_article[$page[4]] = $fn;
			}
		}
		
		$temp = '';
		for($i = 1; $i <= ZINEPAGESTOTAL; $i++ ) {
			echo "<div class='zipa' id='zp".$i."'><span>".$i."</span>";
			if(isset($pages_with_article[$i])) {
				$temp = $this->ii[$pages_with_article[$i]];
				echo "<img id='".rtrim($pages_with_article[$i],'.png')."' srch='".$temp[0].','.$temp[1].','.$temp[2]."' title='".$temp[1]."' src='pages/".THUMBPIC."/".$pages_with_article[$i]."'>";
			}
			echo "</div>\n";
		}
	}
	
	function readIssueFile() {
		$fh = fopen(ISSUEINDEX, 'r');
		$fcontents = rtrim(fread($fh, filesize(ISSUEINDEX)));
		fclose($fh);

		$fcontents = preg_split("/[\r\n]/",$fcontents);
		$index = '';
		
		//go through each line and create hash based on filename
		foreach($fcontents as $page) {

			//if the line is not empty
			if(strlen($page)) {
				$temp = preg_split("/:/", $page);
				$index[$temp[5]] = array_slice($temp, 0, 5);
			}
		}
		return $index;
	}

	public function getPageInfo($p) {
		return $this->ii[$p . '.png'];
	}

	public function deletePage($p) {

		//Delete image files from the four folders
		unlink('pages/' . THUMBPIC . '/' . $p);
		unlink('pages/' . SMALLPIC. '/' . $p);
		unlink('pages/' . MEDIUMPIC . '/' . $p);
		unlink('pages/' . FULLPIC . '/' . $p);

		//Go through each item
		$output = '';
		foreach($this->ii as $fn => $page) {
			//only rewrite to index if it is not the page we wish to delete
			if($p != $fn) {
				$output .= $page[0].':'.$page[1].':'.$page[2].':'.$page[3].':'.$page[4].':'.$fn."\n";
			}
		}
		
		$fh = fopen(ISSUEINDEX, 'w');
		$success = fwrite($fh, $output);
		fclose($fh);

		return $success;

	}

	public function updatePage($p, $a, $ti, $ta) {
		//update the author, title and tags
		$this->ii[$p][0] = $a;
		$this->ii[$p][1] = $ti;
		$this->ii[$p][2] = $ta;

		//rewrite index file with updated info
		return $this->rewriteIssueIndex();
	}
	
	public function movePage($i, $p, $movedpage) {

        // Indices: [0]Author:[1]Title:[2]Tags:[3]Zine Issue:[4]Zine Page:[5]Page/Article name
		// check if the page in this issue is already occupied
		foreach($this->ii as $fn => $page) {
			if($page[3] == $i && $page[4] == $p && $i != 0 && $p != 0) {
				echo $fn;
				return false;
			}
		}

		//update the issue and page number of the article
		$this->ii[$movedpage][3] = $i;
		$this->ii[$movedpage][4] = $p;
		
		//update the issue index file
		return $this->rewriteIssueIndex();
	}
}

$II = new IssueIndex();

?>
