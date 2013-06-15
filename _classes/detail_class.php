<?php
###############################################################################
#
#  detail_class.php
#  Created June 2013 by Bob Trigg (bobtrigg.com)
#  
#  This very simple class defines objects which store data from form input
#  in script form_generator.php. Data stored is the text of a label in a form
#  file to be generated, a indicator of the type of line, and a Boolean
#  indicating whether the shift is full.
#
#  Getters and setters are provided for each property.
#
###############################################################################

class Detail {

	protected $text;
	protected $type;
	protected $full;
	protected $complete_name;
	
	public function __construct($text, $type, $full) {
		$this->text = $text;
		$this->type = $type;
		$this->full = $full;
		$this->complete_name = "";
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
	
	public function set_complete_name($complete_name) {
		$this->complete_name = $complete_name;
	}
	public function get_complete_name() {
		return $this->complete_name;
	}
}
?>