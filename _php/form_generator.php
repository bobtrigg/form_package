<?php
###############################################################################
#
#  To gen: HTML title <title>, page heading (<h1>), descrip (<p>), details
#
###############################################################################

//  Boilerplate HTML saved in include files
$footer_file_name = "_includes/footer_inc.php";
$header_file_name = "_includes/header_inc.php";
$contact_info_file = "_includes/contact_info_inc.php";

//  Initialize form field values
$file_name = $HTMLtitle = $form_header = $description = "";

//  Set flags to indicate when to create unordered lists
$open_ul = true;
$close_ul = false;

$num_lines = 8;

if (isset($_POST['submitted'])) {

	if (isset($_POST['file_name']) && ($_POST['file_name'] != '')) {
		$file_name = trim($_POST['file_name']);
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

				if (isset($_POST['line_type' . $i]) && $_POST['line_type' . $i] == 'category') {
				
					if ($close_ul) {
						fwrite($handle, "</ul>\n");
						$close_ul = false;
					}
				
					fwrite($handle, "\n<p class=\"title\"><strong>" . $name_str . "</strong></p>\n");
					$open_ul = true;
				
				} else {
				
					if ($open_ul) {
						fwrite($handle, "<ul>\n");
						$open_ul = false;
					}
					$close_ul = true;
				
					if (isset($_POST['line_full' . $i]) && ($_POST['line_full' . $i] == 'on')) {
						$class_val = "class=\"full\"";
					} else {
						$class_val = "";
					}
			
					fwrite($handle, "\n<li>\n");
					fwrite($handle, "\t<input name=\"" . $name_str . "\" type=\"checkbox\" " . $class_val . " id=\"" . $name_str . "\">\n");
					fwrite($handle, "\t<label for=\"" . $name_str . "\">" . $name_str . "</label>\n");
					fwrite($handle, "</li>\n");	
				}
			}

		}
		
		//  Create footer and close html file
		$footer_code = file_get_contents($footer_file_name);
		fwrite($handle, $footer_code);
		fclose($handle);
		
	} else {
		echo "<h1>Copy failed</h1>";
	}
	if (!headers_sent($hdr_file_name, $linenum)) {
		header("Location: " . $file_name);
		exit();
	}
}

?>
<html>
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
		  <textarea name="description" id="description" cols="80" rows="5"></textarea>
		</label>
	</fieldset>
	
	<fieldset id="shifts">

		<?php
			for ($i=1;$i<=$num_lines;$i++) {
		?>
		
			<div class="line_item"> 
				<label for="line_text<?php echo $i ?>">Text:</label>
				<input type="text" name="<?php echo "line_text" . $i ?>" id="<?php echo "line_text" . $i ?>" size="30" />
				<input name="<?php echo "line_type" . $i ?>" type="radio" value="shift" checked="checked" />Shift
				<input name="<?php echo "line_type" . $i ?>" type="radio" value="category" />Category
				<input type="checkbox" name="<?php echo "line_full" . $i ?>" id="<?php echo "line_full" . $i ?>" />Full 
			</div>
		
		<?php } ?>

        <input type="submit" name="submit" value="Save">
		<input type="hidden" name="submitted" value="1" />
		</fieldset>

	</form>
</body>
</html>