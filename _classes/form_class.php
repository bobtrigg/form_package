<?php
class Form {

	function create_line($curr_dtl, $line_ndx, $html_file) {

		static $open_ul = true;
		static $close_ul = false;
		static $cat_str = "";
			
		if (isset($_POST['line_text' . $line_ndx]) && ($_POST['line_text' . $line_ndx] != '')) {
		
			$name_str = $_POST['line_text' . $line_ndx];
			$curr_dtl->set_text($name_str);

			// Remove "bad characters", comma and space
			$bad_chars = array(",", " ");
			$stripped_name = str_replace($bad_chars, "", $name_str);

			if (isset($_POST['line_type' . $line_ndx]) && $_POST['line_type' . $line_ndx] == 'category') {
			
				if ($close_ul) {
					$html_file->write_to_file("</ul>\n");
					$close_ul = false;
				}
			
				$html_file->write_to_file("\n<p class=\"title\"><strong>" . $name_str . "</strong></p>\n");
				$open_ul = true;
				$cat_str = $stripped_name;
				$curr_dtl->set_type('category');
			
			} else {
			
				if ($open_ul) {
					$html_file->write_to_file("<ul>\n");
					$open_ul = false;
				}
				$close_ul = true;
			
				if (isset($_POST['line_full' . $line_ndx]) && ($_POST['line_full' . $line_ndx] == 'on')) {
					$class_val = "class=\"full\"";
					$curr_dtl->set_full(true); 
				} else {
					$class_val = "";
					$curr_dtl->set_full(false); 
				}
		
				if ($cat_str != "") {
					$complete_name = $cat_str . ":" . $stripped_name;
				} else {
					$complete_name = $stripped_name;
				}
		
				$html_file->write_to_file("\n<li>\n");
				$html_file->write_to_file("\t<input name=\"" . $complete_name . "\" type=\"checkbox\" " . $class_val . " id=\"" . $complete_name . "\">\n");
				$html_file->write_to_file("\t<label for=\"" . $complete_name . "\">" . $name_str . "</label>\n");
				$html_file->write_to_file("</li>\n");	
				
				$curr_dtl->set_complete_name($complete_name);
				$curr_dtl->set_type('checkbox');
			}
		} else {   // Line had no content provided; return false
			return false;
		}
		
		return $curr_dtl;
	}
}
?>