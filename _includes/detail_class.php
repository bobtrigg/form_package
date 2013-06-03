<?php

class Detail {

	protected $text;
	protected $type;
	protected $full;
	
	public function __construct($text, $type, $full) {
		$this->text = $text;
		$this->type = $type;
		$this->full = $full;
	}
	
	public function set_text($text) {
		$this->text = $text;
	}
	public function get_text() {
		return $this->text;
	}
	
	public function set_type($type) {
		$this->type = $type;
	}
	public function get_type() {
		return $this->type;
	}
	
	public function set_full($full) {
		$this->full = $full;
	}
	public function get_full() {
		return $this->full;
	}
}


?>