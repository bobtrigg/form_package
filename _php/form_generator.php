<?php
###############################################################################
#
#  To gen: HTML title <title>, page heading (<h1>), descrip (<p>), details
#
###############################################################################

//  Find boilerplate HTML saved in include files
require_once("../_includes/file_names_inc.php");
$footer_file_name = FOOTER_FILE;
$header_file_name = HEADER_FILE;
$contact_info_file = CONTACT_FILE;
$json_file = JSON_FILE;

$messages = array();

//  Initialize form field values
$file_name = $HTMLtitle = $form_header = $description = $submit_to = "";
$field_names = "Name,Email,Phone";

if (file_exists($json_file) && is_readable($json_file)) {
	
	$json_data = nl2br(file_get_contents($json_file));
	$data_array = json_decode($json_data,true);
		
	if (isset($data_array['submit_to'])) {
		$submit_to = $data_array['submit_to'];
	}
	if (isset($data_array['send_text_email'])) {
		$send_text_email = $data_array['send_text_email'];
	}
}

//  Set flags to indicate when to create unordered lists
$open_ul = true;
$close_ul = false;

//  Set $num_lines, overwriting existing value according to this priority:
//  Use value in URL if exists; if not use include file if it exists; if not use default

$num_lines = 8;
include("../_includes/defaults_inc.php");
if (isset($_GET['num_lines']) && ($_GET['num_lines'] != '')) {
	$num_lines = $_GET['num_lines'];
}

//  Storage for individual line data
//  Details of lines are stored in Detail objects.

include_once("../_includes/detail_class.php");

$detail_array = array();

for ($i=0;$i<$num_lines;$i++) {
	$detail_array[$i] = new Detail("","shift",false);
}

//  Process user-submitted data

if (isset($_POST['submitted'])) {

	//  If email is requested and no URL exists, do not process new form!
	$email_exists = true;
	if (isset($_POST['submit_to']) && ($_POST['submit_to'] != '')) {
		$submit_to = trim($_POST['submit_to']);
	} else {
		if ($send_text_email == "Y") {
			$email_exists = false;
			$messages[] = "<h3>You must provide a data recipient email address!</h3>";
		}
	}
	
	if (isset($_POST['file_name']) && ($_POST['file_name'] != '')) {
		$file_name = trim($_POST['file_name']);
		$file_name = preg_replace('/ /','',$file_name);   // Remove spaces
		$file_name = preg_replace('/\./','',$file_name);   // Remove periods

		//  If string does not end in 'html', add '.html' at the end.

		//  Standardize b4 search: if file name ends in 'htm', add an 'l'
		if (preg_match('/htm$/i',$file_name)) {
			$file_name .= 'l';
		}
		//  Now replace suffix delimiting period, removed above
		if (preg_match('/html$/i',$file_name)) {
			// preg_replace('/html/','.html',$file_name);  *** doesn't work!
			$file_name = substr($file_name, 0, -4) . '.html';
		} else {
			$file_name .= '.html';
		}
	} else {
		$file_name = "yourform.html";
	}
	if (isset($_POST['HTMLtitle']) && ($_POST['HTMLtitle'] != '')) {
		$HTMLtitle = trim($_POST['HTMLtitle']);
	} else {
		$HTMLtitle = "Working title";
	}
	if (isset($_POST['form_header']) && ($_POST['form_header'] != '')) {
		$form_header = trim($_POST['form_header']);
	} else {
		$form_header = "Data input form";
	}
	if (isset($_POST['description']) && ($_POST['description'] != '')) {
		$description = trim(nl2br($_POST['description']));
		//  Remove backslashes preceding a single quote
		$description = preg_replace('/\\\\/','',$description);
		//  Convert special characters to ampersand code:
		$description = htmlentities($description);
	}
	
	if (!file_exists($file_name) || (is_writable($file_name))) {
	
		$handle = fopen($file_name,'w');
		
		//  Create header in html file
		fwrite($handle, "<!DOCTYPE html>\n<html lang='en'>\n<head>");
		fwrite($handle, "<title>" . $HTMLtitle . "</title>");
		$header_code = file_get_contents($header_file_name);
		fwrite($handle, $header_code);
		
		//  Write header text from form into html file
		fwrite($handle, "<h1><br>" . $form_header . "</h1>");
		fwrite($handle, "<p class=\"clear\">&nbsp;</p>");

		//  Write description text from form into html file
		fwrite($handle, "<p>" . $description . "</p>");
		
		//  Write contact info fields into html file
		$contact_info_fields = file_get_contents($contact_info_file);
		fwrite($handle, $contact_info_fields);
		
		//  Write checkbox lines to html file
		for ($i=1;$i<=$num_lines;$i++) {
		
			if (isset($_POST['line_text' . $i]) && ($_POST['line_text' . $i] != '')) {
			
				$name_str = $_POST['line_text' . $i];
				$detail_array[$i-1]->set_text($name_str);
				$stripped_name = preg_replace('/ /','_',$name_str);

				if (isset($_POST['line_type' . $i]) && $_POST['line_type' . $i] == 'category') {
				
					if ($close_ul) {
						fwrite($handle, "</ul>\n");
						$close_ul = false;
					}
				
					fwrite($handle, "\n<p class=\"title\"><strong>" . $name_str . "</strong></p>\n");
					$open_ul = true;
					$cat_str = $stripped_name;
					$detail_array[$i-1]->set_type('category');
				
				} else {
				
					if ($open_ul) {
						fwrite($handle, "<ul>\n");
						$open_ul = false;
					}
					$close_ul = true;
				
					if (isset($_POST['line_full' . $i]) && ($_POST['line_full' . $i] == 'on')) {
						$class_val = "class=\"full\"";
						// $full_array[$i-1] = true;
						$detail_array[$i-1]->set_full(true); 
					} else {
						$class_val = "";
						// $full_array[$i-1] = false;
						$detail_array[$i-1]->set_full(false); 
					}
			
					if ($cat_str != "") {
						$complete_name = $cat_str . ":" . $stripped_name;
					}
			
					fwrite($handle, "\n<li>\n");
					fwrite($handle, "\t<input name=\"" . $complete_name . "\" type=\"checkbox\" " . $class_val . " id=\"" . $complete_name . "\">\n");
					fwrite($handle, "\t<label for=\"" . $complete_name . "\">" . $name_str . "</label>\n");
					fwrite($handle, "</li>\n");	
					
					$field_names .= "," . $complete_name;
					$detail_array[$i-1]->set_type('shift');
				}
			}
		}
		if ($close_ul) {
			fwrite($handle,"</ul>\n");
		}
		
		//  Create footer and close html file

		if (isset($submit_to) && ($submit_to != '')) {
			$write_line = "<input type=\"HIDDEN\" name=\"submit_to\" value=\"" . $submit_to . "\">\n";
			fwrite($handle,$write_line);
		}
		$write_line = "<input type=\"HIDDEN\" name=\"form_id\" value=\"" . $HTMLtitle . "\">\n";
		fwrite($handle,$write_line);
		$write_line = "<input type=\"hidden\" name=\"Event\" id=\"Event\" value=\"" . $HTMLtitle . "\">\n";
		fwrite($handle,$write_line);
		
		$write_line = "<input type=\"HIDDEN\" name=\"data_order\" value=\"" . $field_names . "\">\n";
		fwrite($handle,$write_line);
		
		$footer_code = file_get_contents($footer_file_name);
		fwrite($handle, $footer_code);
		fclose($handle);
		
	} else {
		echo "<h1>Copy failed</h1>";
	}
	
	if (!headers_sent($hdr_file_name, $linenum) && $email_exists) {
		header("Location: " . $file_name);
		exit();
	}
}

