<?php

class JSON_Data {

	protected $data_values = array();
	protected $return_values = array();
	
	protected $data_names = array('submit_to','form_id','ok_url','not_ok_url','send_text_email','append_to_file','data_file_name','format','delimiter');

	public function __construct($json_file) {
		$this->json_file = $json_file;
	}
	
	protected function ensure_json_value($ndx_name) {
	
		return (isset($this->data_values[$ndx_name]) ? $this->data_values[$ndx_name] : " ");
	}
	
	function get_json_data($json_file) {

	//  get_json_data gets the contents of the JSON data file, sets defaults for undefined data
	//  and returns an array of the data.

		if (file_exists($json_file) && is_readable($json_file)) {
			
			$json_data = nl2br(file_get_contents($json_file));
			$this->data_values = json_decode($json_data,true);
			
		} else {
		
			$this->data_values = array();
		}
		
		foreach ($this->data_names as $data_name) {
			$this->return_values[] = $this->ensure_json_value($data_name);
		}
			
		return $this->return_values;
	}
}
?>