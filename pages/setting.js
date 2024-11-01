function _sepopups_submit() {
	if(document.sep_form.sep_title.value == "") {
		alert(sepopups_adminscripts.sep_title);
		document.sep_form.sep_title.focus();
		return false;
	}
	else if(document.sep_form.sep_text.value == "") {
		alert(sepopups_adminscripts.sep_text);
		document.sep_form.sep_text.focus();
		return false;
	}
	else if(document.sep_form.sep_group.value == "" && document.sep_form.sep_group_txt.value == "") {
		alert(sepopups_adminscripts.sep_group);
		document.sep_form.sep_group_txt.focus();
		return false;
	}
	else if(document.sep_form.sep_status.value == "") {
		alert(sepopups_adminscripts.sep_status);
		document.sep_form.sep_status.focus();
		return false;
	}
	else if(document.sep_form.sep_start.value == "") {
		alert(sepopups_adminscripts.sep_start);
		document.sep_form.sep_start.focus();
		return false;
	}
	else if(document.sep_form.sep_end.value == "") {
		alert(sepopups_adminscripts.sep_end);
		document.sep_form.sep_end.focus();
		return false;
	}
}

function _sepopups_delete(id) {
	if(confirm(sepopups_adminscripts.sep_delete)) {
		document.frm_sep_display.action="options-general.php?page=simple-exit-popup&ac=del&did="+id;
		document.frm_sep_display.submit();
	}
}	

function _sepopups_redirect() {
	window.location = "options-general.php?page=simple-exit-popup";
}

function _sepopups_help() {
	window.open("http://www.gopiplus.com/work/2020/06/20/simple-exit-popup-wordpress-plugin/");
}

function _sepopups_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	document.getElementById('sep_group').value = "";
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(sepopups_adminscripts.sep_numletters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('sep_group_txt').value = newinputtxt;
		return false;  
	}  
}