?>
<!DOCTYPE html>
<html lang=”en”>
<head>
	<title>PHP Form Generator</title>
	<link href="../_css/genform.css" rel="stylesheet" type="text/css">
	<script src="../_js/jquery-1.6.2.js"></script>
</head>
<body>
	<form action="form_generator.php" method="post" id="initform" name="initform">
	
		<h1>Form Generator</h1>
		<h2>Fill in the following fields to set up a new form.</h2>
		
		<?php
			if (!empty($messages)) {

				echo "<p>";
				
				foreach ($messages as $msg) {
					echo "<strong>$msg</strong><br>\n";
				}
				echo "</p>\n";
			}
		?>
	
       <fieldset id="header">
	   
	   <div class="inputField">
            <div class="labelField">
                <label for="file_name">HTML form file name:</label>
            </div>
            <input name="file_name" type="text" id="file_name" size="50" value="<?php echo $file_name;?>">
        </div>

	   <div class="inputField">
            <div class="labelField">
                <label for="HTMLtitle">HTML title:</label>
            </div>
            <input name="HTMLtitle" type="text" id="HTMLtitle" size="50" value="<?php echo $HTMLtitle;?>">
        </div>

	   <div class="inputField">
            <div class="labelField">
                <label for="form_header">Form header:</label>
            </div>
            <input name="form_header" type="text" id="form_header" size="50" value="<?php echo $form_header;?>">
        </div>

		<label for="description">Description<br>
		  <textarea name="description" id="description" cols="80" rows="5"><?php echo $description ?></textarea>
		</label>

	   <div class="inputField">
            <div class="labelField">
                <label for="form_header">Data recipient&apos;s email:</label>
            </div>
            <input name="submit_to" type="email" id="submit_to" size="50" value="<?php echo $submit_to;?>">
        </div>

		</fieldset>
	
	<fieldset id="shifts">

		<?php
			for ($i=1;$i<=$num_lines;$i++) {
		?>
		
			<div class="line_item"> 
				<label for="line_text<?php echo $i ?>">Text:</label>
				<input type="text" name="<?php echo "line_text" . $i ?>" id="<?php echo "line_text" . $i ?>" size="30" value="<?php echo $detail_array[$i-1]->get_text(); ?>" />
				<input name="<?php echo "line_type" . $i ?>" type="radio" value="shift" <?php if ($detail_array[$i-1]->get_type() == 'shift') {echo "checked=\"checked\"";} ?> />Shift
				<input name="<?php echo "line_type" . $i ?>" type="radio" value="category" <?php if ($detail_array[$i-1]->get_type() == 'category') {echo "checked=\"checked\"";} ?> />Category
				<input type="checkbox" name="<?php echo "line_full" . $i ?>" id="<?php echo "line_full" . $i ?>" <?php if ($detail_array[$i-1]->get_full()) {echo "checked=\"checked\"";} ?> />Full 
			</div>
		
		<?php } ?>

        <input type="submit" name="submit" value="Save">
		<input type="hidden" name="submitted" value="1" />
		</fieldset>

	</form>
</body>
</html>