<?php

################################################################################
# Define a function to process each field:
# The function's single parameter is the name of the field to be processed.
# Function uses the field name to retrieve the value from $_POST
################################################################################
function process_field($field_name,$send_text_email,$append_to_file,$handle,$first_field,$format="delim",$delimiter=",") {

	global $e_body;
	
	$field_value = $_POST[$field_name];
	
	if ($send_text_email) {
		
		switch ($format) {
			case "JSON":
			case "JavaScript":
				if (!$first_field) {
					$e_body .= ",\n";
				}
				$name_value_pair = '"' . $field_name . '" : "' . $field_value . '"';
				$e_body .=  $name_value_pair;
				break;
			case "delim":
			default:
				$name_value_pair = $field_name . $delimiter . $field_value . "\n";
				$e_body .=  $name_value_pair;
		}
	}
	
	if ($append_to_file) {

		//  Cases: JSON, JS, text
		
		switch ($format) {
			case "JSON":
			case "JavaScript":
				if (!$first_field) {
					fwrite($handle,",\n");
				}
				$name_value_pair = '"' . $field_name . '" : "' . $field_value . '"';
				fwrite($handle, $name_value_pair);
				break;
			case "delim":
			default:
				$name_value_pair = $field_name . $delimiter . $field_value . "\n";
				fwrite($handle, $name_value_pair);
		}
	}
}
?>