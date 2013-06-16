<?php
class Email {

	protected $recipient;
	protected $subject;
	protected $from;
	protected $body;
	
	public function __construct($recipient, $subject, $from, $body) {
		$this->recipient = $recipient;
		$this->subject = $subject;
		$this->from = $from;
		$this->body = $body;
	}

	public function add_to_body($content) {
		$this->body .= $content;
	}
	public function send_mail(){
		mail($this->get_recipient(), $this->get_subject(), $this->get_body(), $this->get_from());
	}
	
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