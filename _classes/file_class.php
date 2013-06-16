<?php

class File {

	protected $file_name = " ";
	protected $handle;
	protected $contents = "";
	
	function get_file_name() {
		return $this->file_name;
	}
	function add_to_file_contents($content) {
	//  In lieu of tradtional setter: contents is always append, never rewritten
		$this->contents .= $content;
	}

	 function create_file_from_content() {
		$this->open_file();
		$this->write_to_file($this->contents);
		$this->close_file();
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