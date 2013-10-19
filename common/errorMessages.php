<?php
//this file can be internationalized later, it currently uses client_currency_index (1).
//once it is internationalized, don't overload this field like this, use a language preference instead.
function getErrorMessage($client_currency_index, $errorMessage, $errorType) {
	$errorArray = array();
	
	switch($client_currency_index) {
		case 1:
			//echo $errorMessage;
			//echo $errorType;
			//errorType "missing"
			$errorArray["client_name"]["required"] = "You did not fill out the client name field.";
			$errorArray["client_phone"]["required"] = "You did not fill out the client phone number field.";
			$errorArray["client_email"]["required"] = "You did not fill out the client email field.";
			$errorArray["client_address"]["required"] = "You did not fill out the client address field.";
			$errorArray["client_city"]["required"] = "You did not fill out the client city field.";
			$errorArray["client_zip"]["required"] = "You did not fill out the client zip field.";
			$errorArray["client_logo_link"]["required"] = "You didn't upload a file.";
			$errorArray["contact_name"]["required"] = "Please enter a name for your contact. Note: All clients must have at least one contact.";
			$errorArray["project_name"]["required"] = "Please enter a name for your project.";
			$errorArray["person_first_name"]["required"] = "Please enter a first name for this person.";
			$errorArray["person_last_name"]["required"] = "Please enter a last name for this person.";
			$errorArray["person_email"]["required"] = "Please enter an email address for this person.";
			//errorType "invalid input"
			$errorArray["client_email"]["invalid_input"] = "Please check you entered a valid email address for the client.";
			$errorArray["client_phone"]["invalid_input"] = "Please check you entered a valid phone number for the client in format XXX-XXX-XXXX.";
			$errorArray["client_zip"]["invalid_input"] = "Please check you entered a valid zip code for this client in format XXXXX.";
			//errors for file upload
			$errorArray["client_logo_link"]["invalid_file"] = "You may only upload JPEG images.";
			$errorArray["client_logo_link"]["upload_problem"] = "Something went wrong uploading your image.";
			//contact errors
			$errorArray["contact_email"]["invalid_input"] = "Please check you entered a valid email address for your contact.";
			$errorArray["contact_office_number"]["invalid_input"] = "Please check you entered a valid office phone number for the contact in format XXX-XXX-XXXX.";
			$errorArray["contact_mobile_number"]["invalid_input"] = "Please check you entered a valid mobile phone number for the contact in format XXX-XXX-XXXX.";
			$errorArray["contact_fax_number"]["invalid_input"] = "Please check you entered a valid fax phone number for the contact in format XXX-XXX-XXXX.";
			$errorArray["contact_primary"]["required"] = "All clients must have at least one primary contact.";
			return $errorArray[$errorMessage][$errorType];
			break;
		default:
			return "You entered an invalid language index.";
	}
}
?>