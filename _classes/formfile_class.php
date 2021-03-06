<?php
require_once("../_classes/file_class.php");

class Formfile extends File {

	function set_form_file_name() {
	
		if (isset($_POST['file_name']) && ($_POST['file_name'] != '')) {
		
			$this->file_name = trim($_POST['file_name']);
			$this->file_name = preg_replace('/ /','',$this->file_name);   // Remove spaces
			$this->file_name = preg_replace('/\./','',$this->file_name);   // Remove periods
			$this->ensure_suffix();

		} else {
			$this->file_name = "yourform.html";
		}
	}
	function ensure_suffix() {
	
		//  If string does not end in 'html', add '.html' at the end.

		//  Standardize b4 search: if file name ends in 'htm', add an 'l'
		if (preg_match('/htm$/i',$this->file_name)) {
			$this->file_name .= 'l';
		}
		//  Now replace suffix delimiting period, removed above
		if (preg_match('/html$/i',$this->file_name)) {
			$this->file_name = substr($this->file_name, 0, -4) . '.html';
		} else {
			$this->file_name .= '.html';
		}	
	}
	function create_head_section($HTMLtitle, $header_file_name) {
	
		$this->add_to_file_contents("<!DOCTYPE html>\n<html lang='en'>\n<head>");
		$this->add_to_file_contents("<title>" . $HTMLtitle . "</title>");
		$this->add_to_file_contents(file_get_contents($header_file_name));
	}
	function add_standard_content($form_header, $description, $contact_info_file) {
		
		//  Write header text from form into html file
		$this->add_to_file_contents("<h1><br>" . $form_header . "</h1>");
		$this->add_to_file_contents("<p class=\"clear\">&nbsp;</p>");

		//  Write description text from form into html file
		$this->add_to_file_contents("<p>" . $description . "</p>");
		
		//  Write contact info fields from boilerplate file into html file
		$this->add_to_file_contents(file_get_contents($contact_info_file));
	}
	
	function add_hidden_fields($html_file, $submit_to, $HTMLtitle, $field_names) {

		if (isset($submit_to) && ($submit_to != '')) {
			$html_file->add_to_file_contents("<input type=\"hidden\" name=\"submit_to\" value=\"" . $submit_to . "\">\n");
		}
		$html_file->add_to_file_contents("<input type=\"hidden\" name=\"form_id\" value=\"" . $HTMLtitle . "\">\n");
		$html_file->add_to_file_contents("<input type=\"hidden\" name=\"Event\" id=\"Event\" value=\"" . $HTMLtitle . "\">\n");
		$html_file->add_to_file_contents("<input type=\"hidden\" name=\"data_order\" value=\"" . $field_names . "\">\n");
	}
}
?>