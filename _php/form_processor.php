<?php
###############################################################################
#  PHP Form Processor
#  Created April, 2013 by Bob Trigg

#  This script accepts user-entered data from a web form and
#  reports data by sending email and/or appending to a data file.
#  The data file can be created in one of several formats: JSON,
#  JavaScript array, or delimited.

#  The script gets a list of data fields to be reported from a 
#  hidden field in the form, field_names. Included in this package is 
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

###############################################################################

date_default_timezone_set('America/Los_Angeles');
$messages = array();
// $messages[] = '<h3>return here</h3>'; Uncommenting this line will prevent redirection after processing

require_once ('../_includes/process_field.php');  // Includes function to process each field
require_once("../_includes/base_pack.php");
require_once("../_classes/email_class.php");
require_once("../_classes/datafile_class.php");

$json_file = JSON_FILE;

# grab JSON data. This data may be overwritten by form data; any fields w/out values from form will assume JSON data

$json_object = new JSON_Data($json_file);

list($submit_to, $form_id, $ok_url, $not_ok_url, $send_text_email, $append_to_file, $data_file_name, $format, $delimiter) = $json_object->get_json_data($json_file);

# Put in the form-supplied data, which when present overrides the defaults

$field_names = set_field('field_names'," ");
$submit_to = set_field('submit_to',$submit_to);
$ok_url = set_field('ok_url',$ok_url);
$not_ok_url = set_field('not_ok_url',$not_ok_url);
$form_id = set_field('form_id',$form_id);

####  Ready to rock! Start the data validation...

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

// Set up email fields if email is requested

if (isset($send_text_email) && ($send_text_email != '')) {

	$email = new Email($submit_to, "Data submitted via form " . $form_id, "From: admin@marinbike.org", "");
	
	if ($format == "JSON" || $format == "JavaScript") {
		$email->add_to_body("{\n");
	}	
}
	
// Set up file if file is requested.
// Assume $data_file_name was set if needed during parm checking.

if (isset($data_file_name) && ($data_file_name != '')) {

	$datafile = new Datafile($data_file_name);

	if (!file_exists($datafile->get_file_name())) {
		$datafile->open_file('a');
	} else {

		if (is_writable($datafile->get_file_name())) {
			$datafile->open_file('a');
			$datafile->add_to_file_contents("\n");
		}	
	}
	$datafile->add_to_file_contents("Data submitted at " . date("M j, Y, g:i A") . " PST from " . $form_id . "\n");
	if ($format == "JSON" || $format == "JavaScript") {
		$datafile->add_to_file_contents("{\n");
	}
}	

//  Put the fields to be reported into an array

$field_name_array = explode(',',$field_names);
$first_field = true;

foreach ($field_name_array as $field) { 

	if ($send_text_email) {
		add_to_email($email,$field,$first_field,$format,$delimiter);	
	} 
	if ($append_to_file) {
		add_to_text_file($datafile,$field,$first_field,$format,$delimiter);	
	}
	$first_field = false;
}

//  Send email if requested
	
if ((isset($send_text_email) && ($send_text_email != ''))) {

	if ($format == "JSON" || $format == "JavaScript") {
		$email->add_to_body("\n}\n");
	}	

	mail($email->get_recipient(), $email->get_subject(), $email->get_body(), $email->get_from());
}

//  Data file is requested

if (isset($data_file_name) && ($data_file_name != '')) {
	if ($format == "JSON" || $format == "JavaScript") {
		$datafile->add_to_file_contents("\n}\n");
	}
	$datafile->write_to_file($datafile->get_contents());
	$datafile->close_file();
}

//  Go to the new URL indicated in the form's code. Stay here if URL is unspecified.

if (isset($ok_url) && ($ok_url != '') && empty($messages)) {
	if (!headers_sent($filename, $linenum)) {
		header("Location: " . $ok_url);
		exit();
	}
}
?>

<!--  The following HTML will only be deployed if no forwarding page URL is provided.  -->

<!DOCTYPE html>
<html lang=”en”>
<head>
<title>Bob Trigg's excellent PHP form processor</title>
</head>
<body>
<h1>Your form data has been accepted and processed.</h1>

<?php
	if (!empty($messages)) {

		echo "<p>";
		
		foreach ($messages as $msg) {
			echo "<strong>$msg</strong><br>\n";
		}
		echo "</p>\n";
	}
?>

<?php //echo $email->get_body(); ?>

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