<?php
###############################################################################
#
#  form_processor_init.php
#  Created in June 2013 by Bob Trigg (bobtrigg.com)
#
#  This simple sticky form gathers user-submitted default data used in the 
#  universal form processor form_processor.php. 
#  Default values are stored in a JSON file, defaults.json. 
#  Existing values are retrieved from the file before the form is displayed.
#
###############################################################################

require_once("../_includes/file_names_inc.php");
require_once("../_includes/functions.php");
require_once("../_classes/json_class.php");

$json_file = JSON_FILE;

//  Set default values
$submit_to = $form_id = $ok_url = $not_ok_url = $send_text_email = $append_to_file = $data_file_name = "";
$format = "delim";
$delimiter = ',';

$messages = array();

if (isset($_POST['submitted'])) {

	//  Grab and write form data to JSON file

	$data_array = array(); // Array of JSON data
	
	//  Get form data
	
	$submit_to = $data_array['submit_to'] = set_field('submit_to',$submit_to);
	$form_id = $data_array['form_id'] = set_field('form_id',$form_id);
	$ok_url = $data_array['ok_url'] = set_field('ok_url',$ok_url);
	$not_ok_url = $data_array['not_ok_url'] = set_field('not_ok_url',$not_ok_url);
	$send_text_email = $data_array['send_text_email'] = set_field('send_text_email',$send_text_email);
	$append_to_file = $data_array['append_to_file'] = set_field('append_to_file',$append_to_file);
	$data_file_name = $data_array['data_file_name'] = set_field('data_file_name',$data_file_name);
	$format = $data_array['format'] = set_field('format',$format);
	$delimiter = $data_array['delimiter'] = set_field('delimiter',$delimiter);

	//  Create and write JSON data file
	
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

//  Read JSON file and populate form field variables

$json_object = new JSON_Data($json_file);

list($submit_to, $form_id, $ok_url, $not_ok_url, $send_text_email, $append_to_file, $data_file_name, $format, $delimiter) = $json_object->get_json_data($json_file);

if (file_exists($json_file) && !is_readable($json_file)) {
	$messages[] = "Existing data file is not readable; notify tech support";
}
?>
<!DOCTYPE html>
<html lang=”en”>
<head>
	<title>PHP Form Processor Initializer</title>
	<link href="../_css/form.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="../_js/jquery-1.6.2.js"></script>
	<script type="text/javascript" src="../_js/set_visibility.js"></script>
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
            <input name="ok_url" type="text" id="ok_url" size="80" value="<?php echo $ok_url;?>">
        </div>

        <div class="inputField">
            <div class="labelField">
                <label for="not_ok_url">Default URL after processing fails:</label>
            </div>
            <input name="not_ok_url" type="text" id="not_ok_url" size="80" value="<?php echo $not_ok_url;?>">
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