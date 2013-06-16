<?php
require_once("../_classes/file_class.php");

class Datafile extends File {

	public function __construct($filename) {
		$this->file_name = $filename;
	}
}
?>