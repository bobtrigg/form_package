<?php
###############################################################################
#
#  form_generator.php
#  Created by Bob Trigg, June 2013
#
#  This script uses user input from a form to generate a new form file. 
#  Expected input includes the new file name, the page title, page header,
#  a description (from a text box), the email address of the person to 
#  receive emails when the new form is submitted, and detail lines.

#  Each detail line on the new form will include a text label, possibly a checkbox,
#  and possibly the word "FULL"! instead of the checkbox. This form contains
#  a number of lines in which the user can enter that information. Note that the
#  syntax is specific to the application for which this was written, but a 'category'
#  does not get a checkbox, and a 'checkbox' does.

#  Default values are stored in a JSON file, defaults.json, which can be set up with
#  the program form_processor_init.php which came with this package. 

#  Existing values are retrieved from the file before the form is displayed.
#  After the user clicks 'Save' to submit the form, the new form is generated
#  and the new form opens in the same window.

#  This code is offered free; anyone using it has permission to alter it in
#  any way useful to them.

###############################################################################

require_once("../_includes/base_pack.php");
require_once("../_classes/detail_class.php");
require_once("../_classes/form_class.php");
require_once("../_classes/file_class.php");
require_once("../_includes/form_functions.php");

//  Find boilerplate HTML saved in include files

$footer_file_name = FOOTER_FILE;
$header_file_name = HEADER_FILE;
$contact_info_file = CONTACT_FILE;
$json_file = JSON_FILE;

$messages = array();

//  Initialize form field values
$file_name = $HTMLtitle = $form_header = $description = $submit_to = "";
$field_names = "Name,Email,Phone";

$num_lines = set_num_lines();

//  Instantiate JSON file object and get data from it
$json_object = new JSON_Data($json_file);

list($submit_to, $form_id, $ok_url, $not_ok_url, $send_text_email, $append_to_file, $data_file_name, $format, $delimiter) = $json_object->get_json_data($json_file);

//  Set flags to indicate when to create unordered lists
$open_ul = true;
$close_ul = false;

//  Storage for individual line data
//  Details of lines are stored in Detail objects.

$detail_array = array();

for ($i=0;$i<$num_lines;$i++) {
	$detail_array[$i] = new Detail("","checkbox",false);
}

//  Process user-submitted data


$messages[] = "Is the submit recognized?";
if (isset($_POST['submitted'])) {

$messages[] = "Yes!";
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
	
	$html_file = new File();
	
	$html_file->set_file_name($_POST['file_name']);
	$file_name = $html_file->get_file_name();
	
	if (isset($_POST['HTMLtitle']) && ($_POST['HTMLtitle'] != '')) {
		$HTMLtitle = trim($_POST['HTMLtitle']);
		$HTMLtitle = preg_replace('/\\\\/','',$HTMLtitle);
	} else {
		$HTMLtitle = "Working title";
	}
	
	if (isset($_POST['form_header']) && ($_POST['form_header'] != '')) {
		$form_header = trim($_POST['form_header']);
		$form_header = preg_replace('/\\\\/','',$form_header);
	} else {
		$form_header = "Data input form";
	}

	if (isset($_POST['description']) && ($_POST['description'] != '')) {
		$description = trim(nl2br($_POST['description']));
		//  Remove backslashes preceding a single quote
		$description = preg_replace('/\\\\/','',$description);
		//  Convert special characters to ampersand code:
		// $description = htmlentities($description);
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

				// Remove "bad characters", comma and space
				$bad_chars = array(",", " ");
				$stripped_name = str_replace($bad_chars, "", $name_str);

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
						$detail_array[$i-1]->set_full(true); 
					} else {
						$class_val = "";
						$detail_array[$i-1]->set_full(false); 
					}
			
					if ($cat_str != "") {
						$complete_name = $cat_str . ":" . $stripped_name;
					} else {
						$complete_name = $stripped_name;
					}
			
					fwrite($handle, "\n<li>\n");
					fwrite($handle, "\t<input name=\"" . $complete_name . "\" type=\"checkbox\" " . $class_val . " id=\"" . $complete_name . "\">\n");
					fwrite($handle, "\t<label for=\"" . $complete_name . "\">" . $name_str . "</label>\n");
					fwrite($handle, "</li>\n");	
					
					$field_names .= "," . $complete_name;
					$detail_array[$i-1]->set_type('checkbox');
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
		  <textarea name="description" id="description" cols="80" rows="5"><?php echo $description; ?></textarea>
		</label>

	   <div class="inputField">
            <div class="labelField">
                <label for="form_header">Data recipient&apos;s email:</label>
            </div>
            <input name="submit_to" type="email" id="submit_to" size="50" value="<?php echo $submit_to;?>">
        </div>

		</fieldset>
	
	<fieldset id="checkboxes">

		<?php
			for ($i=1;$i<=$num_lines;$i++) {
		?>
		
			<div class="line_item"> 
				<label for="line_text<?php echo $i ?>">Text:</label>
				<input type="text" name="<?php echo "line_text" . $i ?>" id="<?php echo "line_text" . $i ?>" size="30" value="<?php echo $detail_array[$i-1]->get_text(); ?>" />
				<input name="<?php echo "line_type" . $i ?>" type="radio" value="checkbox" <?php if ($detail_array[$i-1]->get_type() == 'checkbox') {echo "checked=\"checked\"";} ?> />Checkbox
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