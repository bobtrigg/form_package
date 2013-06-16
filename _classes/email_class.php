<?php
class Email {

	protected $recipient;
	protected $subject;
	protected $from;
	protected $body

	public function set_recipient($recipient) {
		$this->recipient = $recipient;
	}
	public function get_recipient() {
		return $this->recipient;
	}
	
	public function set_subject($subject) {
		$this->subject = $subject;
	}
	public function get_subject() {
		return $this->subject;
	}
	
	public function set_from($from) {
		$this->from = $from;
	}
	public function get_from() {
		return $this->from;
	}
	
	public function set_body($body) {
		$this->body = $body;
	}
	public function get_body() {
		return $this->body;
	}
	

}
?>