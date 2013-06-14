<?php
#############################################################################
#
#  This file contains functions used exclusively by form_generator.php
#
#############################################################################

//  Functions used in this script file

function set_num_lines() {

	//  Set $num_lines, according to this priority:
	//    1) use value in URL if exists
	//    2) otherwise use include file if it exists
	//    3) otherwise use hard-coded default

	$num_lines = 8;
	
	include("../_includes/defaults_inc.php");
	
	if (isset($_GET['num_lines']) && ($_GET['num_lines'] != '')) {
		$num_lines = $_GET['num_lines'];
	}
	
	return $num_lines;
}





?>