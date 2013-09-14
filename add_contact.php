<?php
require_once("/common/common.inc.php");
require_once("/classes/Contact.class.php");

//if the user submitted the form, process it. Otherwise, display the form.
if (isset($_POST["action"]) and $_POST["action"] == "add_contact") {
	processContact();
} else {
	displayContactInsertForm(array(), array(), new Contact(array()));
}


function displayContactInsertForm($errorMessages, $missingFields, $contact) {
	print_r($contact);
	displayPageHeader("Create New Contact");
	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	}
	//the form below displays the fields to the user. Remove the HTML here! 
		
	?>
	 <form action = "add_contact.php" method="post" style="margin-bottom:50px;">
   	 	<div style="width:30em;">
   	 	<input type="hidden" name="action" value="add_contact"/>
  	 	<?php
		//there has to be a better way!
		if (isset ($_GET["client_id"])) {?>
		   <input type="hidden" name="client_id" value="<?php echo $_GET["client_id"]?>"/>
		<?php } else {?>
		  <input type="hidden" name="client_id" value="<?php echo $_POST["client_id"]?>"/>
    	<?php } ?>
		
	   	<label for="contact_first_name"<?php validateField("contact_first_name", $missingFields)?>>Contact First Name *</label>
    	<input type="text" name="contact_first_name" id="contact_first_name" value="<?php echo $contact->getValueEncoded("contact_first_name")?>" /><br/>
    
  		<label for="contact_last_name"<?php validateField("contact_last_name", $missingFields)?>>Contact Last Name *</label>
   		<input type="text" name="contact_last_name" id="contact_last_name" value="<?php echo $contact->getValueEncoded("contact_last_name")?>" /><br/>
        
	  	<label for="contact_title"<?php validateField("contact_title", $missingFields)?>>Contact Title</label>
    	<input type="text" name="contact_title" id="contact_title" value="<?php echo $contact->getValueEncoded("contact_title")?>" /><br/>
    
	   	<label for="contact_email"<?php validateField("contact_email", $missingFields)?>>Contact E-Mail Address *</label>
  		<input type="text" name="contact_email" id="contact_email" value="<?php echo $contact->getValueEncoded("contact_email")?>" /><br/>
    
		<label for="contact_office_number"<?php validateField("contact_office_number", $missingFields)?>>Contact Office Number</label>
   		<input type="text" name="contact_office_number" id="contact_office_number" value="<?php echo $contact->getValueEncoded("contact_office_number")?>" /><br/>
    
		<label for="contact_mobile_number"<?php validateField("contact_mobile_number", $missingFields)?>>Contact Mobile Nummber</label>
   		<input type="text" name="contact_mobile_number" id="contact_mobile_number" value="<?php echo $contact->getValueEncoded("contact_mobile_number")?>" /><br/>
    
	   	<label for="contact_fax_number"<?php validateField("contact_fax_number", $missingFields)?>>Contact Fax Number</label>
   		<input type="text" name="contact_fax_number" id="contact_fax_number" value="<?php echo $contact->getValueEncoded("contact_fax_number")?>" /><br/>
    	
        <div style="clear:both">
   		<input type="submit" name="submitButton" id="submitButton" value="Send Details" />
   		<input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right:20px;"/>
    	</div>
    		</div>
     </form>
     <?php
	 displayPageFooter();
}

//update this for the contact.
function processContact() {
	$requiredFields = array("contact_first_name", "contact_last_name", "contact_email");
	$missingFields = array();
	$errorMessages = array();
	
	//create the object here and pass in the appropriate fields to the constructor. These values are now part of the contact object.
	$contact = new Contact( array(
		"contact_first_name" => isset($_POST["contact_first_name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact_first_name"]) : "",
		"contact_last_name" => isset($_POST["contact_last_name"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact_last_name"]) : "",
		"contact_title" => isset($_POST["contact_title"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact_title"]) : "",
		"contact_email" => isset($_POST["contact_email"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact_email"]) : "",
		"contact_office_number" => isset($_POST["contact_office_number"])? preg_replace("/[^0-9]/", "", $_POST["contact_office_number"]) : "",
		"contact_mobile_number" => isset($_POST["contact_mobile_number"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact_mobile_number"]) : "",
		"contact_fax_number" => isset($_POST["contact_fax_number"])? preg_replace("/[^0-9]/", "", $_POST["contact_fax_number"]) : "",
		"client_id" => isset($_GET["client_id"])? preg_replace("/[^0-9]/", "", $_GET["client_id"]) : ""
	));
	
	
	foreach($requiredFields as $requiredField) {
		if ( !$contact->getValue($requiredField)) {
			$missingFields[] = $requiredField;
		}
	}
	
	if ($missingFields) {
		$errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Send Details to resend the form.</p>';
	}
		
	if ($errorMessages) {
	   	displayContactInsertForm($errorMessages, $missingFields, $contact);
	} else {
		$client_id=$_POST["client_id"];
		$contact->insertContact($client_id);		
		displayClients();
	}
} 


function displayClients() {
	header("Location: manage_clients.php");
	displayPageFooter();
}
?>         
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 