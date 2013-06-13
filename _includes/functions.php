<?php

function set_field($fld_name, $fld_val) {

//  This function looks for a field specified by the parameter
//  in the _POST array of user-entered data.
//  If the data exists, it returns the field value;
//  otherwise it returns the existing value, passed as a 2nd parameter.

	if (isset($_POST[$fld_name]) && ($_POST[$fld_name] != '') && ($_POST[$fld_name] != ' ')) {
		return $_POST[$fld_name];
	} else {
		return $fld_val;
	}
}

// The following two functions set the 'checked' value for
// checkboxes and radio buttons in the form.

function is_it_checked($fld_val) {
	if ($fld_val == 'Y') { echo 'checked'; }
}
function is_radio_checked($radio_name, $fld_val) {
	if ($radio_name == $fld_val) { echo 'checked'; }
}

function get_json_data($json_file) {

//  get_json_data gets the contents of the JSON data file, sets defaults for undefined data
//  and returns an array of the data.

	
	if (file_exists($json_file) && is_readable($json_file)) {
		
		$json_data = nl2br(file_get_contents($json_file));
		$data_array = json_decode($json_data,true);
		
	} else {
	
		$data_array = array();
	}
	
	$submit_to = (isset($data_array['submit_to']) ? $data_array['submit_to'] : " ");
	
	$form_id = (isset($data_array['form_id']) ? $data_array['form_id'] : " ");
	$ok_url = (isset($data_array['ok_url']) ? $data_array['ok_url'] : " ");
	$not_ok_url = (isset($data_array['not_ok_url']) ? $data_array['not_ok_url'] : " ");
	$send_text_email = (isset($data_array['send_text_email']) ? $data_array['send_text_email'] : " ");
	$append_to_file = (isset($data_array['append_to_file']) ? $data_array['append_to_file'] : " ");
	$data_file_name = (isset($data_array['data_file_name']) ? $data_array['data_file_name'] : "");
	$format = (isset($data_array['format']) ? $data_array['format'] : "");
	$delimiter = (isset($data_array['delimiter']) ? $data_array['delimiter'] : " ");
	
	// return $data_array;
	return array($submit_to, $form_id, $ok_url, $not_ok_url, $send_text_email, $append_to_file, $data_file_name, $format, $delimiter);
}

?>