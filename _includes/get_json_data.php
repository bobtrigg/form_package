<?php
	$json_data = nl2br(file_get_contents($json_file));
	$data_array = json_decode($json_data,true);
	
	isset($data_array['submit_to']) ? $submit_to = $data_array['submit_to'] : $submit_to = " ";
	isset($data_array['form_id']) ? $form_id = $data_array['form_id'] : $form_id = " ";
	isset($data_array['ok_url']) ? $ok_url = $data_array['ok_url'] : $ok_url = " ";
	isset($data_array['not_ok_url']) ? $not_ok_url = $data_array['not_ok_url'] : $not_ok_url = " ";
	isset($data_array['send_text_email']) ? $send_text_email = $data_array['send_text_email'] : $send_text_email = " ";
	isset($data_array['append_to_file']) ? $append_to_file = $data_array['append_to_file'] : $append_to_file = " ";
	isset($data_array['data_file_name']) ? $data_file_name = $data_array['data_file_name'] : $data_file_name = "";
	isset($data_array['format']) ? $format = $data_array['format'] : $format = "";
	isset($data_array['delimiter']) ? $delimiter = $data_array['delimiter'] : $delimiter = " ";
?>