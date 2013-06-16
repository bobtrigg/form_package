<?php

################################################################################
# Define a function to process each field:
# The function's single parameter is the name of the field to be processed.
# Function uses the field name to retrieve the value from $_POST
################################################################################
function add_to_email($email,$field_name,$first_field,$format="delim",$delimiter=",") {

	$field_value = $_POST[$field_name];
	
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

	$messages[] = "adding " . $field_name . " to data file.";
	$field_value = $_POST[$field_name];
	
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