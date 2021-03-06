<?php
	require_once("../common/common.inc.php");
	require_once("../classes/Client.class.php");
	require_once("../classes/Contact.class.php");
	require_once("../common/errorMessages.php");
	require_once("../classes/Project.class.php");
	

	/*OVERALL CONTROL
	1. first time user comes in, call the displayClientAndContactsEditForm function.
	2. Set the client and contact objects to the value pulled from the database.
	3. User clicks on a button to submit the form, call the editClientAndContacts function.
	4. If required fields are missing in the form, re-display the form with error messages.
	5. If there are no missing required fields, call Client::updateClient AND Contact:updateContact	*/
			
	if (isset($_POST["action"]) and $_POST["action"] == "edit_client") {
		//error_log("user came in from form, calling editClientAndContacts");
		editClientAndContacts();
	} else {
		//error_log("showing the edit form, this is the first time the user has come iin.");
		displayClientAndContactsEditForm(array(), array(), new Client(array()), new Contact(array()));
	}
	
	/*DISPLAY CLIENT AND CONTACT EDIT WEB FORM (displayClientAndContactEditForm)
	note...I think we can remove the PHP validation to update the style in validateField
	1. This is the form displayed to the user, the first time the user comes in it gets the client_id out of the $_GET variable (please encode!!)
	2. If first time, pull the client and contact objects from the database.
	3. on reocurring pulls, error messages may or may not be there, based on the user's input, object details will come from the $_POST variable.*/
?>

<?php function displayClientAndContactsEditForm($errorMessages, $missingFields, $client, $contact) {
	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	}
	if (isset($_GET["client_id"])) {
		$client=Client::getClient($_GET["client_id"]);
		$contact=Contact::getContacts($_GET["client_id"]);
	} else {
		//error_log("this is not the first time the user displayed this form.");
		//error_log(print_r($contact,true));
		//error_log(gettype($contact));
	}
	
	include('header.php'); //add header.php to page
	
?>

