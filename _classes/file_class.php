<?php

class File {

	protected $file_name;
	protected $handle;
	
	function set_file_name($file_name) {
	
		if (isset($_POST['file_name']) && ($_POST['file_name'] != '')) {
		
			$this->file_name = trim($file_name);
			$this->file_name = preg_replace('/ /','',$this->file_name);   // Remove spaces
			$this->file_name = preg_replace('/\./','',$this->file_name);   // Remove periods

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
		} else {
			$this->file_name = "yourform.html";
		}
	}
	
	function get_file_name() {
		return $this->file_name;
	}
	
	function open_file() {
		$this->handle = fopen($this->file_name,'w');
	}
	
	function write_to_file($file_string) {
		fwrite($this->handle, $file_string);
	}
	function close_file() {
		fclose($this->handle);
	}
}
?>