<?php
###############################################################################
#  This simple sticky form processes default data used in the universal form
#  processor form_processor.php. Default values are stored in a JSON file,
#  defaults.json. Values are retrieved from the file before the form
#  is displayed.
###############################################################################

require_once("../_includes/file_names_inc.php");

$json_file = JSON_FILE;
$submit_to = $form_id = $ok_url = $not_ok_url = $send_text_email = $append_to_file = "";
$messages = array();

function is_it_checked($fld_val) {
	if ($fld_val == 'Y') { echo 'checked'; }
}
function is_radio_checked($radio_name, $fld_val) {
	if ($radio_name == $fld_val) { echo 'checked'; }
}

# Validate and write form data to JSON file

if (isset($_POST['submitted'])) {

	$data_array = array();
	if (isset($_POST['submit_to']) && ($_POST['submit_to'] != '')) {
		$submit_to = $data_array['submit_to'] = $_POST['submit_to'];
	}
	if (isset($_POST['form_id']) && ($_POST['form_id'] != '') && ($_POST['form_id'] != ' ')) {
		$form_id = $data_array['form_id'] = $_POST['form_id'];
	}
	if (isset($_POST['ok_url']) && ($_POST['ok_url'] != '') && ($_POST['ok_url'] != ' ')) {
		$ok_url = $data_array['ok_url'] = $_POST['ok_url'];
	}
	if (isset($_POST['not_ok_url']) && ($_POST['not_ok_url'] != '') && ($_POST['not_ok_url'] != ' ')) {
		$not_ok_url = $data_array['not_ok_url'] = $_POST['not_ok_url'];
	}
	if (isset($_POST['send_text_email']) && ($_POST['send_text_email'] != '')) {
		$send_text_email = $data_array['send_text_email'] = $_POST['send_text_email'];
	}
	if (isset($_POST['append_to_file']) && ($_POST['append_to_file'] != '')) {
		$append_to_file = $data_array['append_to_file'] = $_POST['append_to_file'];
	}
	if (isset($_POST['data_file_name']) && ($_POST['data_file_name'] != '') && ($_POST['data_file_name'] != ' ' )) {
		$data_file_name = $data_array['data_file_name'] = $_POST['data_file_name'];
	}
	if (isset($_POST['format']) && ($_POST['format'] != '')) {
		$format = $data_array['format'] = $_POST['format'];
	} else {	
		$format = $data_array['format'] = "delim";
	}
	if (isset($_POST['delimiter']) && ($_POST['delimiter'] != '')) {
		$delimiter = $data_array['delimiter'] = $_POST['delimiter'];
	} else {
		$delimiter = $data_array['delimiter'] = ',';
	}
	
	if (file_exists($json_file) && !is_writable($json_file)) {
		$messages[] = "JSON file is not writable; contact tech support";
	} else {
		if ($handle = fopen($json_file,'w')) {
			fwrite($handle, json_encode($data_array));
			fclose($handle);
			$messages[] = "JSON file successfully updated";
		} else {
			$messages[] = "Writing to JSON file failed";
		}
	}
}

# Read JSON file and populate form field variables

if (file_exists($json_file)) {
	if (is_readable($json_file)) {
	
		$json_data = nl2br(file_get_contents($json_file));
		$data_array = json_decode($json_data,true);
		
		isset($data_array['submit_to']) ? $submit_to = $data_array['submit_to'] : $submit_to = " ";
		isset($data_array['form_id']) ? $form_id = $data_array['form_id'] : $form_id = " ";
		isset($data_array['ok_url']) ? $ok_url = $data_array['ok_url'] : $ok_url = " ";
		isset($data_array['not_ok_url']) ? $not_ok_url = $data_array['not_ok_url'] : $not_ok_url = " ";
		isset($data_array['send_text_email']) ? $send_text_email = $data_array['send_text_email'] : $send_text_email = " ";
		isset($data_array['append_to_file']) ? $append_to_file = $data_array['append_to_file'] : $append_to_file = " ";
		isset($data_array['data_file_name']) ? $data_file_name = $data_array['data_file_name'] : $data_file_name = "";
		isset($data_array['format']) ? $format = $data_array['format'] : $format = "";
		isset($data_array['delimiter']) ? $delimiter = $data_array['delimiter'] : $delimiter = " ";
		
	} else {
		$messages[] = "File is not readable; notify tech support";
	}
}
?>
<!DOCTYPE html>
<html lang=”en”>
<head>
	<title>PHP Form Processor Initializer</title>
	<link href="../_css/form.css" rel="stylesheet" type="text/css">
	<script src="../_js/jquery-1.6.2.js"></script>
	<script>
	
	//  This script manages the visibility of fields dependent on
	//  values of other fields
	
		$(document).ready(function() {
			upd_file_vis();
		});
			
		function upd_file_vis() {
		
			if ($("#append_to_file").is(':checked')) {
				$("#data_file_only").css('visibility','visible');
				$("#data_file_only").css('height','auto');
				upd_delim_vis();
			} else {
				$("#data_file_only").css('visibility','hidden');
				$("#data_file_only").css('height',0);
				$("#delim_div").css('visibility','hidden');
				$("#delim_div").css('height',0);
			}
		}
			
		function upd_delim_vis() {
			
			if ($("#delim_btn").is(':checked')) {
				$("#delim_div").css('visibility','visible');
				$("#delim_div").css('height','auto');
			} else {
				$("#delim_div").css('visibility','hidden');
				$("#delim_div").css('height',0);
			}
		}
	</script>