<section id="page-content" class="page-content">
	<header class="page-header">
		<h1 class="page-title">Edit Client Details</h1>
		<nav class="page-controls-nav">
			<ul class="page-controls-list client">
				<li class="page-controls-item link-btn"><a class="add-client-link" href="client-add.php">+ Add Client</a></li>
				<li class="page-controls-item"><a class="view-client-archive-link" href="client-archives.php">View Archives</a></li>
				<li class="page-controls-item"><a class="view-all-link" href="clients.php">View All</a></li>
			</ul>
		</nav>
	</header>

	<section class="content">
		<!--BEGIN FORM-->
		<form action="client-edit.php" method="post" style="margin-bottom:50px;" enctype="multipart/form-data">
			<input type="hidden" name="action" value="edit_client">
			<?PHP
			//we need to get the client_id into the $_POST so it is there when the user posts the form.
			if (isset($_GET["client_id"])) {
				$client_id = $_GET["client_id"];
			?>
			<input type="hidden" name="client_id" value="<?php echo $client_id?>">
		<?php } 
		if (isset($_POST["client_id"])) {
			$client_id = $_POST["client_id"];
		?>
			<input type="hidden" name="client_id" value="<?php echo $client_id?>">
		<?php } ?>
		<figure class="client-logo l-col-20">
			<img class="client-logo-img small" src="<?php echo "images/" . $client->getValue("client_logo_link")?>" title="Client/Company name logo" alt="Client/Company name logo" />
			<fieldset class="client-logo-upload">
				<legend class="client-logo-title">Upload Client Logo</legend>
				<header class="client-logo-header">
					<h1 class="client-logo-title">Upload Client Logo</h1>
				</header>
				<?//hack the image by using a hidden field.?>
				<input type="hidden" name="client-logo-file" value="<?php echo $client->getValue("client_logo_link")?>">
				<input id="client-logo-file" name="client-logo-file" class="client-logo-file" type="file" value="browse" tabindex="21" />
				<input id="client-logo-upload-btn" name="client-logo-upload-btn" class="client-logo-upload-btn" type="button" value="Upload" tabindex="22" /> or <a class="" href="#">Cancel</a>
			</fieldset>
		</figure>
		<section class="client-detail l-col-80">
			<fieldset class="client-details-entry">
				<!-- <legend class="client-details-title">Edit client details:</legend> -->
				<header class="entity-details-header client">
					<h1 class="entity-details-title client">Edit client details:</h1>
					<h4 class="required">= Required</h4>
				</header>
				<ul class="entity-list entity-details-list client-details-list">
					<li class="entity-details-item name client">
						<label for="client-name" <?php validateField("client_name", $missingFields)?> class="entity-details-label client required">Client's name:</label>
						<input id="client-name" name="client-name" class="client-contact-info-input" type="text" tabindex="1" value="<?php echo $client->getValueEncoded("client_name")?>" />
					</li>
					<li class="entity-details-item phoneNum client">
						<label for="client-phone" <?php validateField("client_phone", $missingFields)?> class="entity-details-label client">Phone number:</label>
						<input id="client-phone" name="client-phone" class="client-contact-info-input" type="text" tabindex="2" value="<?php echo $client->getValueEncoded("client_phone")?>" />
					</li>
					<li class="entity-details-item email client">
						<label for="client-email" <?php validateField("client_email", $missingFields)?> class="entity-details-label client">Email address:</label>
						<input id="client-email" name="client-email" class="client-contact-info-input" type="text" tabindex="3" value="<?php echo $client->getValueEncoded("client_email")?>" />
					</li>
					<li class="entity-details-item fax client">
						<label for="client-fax" <?php validateField("client_fax", $missingFields)?> class="entity-details-label client">Fax number:</label>
						<input id="client-fax" name="client-fax" class="client-contact-info-input" type="text" tabindex="4" value="<?php echo $client->getValueEncoded("client_fax")?>" />
					</li>
					<li class="entity-details-item info-sync client">
						<label for="contact-info-sync" class="entity-details-label client">Use same info for contact:</label>
						<input id="contact-info-sync" name="contact-info-sync" class="contact-info-sync-input" type="checkbox" tabindex="4" value="info-sync" />
					</li>
					<li class="entity-details-item address">
						<label for="client-streetAddress" <?php validateField("client_address", $missingFields)?> class="entity-details-label client">Street Address:</label>
						<textarea id="client-streetAddress" name="client-streetAddress" class="streetAddress-input" tabindex="5"><?php echo $client->getValueEncoded("client_address")?></textarea><br />
						<label for="client-city" <?php validateField("client_city", $missingFields)?> class="entity-details-label client">City</label>
						<input id="client-city" name="client-city" class="client-city-input" type="text" tabindex="6" value="<?php echo $client->getValueEncoded("client_city")?>" /><br />
						<!--handle state differently, as it is a select field.-->
						<label for="client-state" <?php validateField("client_state", $missingFields)?> class="entity-details-label client">State:</label>
						<select id="client-state" name="client-state" class="client-state-select" tabindex="7">
							<option selected="selected" value="default">Select state</option>
							<option value="AL" <?php setSelected($client, "client_state", "AL")?>>Alabama</option>
							<option value="AK" <?php setSelected($client, "client_state", "AK")?>>Alaska</option>
							<option value="AZ" <?php setSelected($client, "client_state", "AZ")?>>Arizona</option>
							<option value="AR" <?php setSelected($client, "client_state", "AR")?>>Arkansas</option>
							<option value="CA" <?php setSelected($client, "client_state", "CA")?>>California</option>
							<option value="CO" <?php setSelected($client, "client_state", "CO")?>>Colorado</option>
							<option value="CT" <?php setSelected($client, "client_state", "CT")?>>Connecticut</option>
							<option value="DE" <?php setSelected($client, "client_state", "DE")?>>Delaware</option>
							<option value="FL" <?php setSelected($client, "client_state", "FL")?>>Florida</option>
							<option value="GA" <?php setSelected($client, "client_state", "GA")?>>Georgia</option>
							<option value="HI" <?php setSelected($client, "client_state", "HI")?>>Hawaii</option>
							<option value="ID" <?php setSelected($client, "client_state", "ID")?>>Idaho</option>
							<option value="IL" <?php setSelected($client, "client_state", "IL")?>>Illinois</option>
							<option value="IN" <?php setSelected($client, "client_state", "IN")?>>Indiana</option>
							<option value="IA" <?php setSelected($client, "client_state", "IA")?>>Iowa</option>
							<option value="KS" <?php setSelected($client, "client_state", "KS")?>>Kansas</option>
							<option value="KY" <?php setSelected($client, "client_state", "KY")?>>Kentucky</option>
							<option value="LA" <?php setSelected($client, "client_state", "LA")?>>Louisiana</option>
							<option value="ME" <?php setSelected($client, "client_state", "ME")?>>Maine</option>
							<option value="MD" <?php setSelected($client, "client_state", "MD")?>>Maryland</option>
							<option value="MA" <?php setSelected($client, "client_state", "MA")?>>Massachusetts</option>
							<option value="MI" <?php setSelected($client, "client_state", "MI")?>>Michigan</option>
							<option value="MN" <?php setSelected($client, "client_state", "MN")?>>Minnesota</option>
							<option value="MS" <?php setSelected($client, "client_state", "MS")?>>Mississippi</option>
							<option value="MO" <?php setSelected($client, "client_state", "MO")?>>Missouri</option>
							<option value="MT" <?php setSelected($client, "client_state", "MT")?>>Montana</option>
							<option value="NE" <?php setSelected($client, "client_state", "NE")?>>Nebraska</option>
							<option value="NV" <?php setSelected($client, "client_state", "NV")?>>Nevada</option>
							<option value="NH" <?php setSelected($client, "client_state", "NH")?>>New Hampshire</option>
							<option value="NJ" <?php setSelected($client, "client_state", "NJ")?>>New Jersey</option>
							<option value="NM" <?php setSelected($client, "client_state", "NM")?>>New Mexico</option>
							<option value="NY" <?php setSelected($client, "client_state", "NY")?>>New York</option>
							<option value="NC" <?php setSelected($client, "client_state", "NC")?>>North Carolina</option>
							<option value="ND" <?php setSelected($client, "client_state", "ND")?>>North Dakota</option>
							<option value="OH" <?php setSelected($client, "client_state", "OH")?>>Ohio</option>
							<option value="OK" <?php setSelected($client, "client_state", "OK")?>>Oklahoma</option>
							<option value="OR" <?php setSelected($client, "client_state", "OR")?>>Oregon</option>
							<option value="PA" <?php setSelected($client, "client_state", "PA")?>>Pennsylvania</option>
							<option value="RI" <?php setSelected($client, "client_state", "RI")?>>Rhode Island</option>
							<option value="SC" <?php setSelected($client, "client_state", "SC")?>>South Carolina</option>
							<option value="SD" <?php setSelected($client, "client_state", "SD")?>>South Dakota</option>
							<option value="TN" <?php setSelected($client, "client_state", "TN")?>>Tennessee</option>
							<option value="TX" <?php setSelected($client, "client_state", "TX")?>>Texas</option>
							<option value="UT" <?php setSelected($client, "client_state", "UT")?>>Utah</option>
							<option value="VT" <?php setSelected($client, "client_state", "VT")?>>Vermont</option>
							<option value="VA" <?php setSelected($client, "client_state", "VA")?>>Virginia</option>
							<option value="WA" <?php setSelected($client, "client_state", "WA")?>>Washington</option>
							<option value="WV" <?php setSelected($client, "client_state", "WV")?>>West Virginia</option>
							<option value="WI" <?php setSelected($client, "client_state", "WI")?>>Wisconsin</option>
							<option value="WY" <?php setSelected($client, "client_state", "WY")?>>Wyoming</option>
							<option value="DC" <?php setSelected($client, "client_state", "DC")?>>Washington DC</option>
							<option value="PR" <?php setSelected($client, "client_state", "PR")?>>Puerto Rico</option>
							<option value="VI" <?php setSelected($client, "client_state", "VI")?>>U.S. Virgin Islands</option>
							<option value="AS" <?php setSelected($client, "client_state", "AS")?>>American Samoa</option>
							<option value="GU" <?php setSelected($client, "client_state", "GU")?>>Guam</option>
							<option value="MP" <?php setSelected($client, "client_state", "MP")?>>Northern Mariana Islands</option>
						</select><br />

						<label for="client-zip" <?php validateField("client_zip", $missingFields)?> class="entity-details-label client">Zip code:</label>
						<input id="client-zip" name="client-zip" class="client-zip-input" type="text" tabindex="8" value="<?php echo $client->getValueEncoded("client_zip")?>" /><br />
						<label for="client-country" class="entity-details-label client">Client's country:</label>
						<select id="client-country" name="client-country" class="client-country-select" tabindex="9">
							<option value="">Select client's country...</option>
							<option selected="selected" value="US">United States of America</option>
						</select>
					</li>
					<?php 
						//get the currencies from the table to populate the drop down.
						$currency = Client::getCurrency();
					?>
					<li class="entity-details-item currency client">
						<label for="client-currency" class="entity-details-label client">Preferred currency:</label>
						<select name="client_currency_index" id="client_currency_index" size="1">
							<option value="">Select currency</option>   
							<?php foreach ($currency as $currencies) { ?>
	   							<option value="<?php echo $currencies["client_currency_index"] ?>"<?php setSelected($client, "client_currency_index", $currencies["client_currency_index"]) ?>><?php echo $currencies["client_preferred_currency"]?></option>
	    					<?php } ?>
						</select>
					</li>
					<!--leave these alone for now. Keep this UI as a single form, but these values have no errors associated with them
					so they can be straight updates.-->
					<li class="entity-details-item client-archive client">
						<label for="client-archive" class="entity-details-label client">Archive client?</label>
						<input id="client-archive" name="client-archive" class="client-archive-input" type="checkbox" tabindex="10" value="1" />
					</li>
					<li class="entity-details-item delete-btn client">
						<label for="client-delete-btn" class="entity-details-label client">Delete Client?</label>
						<input id="client-delete-btn" name="client-delete-btn" class="client-delete-btn" onclick="window.open('delete.php?client_id=<?php echo $client_id ?>','myWindow','width=200,height=200,left=250%,right=250%,scrollbars=no')" type="button" value="- Delete Client" tabindex="11" />
					</li>
					<li class="entity-details-item submit-btn client">
						<label for="client-save-btn" class="entity-details-label client">All done?</label>
						<input id="client-save-btn" name="client-save-btn" class="client-save-btn" type="submit" value="+ Save Changes" tabindex="11" /> or
						<a class="" href="#" tabindex="11"><a href="clients.php">Cancel</a>
					</li>
				</ul>
			</fieldset>
		</section>
		<input type="hidden" name="action" value="edit_client">
		<section id="contact-detail" class="contact-detail">
			<header class="details-header contact-details-header">
				<h1 class="client-details-title">Contacts</h1>
			</header>
				<!--there are multiple contacts. loop through them.--->
				<!--this is SO going to break the UI!-->
				<!--need to figure out how to get these things into an array.-->
				<!--these values ARE part of the post, but there could be many of them.-->
				<!--we need to call them into an array of objects, not just an object.-->
				<!--the object does not hold any values when it is sent back.-->
				<?php
				//error_log("Setting up the input fields in the HTML");
				//error_log(print_r($contact,true));
				error_log("the post variable is:");
				error_log(print_r($_POST,true));
				//error_log(gettype($contact));
				if (!isset($contact)) {
				$contact = new Contact(array());
				}
				$i = 0;
				//there aren't any contacts here. Technically, you shouldn't be able to do this but better safe than sorry.
				if(!count($contact)) {
					$contact = new Contact(Array());
				}	
				foreach ($contact as $contacts) {
					?>
					
			<fieldset id="contact-details" class="contact-details-entry">
				<!-- <legend class="contact-details-title">Edit contact details:</legend> -->
				<header class="contact-details-header">
					<h1 class="contact-details-title">Edit contact details:</h1>
					<h4 class="required">= Required</h4>
				</header>
				<!-- <h4 class="required">= Required</h4> -->
				<ul class="details-list client-details-list">
						<li class="client-details-item name">
						<label for="contact-name" class="contact-details-label required">Your contact's name:</label>
						<input id="contact-name" name="contact-name[]" class="contact-info-input" type="text" value="<?php echo $contacts->getValueEncoded("contact_name")?>" /><br />
						<label for="contact-primary" class="contact-details-label">This the primary contact: </label>
						<?php /*
						<select id="contact-primary" name="contact-primary[]" class="contact-info-input">
							<?php if ($contacts->getValueEncoded("contact_primary") == 1) {
								?><option value="1"	selected="selected">Yes</option>
									<option value="0">No</option>
							<?php } else {	
								?>
								<option value="1"> Yes</option>
									<option value="0" selected="selected">No</option><?php } ?>
						
						</select>
						*/
						//whether or not the radio button is checked comes from the database, it should not be hard-coded to be default on.
						?>
						<input id="contact-primary" name="contact-primary[<?php echo $i?>]" class="contact-info-input" type="checkbox" <?php setChecked($contacts, "contact_primary", "1") ?> />

					</li>
					<li class="client-details-item phoneNum">
						<label for="contact-officePhone" class="contact-details-label">Office phone:</label>
						<input id="contact-officePhone" name="contact-officePhone[]" class="contact-info-input" type="text" value="<?php echo $contacts->getValueEncoded("contact_office_number")?>" /><br />
						<label for="contact-mobilePhone" class="contact-details-label">Mobile phone:</label>
						<input id="contact-mobilePhone" name="contact-mobilePhone[]" class="contact-info-input" type="text" value="<?php echo $contacts->getValueEncoded("contact_mobile_number")?>" />
					</li>
					<li class="client-details-item email">
						<label for="contact-email" class="contact-details-label">Email:</label>
						<input id="contact-email" name="contact-email[]" class="contact-info-input" type="text" value="<?php echo $contacts->getValueEncoded("contact_email")?>" />
					</li>
					<li class="client-details-item fax">
						<label for="contact-fax" class="contact-details-label">Fax:</label>
						<input id="contact-fax" name="contact-fax[]" class="contact-info-input" type="text" value="<?php echo $contacts->getValueEncoded("contact_fax_number")?>" />
					</li>
					<li class="client-details-item cancel-additional">
						<label for="cancel-contact-link" class="contact-details-label">Need to remove contact?</label>
						<a id="cancel-contact-link" class="cancel-action-link" href="#" tabindex="19">Remove contact</a>
					</li>
				</ul>
			</fieldset>
			<?php 
			$i++;
			//echo $i;
			//$counter = $i;
			} 
			//error_log("here is the counter variable: " . $counter++ );
			?>
			<fieldset id="contact-save" class="contact-details-entry">
				<ul class="details-list contact-details-submit">
					<li class="client-details-item add-additional">
						<label for="add-additional-link" class="contact-details-label">Need to add more contacts?</label>
						<a id="add-additional-link" href="#" class="">Add another contact</a>
					</li>
					<li class="client-details-item submit-contact">
						<label for="contact-save-btn" class="contact-details-label">All done?</label>
						<input id="contact-save-btn" name="contact-save-btn" class="contact-save-btn" type="submit" value="+ Save Contact" tabindex="11" /> or
						<a class="" href="#" tabindex="11">Cancel</a>
					</li>
				</ul>
			</fieldset>
		</section>
		<section class="client-projects">

			<header class="details-header client-projects-header">
				<h1 class="client-details-title">Projects</h1>
			</header>
			<h1 class="client-projects-title active">Active Projects</h1>
			<ul class="details-list client-projects-list active">
			<?php 
				//we'll use an existing function to work this magic. Get all the clients with
				//projects and then display them (active and archived) by name for a particular client.
				$clientProjects = Project::getClientsProjectsByStatus(1);
				foreach($clientProjects as $clientProject) {
					if ($client->getValueEncoded("client_id") == $clientProject->getValueEncoded("client_id")) {
						?> <li class="client-projects-list-item"><?php echo $clientProject->getValueEncoded("project_name") ?></li> <?php
					}
				}			
			?>
			</ul>
			<h1 class="client-projects-title archive">Archived Projects</h1>
			<ul class="details-list client-projects-list archive">
