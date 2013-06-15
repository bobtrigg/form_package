<?php
class Form {

	static $open_ul = true;
	static $close_ul = false;
	static $cat_str = "";
			
	function remove_illegal_chars($text_str) {
		$bad_chars = array(",", " ");
		return str_replace($bad_chars, "", $text_str);
	}
	
	function process_category($html_file, $curr_dtl) {
	
		if (Form::$close_ul) {
			$html_file->add_to_file_contents("</ul>\n");
			Form::$close_ul = false;
		}
	
		$html_file->add_to_file_contents("\n<p class=\"title\"><strong>" . $curr_dtl->get_text() . "</strong></p>\n");
		Form::$open_ul = true;
		Form::$cat_str = $curr_dtl->get_stripped_name();
		$curr_dtl->set_type('category');			
	}

	function set_complete_name($curr_dtl){
	
		if (Form::$cat_str != "") {
			$complete_name = Form::$cat_str . ":" . $curr_dtl->get_stripped_name();
		} else {
			$complete_name = $curr_dtl->get_stripped_name();
		}
		$curr_dtl->set_complete_name($complete_name);
	}
	
	function set_full_status($curr_dtl, $line_ndx) {
	
		if (isset($_POST['line_full' . $line_ndx]) && ($_POST['line_full' . $line_ndx] == 'on')) {
			$class_val = "class=\"full\"";
			$curr_dtl->set_full(true); 
		} else {
			$class_val = "";
			$curr_dtl->set_full(false); 
		}
		
		return $class_val;
	}
	
	function write_checkbox_to_form($html_file, $curr_dtl, $line_ndx) {

		$html_file->add_to_file_contents("\n<li>\n");
		$html_file->add_to_file_contents("\t<input name=\"" . $curr_dtl->get_complete_name . "\" type=\"checkbox\" " . $this->set_full_status($curr_dtl, $line_ndx) . " id=\"" . $curr_dtl->get_complete_name . "\">\n");
		$html_file->add_to_file_contents("\t<label for=\"" . $curr_dtl->get_complete_name . "\">" . $curr_dtl->get_text() . "</label>\n");
		$html_file->add_to_file_contents("</li>\n");	
	}

	function process_checkbox($html_file, $curr_dtl, $line_ndx) {
	
		if (Form::$open_ul) {
			$html_file->add_to_file_contents("<ul>\n");
			Form::$open_ul = false;
		}
		Form::$close_ul = true;
	
		$this->set_complete_name($curr_dtl);
		
		$this->write_checkbox_to_form($html_file, $curr_dtl, $line_ndx);
		
		$curr_dtl->set_type('checkbox');
	}
	
	function create_line($curr_dtl, $line_ndx, $html_file) {

		if (isset($_POST['line_text' . $line_ndx]) && ($_POST['line_text' . $line_ndx] != '')) {
		
			$curr_dtl->set_text($_POST['line_text' . $line_ndx]);
			$curr_dtl->set_stripped_name($this->remove_illegal_chars($curr_dtl->get_text()));

			if (isset($_POST['line_type' . $line_ndx]) && $_POST['line_type' . $line_ndx] == 'category') {
				$this->process_category($html_file, $curr_dtl);
			} else {				
				$this->process_checkbox($html_file, $curr_dtl, $line_ndx);			
			}
			
		} else {   // Line had no content provided; return false
			return false;
		}
		
		return $curr_dtl;
	}
}
?>