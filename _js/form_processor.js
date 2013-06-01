$("document").ready( function () {
//##################################################################
// This script populates the field_names hidden field in an
// html form. It looks for all input fields in a form with the
// action 'form_processor.php', and puts all their names in a
// comma-delimited list. It also allows for adding hidden fields
// to the list by the use of the class 'sendtoform'.
//
// Additionally, if any of the hidden field 'submit_to', 'form_id',
// 'ok_url', or 'not_ok_url' are missing, this script will attempt
// to populate them using the function set_fields
//##################################################################
	
	var field_names = "";
	
	//  Find all input fields and add their names to the field_names list
	$("form[action$='form_processor.php'] input[type!=hidden]").each(function() {
	
		if ($(this).attr("type") != "submit") {
		
			if (field_names) {
				field_names += ",";
			}
			
			field_names += $(this).attr("name");
		}
		// $(this).css("border","3px solid red");  // Use this for testing...
	});
		
	//  Add any hidden fields with class 'sendtoform' to the field_names list
	$("form[action$='_php/form_processor.php'] input[class~=sendtoform]").each(function() {
		if (field_names) {
			field_names += ",";
		}
		field_names += $(this).attr("name");
		// $(this).css("border","3px solid red");  // Use this for testing...
	});
		
	var field_list = $("input[name='field_names']");

	//  Replace hidden field field_list, or create it new.
	if (field_list.length != 0) {
		field_list.attr("value",field_names);
	} else {
		var field_list = '<input type="hidden" name="field_names" value="' + field_names + '">';
		$("input[type='submit']").after(field_list);
	}
	
	//  Create additional hidden fields if they don't exist.
	if ($("input[name='submit_to']").length == 0 ||
		$("input[name='form_id']").length == 0 ||
		$("input[name='ok_url']").length == 0 ||
		$("input[name='not_ok_url']").length == 0 ) {
	
		set_fields();
	}
	
});

function set_fields(field_name) {
	
	// Initialize a request
	var request;
	if (window.XMLHttpRequest) {
		request=new XMLHttpRequest()
	} else {
		request=new ActiveXObject('Microsoft.XMLHTTP');
	}

	//  Open and process the JSON file form_defaults.json
	
	request.open('GET', '/_php/form_defaults.json');

	request.onreadystatechange = function() {
		if ((request.status === 200) && (request.readyState === 4)) {
			
			var info = JSON.parse(request.responseText);
			var field_names = ['submit_to','form_id','ok_url','not_ok_url'];
			
			for (i=0; i<field_names.length; i++) {
			
				var field_name = field_names[i];       //  Name of the field (from array)
				var field_value = info[field_name];    //  Value of that field (from JSON)
				
				if (field_value && field_value != " ") {   // Add the new hidden field ONLY if data exists in the JSON file
				
					var search_str = "input[name='" + field_name + "']";
					
					if ($(search_str).length == 0) {  // Add new hidden field ONLY IF it doesn't already exist!

						var input_tag = '<input type="hidden" name="' + field_name + '" value="' + field_value + '">';
						
						$("input[type='submit']").after(input_tag);				
					}
				}
			}
		}
	}
	request.send();
}



