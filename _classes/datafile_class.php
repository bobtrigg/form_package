<?php
require_once("../_classes/file_class.php");

class Datafile extends File {

	public function __construct($filename) {
		$this->file_name = $filename;
	}
	
	function prep_file($format, $form_id) {

		if (!file_exists($this->get_file_name())) {
			$this->open_file('a');
		} else {

			if (is_writable($this->get_file_name())) {
				$this->open_file('a');
				$this->add_to_file_contents("\n");
			}	
		}
		$this->add_to_file_contents("Data was submitted at " . date("M j, Y, g:i A") . " PST from " . $form_id . "\n");
		
		if ($format == "JSON" || $format == "JavaScript") {
			$this->add_to_file_contents("{\n");
		}
	}
}	
?>