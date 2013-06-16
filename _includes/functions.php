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
?>