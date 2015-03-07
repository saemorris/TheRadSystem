// Display an error for the user.
function show_message(message) {
	var msg;
	msg = document.getElementById("error_msg");
	msg.textContent = message;
	msg.value = message;
	msg.hidden = false;
}
// Set a function to onSubmit
function validateInput(form) {
	var mfields = document.getElementsByTagName("input");
	var field;
	for (i=0; i < mfields.length; i++) {
		field = mfields[i];
		if (field.getAttribute("assertion") != null && 
	field.getAttribute("assertion").search("not_blank")>=0) {
			if (field.value == "" || field.value == null) {
				show_message("Field '"+field.name+"' cannot be left blank.");
				return false;
			}
		}
	}
}