<?php 
				//we'll use an existing function to work this magic. Get all the clients with
				//projects and then display them (active and archived) by name for a particular client.
				$clientProjects = Project::getClientsProjectsByStatus(0);
				foreach($clientProjects as $clientProject) {
					if ($client_id == $clientProject->getValueEncoded("client_id")) {
						?> <li class="client-projects-list-item"><?php echo $clientProject->getValueEncoded("project_name") ?></li> <?php
					}
				}			
			?>			</ul>
		</section>
	</section>
</section>
<!--END FORM-->
</form><?php } ?>
		
		
<?php /*CLIENT AND CONTACT PROCESSING FUNCTIONS (editClientAndContacts();)
	1. Set up the required fields in each of the forms.
	2. Create the objects based on the values that were submitted the last time the user submitted the form.
	3. Set up the required fields in the $requiredFields array.
	4. Compare the existence of the fields in the objects (based on the $_POST values) with the fields in the $requiredFields array. If
	any are missing, put the fields into the $missingFields[] array.
	5. If the $missingFields array exists, loop through them and call the error message. If there are NO missing fields, still call the error message for the NON missing field errors (email, phone, etc).
	6. If there are error messages, call displayClientAndContactsEditForm with the error messages, the missing fields, and all the data for the object and the whole thing starts over again.
	7. If there are no errors, update the database with the new client and contact information.
	8. If all went well, display the client details page.
	*/ ?>
