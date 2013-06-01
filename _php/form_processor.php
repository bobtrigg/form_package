<?php
#  PHP Form Processor
#  Created April, 2013 by Bob Trigg

#  This script accepts user-entered data from a web form and
#  reports data by sending email and/or appending to a data file.
#  The data file can be created in one of several formats: JSON,
#  JavaScript array, or delimited.

#  The script gets a list of data fields to be reported from a 
#  hidden field in the form, field_names. Included in the package is 
#  a JavaScript file which parses a web page containing a form 
#  and sets up the list of data fields in the form.
	
#  The following input data are expected, either from the form
#  or from a default file: the email recipient, the field
#  list, the name of the form, URLs for success and failure,
#  and the reporting methods (files or email).

#  Default values: 
#  The reporting method, if unspecified, defaults to email, 
#    but if the recipient URL is not specified, the default becomes file.
#  The data format defaults to delimited; the default delimiter is a comma (,).
#  The default data file name id "data_file.txt".
#  The default for successful execution is a simple message on this page;
#    the default for unsuccessful execution is the sending form page.

date_default_timezone_set('America/Los_Angeles');
require_once ('file_names.inc');

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

# grab JSON data. This data may be overwritten by form data; any fields w/out values from form will assume JSON data

if (file_exists(JSON_FILE) && is_readable(JSON_FILE)) {
	
	$json_data = nl2br(file_get_contents(JSON_FILE));
	$data_array = json_decode($json_data,true);
	
	isset($data_array['submit_to']) ? $submit_to = $data_array['submit_to'] : $submit_to = " ";
	isset($data_array['form_id']) ? $form_id = $data_array['form_id'] : $form_id = "";
	isset($data_array['ok_url']) ? $ok_url = $data_array['ok_url'] : $ok_url = "";
	isset($data_array['not_ok_url']) ? $not_ok_url = $data_array['not_ok_url'] : $not_ok_url = "";
	isset($data_array['send_text_email']) ? $send_text_email = $data_array['send_text_email'] : $send_text_email = "";
	isset($data_array['append_to_file']) ? $append_to_file = $data_array['append_to_file'] : $append_to_file = "";
	isset($data_array['data_file_name']) ? $data_file_name = $data_array['data_file_name'] : $data_file_name = DEFAULT_DATA_FILE_NAME;
	isset($data_array['format']) ? $format = $data_array['format'] : $format = "delim";
	isset($data_array['delimiter']) ? $delimiter = $data_array['delimiter'] : $delimiter = ",";
}

# Put in the form-supplied data, which when present overrides the defaults

if (isset($_POST['field_names']) && ($_POST['field_names'] != '')) {
	$field_names = $_POST['field_names'];
}
if (isset($_POST['submit_to']) && ($_POST['submit_to'] != '')) {
	$submit_to = $_POST['submit_to'];
}
if (isset($_POST['ok_url']) && ($_POST['ok_url'] != '')) {
	$ok_url = $_POST['ok_url'];
}
if (isset($_POST['not_ok_url']) && ($_POST['not_ok_url'] != '')) {
	$not_ok_url = $_POST['not_ok_url'];
}
if (isset($_POST['form_id']) && ($_POST['form_id'] != '')) {
	$form_id = $_POST['form_id'];
}

####  Ready to rock! Start the data validation...

// if no form ID, use form page's URL.
if (!isset($form_id) || ($form_id == '')) {
	$form_id = $_SERVER['HTTP_REFERER'];
}

// if no not_ok_url, use form page's URL
if (!isset($not_ok_url) || ($not_ok_url == '')) {
	$not_ok_url = $_SERVER['HTTP_REFERER'];
}

// If there is not reporting method chosen: 
if ((!isset($send_text_email) || ($send_text_email == '')) &&
    (!isset($append_to_file)  || ($append_to_file == '')))    {
	
	// if there's an email recipient set up, send a text email; otherwise, append to file.
	if (isset($submit_to) && ($submit_to != '')) {
		$send_text_email = 'Y';
	} else {
		$append_to_file = 'Y';
	}
}

// Set up email fields if email is requested

if (isset($send_text_email) && ($send_text_email != '')) {
	
	$e_recipient = $_POST['submit_to'];
	$e_subject = "Data submitted via form " . $form_id;
	$e_from = "From: admin@marinbike.org";
	$e_body = "";  // Need this set before processing individual fields!
	
	if ($format == "JSON" || $format == "JavaScript") {
		$e_body .= "{\n";
	}	
}
	
// Set up file if file is requested.
// Assume $data_file_name was set if needed during parm checking.

if (isset($data_file_name) && ($data_file_name != '')) {

	if (!file_exists($data_file_name)) {
		$handle = fopen($data_file_name,'a');
	} else {

		if (is_writable($data_file_name)) {
			$handle = fopen($data_file_name,'a');
			fwrite($handle, "\n");
		}	
	}
	fwrite($handle, "Data submitted at " . date("M j, Y, g:i A") . " PST from " . $form_id . "\n");
	if ($format == "JSON" || $format == "JavaScript") {
		fwrite($handle, "{\n");
	}
}	

//  Put the fields to be reported into an array
$field_name_array = explode(',',$field_names);
$first_field = true;

foreach ($field_name_array as $field) {    //  Call function to do the dirty work...
	process_field($field,$send_text_email,$append_to_file,$handle,$first_field,$format,$delimiter);
	$first_field = false;
}

//  Send email if requested
	
if ((isset($send_text_email) && ($send_text_email != ''))) {

	if ($format == "JSON" || $format == "JavaScript") {
		$e_body .= "\n}\n";
	}	

	//  This line adds content which can be used to identify the message as not spam
	if (!in_array('spamprotector',$field_name_array)) {
		$e_body .= "spamprotector = fromwebformviawebhost\n";
	}

	mail($e_recipient, $e_subject, $e_body, $e_from);
}

if (isset($data_file_name) && ($data_file_name != '')) {
	if ($format == "JSON" || $format == "JavaScript") {
		fwrite($handle, "\n}\n");
	}
	fclose($handle);
}

//  Go to the new URL indicated in the form's code. Stay here if URL is unspecified.

if (isset($ok_url) && ($ok_url != '')) {
	if (!headers_sent($filename, $linenum)) {
		header("Location: " . $ok_url);
		exit();
	}
}
?>

<!--  The following HTML will only be deployed if no forwarding page URL is provided.  -->

<html>
<head>
<title>Bob Trigg's excellent PHP form processor</title>
</head>
<body>
<h1>Your form data has been accepted and processed.</h1>
<?php //echo $e_body; ?>

<?php
	if ((isset($send_text_email) && ($send_text_email != ''))) {
		echo "<h2>An email with your data was sent to " . $submit_to . "</h2>\n";
	}
	if ((isset($append_to_file) && ($append_to_file != ''))) {
		echo "<h2>Your data has been saved in a text file</h2>\n";
	}
	$URL = $_SERVER['HTTP_REFERER'];
	echo '<p><a href="' . $URL . '">Return to submitted form</a><br>';
	echo '<a href="' . substr($URL,0,strpos($URL, '/',10)+1) . '">Go to website home page</a></p>';
?>
</body>
</html>