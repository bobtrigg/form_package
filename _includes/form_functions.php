<?php
#############################################################################
#
#  This file contains functions used exclusively by form_generator.php
#
#############################################################################

//  Functions used in this script file

function set_num_lines() {

	//  Set $num_lines, according to this priority:
	//    1) use value in URL if exists
	//    2) otherwise use include file if it exists
	//    3) otherwise use hard-coded default

	$num_lines = 8;
	
	include("../_includes/defaults_inc.php");
	
	if (isset($_GET['num_lines']) && ($_GET['num_lines'] != '')) {
		$num_lines = $_GET['num_lines'];
	}
	
	return $num_lines;
}
function validate_input() {

	$HTMLtitle = validate_title();
	$form_header = validate_form_header();
	$description = validate_desc();
	
	return array($HTMLtitle, $form_header, $description);
}
function validate_title() {

	if (isset($_POST['HTMLtitle']) && ($_POST['HTMLtitle'] != '')) {
		$HTMLtitle = trim($_POST['HTMLtitle']);
		$HTMLtitle = preg_replace('/\\\\/','',$HTMLtitle);
	} else {
		$HTMLtitle = "Working title";
	}
	return $HTMLtitle;
}
function validate_form_header() {
	
	if (isset($_POST['form_header']) && ($_POST['form_header'] != '')) {
		$form_header = trim($_POST['form_header']);
		$form_header = preg_replace('/\\\\/','',$form_header);
	} else {
		$form_header = "Data input form";
	}
	return $form_header;
}
function validate_desc() {

	
	if (isset($_POST['description']) && ($_POST['description'] != '')) {
		$description = trim(nl2br($_POST['description']));
		//  Remove backslashes preceding a single quote
		$description = preg_replace('/\\\\/','',$description);
		//  Convert special characters to ampersand code:
		// $description = htmlentities($description);
	} else {
		$description = " ";
	}
	return $description;
}
?>