</head>
<body>
	<form action="form_processor_init.php" method="post" id="mainform" name="mainform">
	
		<h1>Form Processor Initializer</h1>
		<h2>Fill in the following fields to use them as defaults during form processing.</h2>
		<h2>Fields left blank will <em>not</em> be given defaults.</h2>
		
		<?php
			if (!empty($messages)) {

				echo "<p class=\"messages\">";
				
				foreach ($messages as $msg) {
					echo "<strong>$msg</strong><br>\n";
				}
				echo "</p>\n";
			}
		?>
	
       <fieldset>
	   
	   <div class="inputField">
            <div class="labelField">
                <label for="submit_to">Default email to send data to:</label>
            </div>
            <input name="submit_to" type="email" id="submit_to" size="50" value="<?php echo $submit_to;?>">
        </div>

        <div class="inputField">
            <div class="labelField">
                <label for="form_id">Default form ID:</label>
            </div>
            <input name="form_id" type="text" id="form_id" size="50" value="<?php echo $form_id;?>">
        </div>

        <div class="inputField">
            <div class="labelField">
                <label for="ok_url">Default URL after form processing completes successfully:</label>
            </div>
            <input name="ok_url" type="url" id="ok_url" size="80" value="<?php echo $ok_url;?>">
        </div>

        <div class="inputField">
            <div class="labelField">
                <label for="not_ok_url">Default URL after processing fails:</label>
            </div>
            <input name="not_ok_url" type="url" id="not_ok_url" size="80" value="<?php echo $not_ok_url;?>">
        </div>
		
		<div class="checkbox">
			<input type="checkbox" name="send_text_email" id="send_text_email" value="Y" <?php is_it_checked($send_text_email);?>>
			<label for="send_text_email">Send text email</label>
			<br>
			<input type="checkbox" name="append_to_file" id="append_to_file" value="Y" <?php is_it_checked($append_to_file);?> onchange="upd_file_vis();">
			<label for="append_to_file">Append to data file</label>
		</div>
		
		<div id="data_file_only" style="visibility:hidden;">

			<div class="inputField" name="data_file_name">
				<div class="labelField">
					<label for="data_file_name">Data file:</label>
				</div>
				<input name="data_file_name" type="text" id="data_file_name" size="100" value="<?php echo $data_file_name;?>">
			</div>
			
			<input type="radio" name="format" value="JSON" <?php is_radio_checked($format,"JSON"); ?> onchange="upd_delim_vis();" /> JSON file
			<input type="radio" name="format" value="JavaScript" <?php is_radio_checked($format,"JavaScript"); ?> onchange="upd_delim_vis();" /> JavaScript array
			<input type="radio" name="format" id="delim_btn" value="delim" <?php is_radio_checked($format,"delim"); ?> onchange="upd_delim_vis();" /> Delimited
	 
			<div class="inputField" name="delimiter" id="delim_div" style="visibility:hidden;">
				<div class="labelField">
					<label for="delimiter">Delimiter (default ','; enter '\t' for tab):</label>
					<input name="delimiter" type="text" id="delimiter" size="3" value="<?php echo $delimiter;?>">
				</div>
			</div>
			
		</div>

        <input type="submit" name="submit" value="Save">
		<input type="hidden" name="submitted" value="1" />
		</fieldset>

	</form>
</body>
</html>