<?php function editClientAndContacts() {
	$requiredFields = array("client_name","contact_name");
	$missingFields = array();
	$errorMessages = array();
	
		//this is for the photo upload, and it is in the wrong place.
		//this is also really hacky. We use a hidden field to get the value back into the post variable
		//and then spit it back into the database. EW!!
	if (isset($_FILES["client-logo-file"]) and $_FILES["client-logo-file"]["error"] == UPLOAD_ERR_OK) {
		if ( $_FILES["client-logo-file"]["type"] != "image/jpeg") {
			
			//I'm hardcoding the client_currency_index, because it's in the wrong place. This should be with the rest of the validation.
			$errorMessages[] = "<li>" . getErrorMessage("1","client_logo_link", "invalid_file") . "</li>";
		} elseif ( !move_uploaded_file($_FILES["client-logo-file"]["tmp_name"], "images/" . basename($_FILES["client-logo-file"]["name"]))) {
			$errorMessages[] = "<li>" . getErrorMessage("1","client_logo_link", "upload_problem") . "</li>";
		} else {
			$_POST["client_logo_link"] = $_FILES["client-logo-file"]["name"];
		}
	} else {
		$_POST["client_logo_link"] = (basename($_POST["client-logo-file"]));
	}

	
	//CREATE THE CLIENT OBJECT ($CLIENT)
	$client = new Client( array(
		//CHECK REG SUBS!!
		"client_id" => isset($_POST["client_id"]) ? preg_replace("/[^ 0-9]/", "", $_POST["client_id"]) : "",
		"client_logo_link" => isset($_POST["client_logo_link"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^.]/", "", $_POST["client_logo_link"]) : $_FILES["client_logo_link"],
		"client_name" => isset($_POST["client-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client-name"]) : "",
		"client_phone" => isset($_POST["client-phone"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-phone"]) : "",
		"client_email" => isset($_POST["client-email"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["client-email"]) : "",
		"client_address" => isset($_POST["client-streetAddress"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client-streetAddress"]) : "",
		"client_state" => isset($_POST["client-state"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client-state"]) : "",
		"client_zip" => isset($_POST["client-zip"])? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-zip"]) : "",
		"client_city" => isset($_POST["client-city"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client-city"]) : "",
		"client_currency_index" => isset($_POST["client_currency_index"])? preg_replace("/[^0-9]/", "", $_POST["client_currency_index"]) : "",
		"client_fax" => isset($_POST["client-fax"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-fax"]) : "",
		"client_archived" => isset($_POST["client-archive"]) ? preg_replace("/[^ \-\_a-zA-Z^0-9]/", "", $_POST["client-archive"]) : "",
	));
	
	error_log("here are the values in the POST array");
	error_log(print_r($_POST,true));
	error_log("Here are the values in the client array.");
	error_log(print_r($client,true));	
	
	//CREATE THE CONTACT OBJECTS ($CONTACT)
	//$client_id = $client->getValue("client_id");
	//error_log("The client id is " . $client_id);
	//$numContacts = Contact::getNumberOfContacts($client_id);
	//error_log("The number of contacts in the database is " . $numContacts);
	//$contact_count = $_POST["contact_count"];
	//error_log("the contact count is " . $contact_count);
	//print_r($_POST);
	
	
	//I'M PUTTING THIS FUNCTION RIGHT HERE FOR NOW BECAUSE I'M TIRED!!
	function setCheckbox($checkboxVals, $i) {
		foreach($checkboxVals as $key=>$value) {
			if ($i == $key) {
					return "1";
			} else {
					return "0";
			}
		}
	}
	
	
	$holderArray[] = new Contact( array(
		//CHECK REG SUBS!!
		"contact_name" => isset($_POST["contact-name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact-name"]) : "",
		"contact_primary" => isset($_POST["contact-primary"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact-primary"]) : "",
		"contact_office_number" => isset($_POST["contact-officePhone"]) ? preg_replace("/[^0-9]/", "", $_POST["contact-officePhone"]) : "",
		"contact_mobile_number" => isset($_POST["contact-mobilePhone"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["contact-mobilePhone"]) : "",
		"contact_email" => isset($_POST["contact-email"]) ? preg_replace("/[^ \-\_a-zA-Z0-9^@^.]/", "", $_POST["contact-email"]) : "",
		"contact_fax_number" => isset($_POST["contact-fax"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["contact-fax"]) : "",
	));	
	
	//error_log("CONTACT PRIMARY");
	//error_log(print_r($_POST,true));
	//exit;
		
	
	//get out the number of contacts to use to control the loop. Use contact_name, which is the required field.
	$numContacts = count($holderArray[0]->getValue("contact_name"));
	//error_log("HERE IS THE ITEM COUNT ");
	//error_log($numContacts);
	//print_r($_POST);
	//print_r($holderArray);
	
	//X contacts, create the array.
	for ($i=0; $i<$numContacts; $i++) {
	
		//error_log("creating the objects");
		//5.4 $data['data'] = $results->result()[0];
		//5.3 $data['data'] = $results->result();
		//$data['data'] = $data['data'][0];
		$contact[] = new Contact( array(
			"contact_name" => $holderArray[0]->getValue("contact_name")[$i],
			"contact_primary" => setCheckbox($_POST["contact-primary"], $i),
			"contact_office_number" => $holderArray[0]->getValue("contact_office_number")[$i],
			"contact_mobile_number" => $holderArray[0]->getValue("contact_mobile_number")[$i],
			"contact_email" => $holderArray[0]->getValue("contact_email")[$i],
			"contact_fax_number" => $holderArray[0]->getValue("contact_fax_number")[$i],	
		));
		
		//print_r("Here is the contact array:");
		//print_r(print_r($contact,true));
		//exit;
	}	
	
	
	
//error messages and validation script.
//these errors may happen in the client OR the contact object, so we have to
//call each separately.
	foreach($requiredFields as $requiredField) {
		if (preg_match("/client/", $requiredField)) {
			if ( !$client->getValue($requiredField)) {
				$missingFields[] = $requiredField;
			}
		} elseif (preg_match("/contact/", $requiredField)) {
			foreach ($contact as $contacts) {
				if (!$contacts->getValue($requiredField)) {
					$missingFields[] = $requiredField;
				}
			}
		}			
	}
	
	
	if ($missingFields) {
		$i = 0;
		$errorType = "required";
		foreach ($missingFields as $missingField) {
			$errorMessages[] = "<li>" . getErrorMessage(1,$missingField, $errorType) . "</li>";
			$i++;
		}
	} else {
		//TAKE THIS OUT. We can do this validation in javascript or some other way. 
		/*$clientEmail = $client->getValue("client_email");
		$clientPhone = $client->getValue("client_phone");
		$clientZip = $client->getValue("client_zip");
		$contactEmail = $contact->getValue("contact_email");
		$contactOfficePhone = $contact->getValue("contact_office_number");
		$contactMobilePhone = $contact->getValue("contact_mobile_number");
		$contactFax = $contact->getValue("contact_fax_number");
		
		
		// validate the email addresses
		if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $clientEmail)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"client_email", "invalid_input") . "</li>";
		}
		if(!preg_match("/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $contactEmail)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_email", "invalid_input") . "</li>";
		}
		
		// validate the phone numbers
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $clientPhone)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"client_phone", "invalid_input") . "</li>";
		}
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contactOfficePhone)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_office_number", "invalid_input") . "</li>";
		}
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contactMobilePhone)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_mobile_number", "invalid_input") . "</li>";
		}
		if(!preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $contactFax)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"contact_fax_number", "invalid_input") . "</li>";
		}
		
		//validate the zip code
		if (!preg_match ("/^[0-9]{5}$/", $clientZip)) {
			$errorMessages[] = "<li>" . getErrorMessage(1,"client_zip", "invalid_input") . "</li>";
		}	*/
	}
		
	if ($errorMessages) {
		error_log("There were errors in the input (errorMessages was not blank. Redisplaying the edit form with missing fields.");
		//error_log($contact[0]);
		//error_log(gettype($contact));
		displayClientAndContactsEditForm($errorMessages, $missingFields, $client, $contact);
	} else {
		error_log("All of the required fields are there...Updating database...");
		$client_id = $client->getValue("client_id");
		$client->updateClient($client_id);
		//delete all the records for this client.
		$contact_ids = Contact::getContactIds($client_id);
		foreach ($contact_ids as $contact_id) {
				error_log("Now deleting contact id " . $contact_id[0]);
				Contact::deleteContactByContactId($contact_id[0]);
		}
		//insert all the records for this client.
		error_log("Now inserting these contacts:");
		error_log(print_r($contact,true));
		//error_log($contact_id);
		foreach ($contact as $contacts) {
			$contact_id = $contacts->getValue("contact_id");
			$contacts->insertContact($client_id);
		}
		
		displayClientAndContactsEditForm($errorMessages, $missingFields, $client, $contact);
	}
}

?>

<footer id="site-footer" class="site-footer">

</footer>
<script src="client-controls.js" type="text/javascript"></script>
</body>
</html>