//  This script manages the visibility of fields dependent on
//  values of other fields

$(document).ready(function() {
	upd_file_vis();
});
	
function upd_file_vis() {

	if ($("#append_to_file").is(':checked')) {
		$("#data_file_only").css('visibility','visible');
		$("#data_file_only").css('height','auto');
		upd_delim_vis();
	} else {
		$("#data_file_only").css('visibility','hidden');
		$("#data_file_only").css('height',0);
		$("#delim_div").css('visibility','hidden');
		$("#delim_div").css('height',0);
	}
}
	
function upd_delim_vis() {
	
	if ($("#delim_btn").is(':checked')) {
		$("#delim_div").css('visibility','visible');
		$("#delim_div").css('height','auto');
	} else {
		$("#delim_div").css('visibility','hidden');
		$("#delim_div").css('height',0);
	}
}