<?php

# This file contains functions specific to form processor

function validate_data($form_id, $not_ok_url, $send_text_email, $append_to_file, $data_file_name) {

	// if no form ID, use form page's URL.
	if (!isset($form_id) || ($form_id == '')) {
		$form_id = $_SERVER['HTTP_REFERER'];
	}

	// if no not_ok_url, use form page's URL
	if (!isset($not_ok_url) || ($not_ok_url == '')) {
		$not_ok_url = $_SERVER['HTTP_REFERER'];
	}

	// If there is no reporting method chosen: 
	if ((!isset($send_text_email) || ($send_text_email == '')) &&
		(!isset($append_to_file)  || ($append_to_file == '')))    {
		
		// if there's an email recipient set up, send a text email; otherwise, append to file.
		if (isset($submit_to) && ($submit_to != '')) {
			$send_text_email = 'Y';
		} else {
			$append_to_file = 'Y';
		}
	}
	// if no data file name, use default of data_file.txt
	if (!isset($data_file_name) || ($data_file_name == '')) {
		$data_file_name = "data_file.txt";
	}
	
	return array($form_id, $not_ok_url, $send_text_email, $append_to_file, $data_file_name);
}

function add_to_email($email,$field_name,$first_field,$format="delim",$delimiter=",") {

	if (isset($_POST[$field_name]) && ($_POST[$field_name] != '')) {
		$field_value = $_POST[$field_name];
	} else {
		$field_value = " ";
	}	
	switch ($format) {
		case "JSON":
		case "JavaScript":
			if (!$first_field) {
				$email->add_to_body(",\n");
			}
			$name_value_pair = '"' . $field_name . '" : "' . $field_value . '"';
			$email->add_to_body($name_value_pair);
			break;
		case "delim":
		default:
			$name_value_pair = $field_name . $delimiter . $field_value . "\n";
			$email->add_to_body($name_value_pair);
	}
}

function add_to_text_file($datafile,$field_name,$first_field,$format="delim",$delimiter=",") {	

	if (isset($_POST[$field_name]) && ($_POST[$field_name] != '')) {
		$field_value = $_POST[$field_name];
	} else {
		$field_value = " ";
	}
	
	//  Cases: JSON, JS, text
	
	switch ($format) {
		case "JSON":
		case "JavaScript":
			if (!$first_field) {
				$datafile->add_to_file_contents(",\n");
			}
			$name_value_pair = '"' . $field_name . '" : "' . $field_value . '"';
			$datafile->add_to_file_contents($name_value_pair);
			break;
		case "delim":
		default:
			$name_value_pair = $field_name . $delimiter . $field_value . "\n";
			$datafile->add_to_file_contents($name_value_pair);
	}
}
?>