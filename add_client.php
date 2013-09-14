<?php
require_once("/common/common.inc.php");
require_once("/classes/Client.class.php");

//if the user submitted the form, process it. Otherwise, display the form.
if (isset($_POST["action"]) and $_POST["action"] == "add_client") {
	processClient();
} else {
	displayClientInsertForm(array(), array(), new Client(array()));
}


function displayClientInsertForm($errorMessages, $missingFields, $client) {
	//print_r($client);
	displayPageHeader("Create new Client");
	if ($errorMessages) {
		foreach($errorMessages as $errorMessage) {
			echo $errorMessage;
		}
	}
	//the form below displays the fields to the user. Remove the HTML here! The $client object contains these values. Get the values to display in the UI using the
	//getValueEncoded function, which strips out special characters.
		
	?>
	 <form action = "add_client.php" method="post" style="margin-bottom:50px;">
   	 	<div style="width:30em;">
   	 	<input type="hidden" name="action" value="add_client"/>
    
	   	<label for="client_name"<?php validateField("client_name", $missingFields)?>>Client Name *</label>
    	<input type="text" name="client_name" id="client_name" value="<?php echo $client->getValueEncoded("client_name")?>" /><br/>
    
  		<?php if (ADDRESS_CONFIG == 1) { ?>
        	<label for="client_address_number"<?php validateField("client_address_number", $missingFields)?>>Client Address Number</label>
   			<input type="text" name="client_address_number" id="client_address_number" value="<?php echo $client->getValueEncoded("client_address_number")?>" /><br/>
        
	  		<label for="client_street_name"<?php validateField("client_street_name", $missingFields)?>>Client Street Name</label>
    		<input type="text" name="client_street_name" id="client_street_name" value="<?php echo $client->getValueEncoded("client_street_name")?>" /><br/>
    
		   	<label for="client_state"<?php validateField("client_state", $missingFields)?>>Client State</label>
   			<input type="text" name="client_state" id="client_state" value="<?php echo $client->getValueEncoded("client_state")?>" /><br/>
    
			<label for="client_zip"<?php validateField("client_zip", $missingFields)?>>Client Zip</label>
   			<input type="text" name="client_zip" id="client_zip" value="<?php echo $client->getValueEncoded("client_zip")?>" /><br/>
    
		  	<label for="client_apartment"<?php validateField("client_apartment", $missingFields)?>>Client Apartment</label>
   			<input type="text" name="client_apartment" id="client_apartment" value="<?php echo $client->getValueEncoded("client_apartment")?>" /><br/>
    
	   		<label for="client_city"<?php validateField("client_city", $missingFields)?>>Client City</label>
   			<input type="text" name="client_city" id="client_city" value="<?php echo $client->getValueEncoded("client_city")?>" /><br/>
    	<?php } else {?>
        	<label for="client_address"<?php validateField("client_address", $missingFields)?>>Client Address</label>
   			<textarea cols="5" rows="10" name="client_address" id="client_address" value="<?php echo $client->getValueEncoded("client_address")?>" /></textarea>
    	<?php } ?>
        
        <?php 
		//get the currencies out to populate the drop down.
		$currency = Client::getCurrency();
		?>
        
	 	<label for="client_currency_index">Preferred Currency</label>
   		<select name="client_currency_index" id="client_currency_index" size="1">    
		<?php foreach ($currency as $currencies) { ?>
   			<option value="<?php echo $currencies["client_currency_index"] ?>"<?php setSelected($client, "client_currency_index", $currencies["client_currency_index"]) ?>><?php echo $currencies["client_preferred_currency"]?></option>
    	<?php } ?>
    	</select><br/>
        <div style="clear:both">
   		<input type="submit" name="submitButton" id="submitButton" value="Send Details" />
   		<input type="reset" name="resetButton" id="resetButton" value="Reset Form" style="margin-right:20px;"/>
    	</div>
    		</div>
     </form>
     <?php
	 displayPageFooter();
}

function processClient() {
	$requiredFields = array("client_name");
	$missingFields = array();
	$errorMessages = array();
	
	//create the object here and pass in the appropriate fields to the constructor. These values are now part of the client object.
	$client = new Client( array(
		"client_name" => isset($_POST["client_name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_name"]) : "",
		//check this is the correct reg sub for the int
		"client_address" => isset($_POST["client_address"])? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_address"]) : "",
		"client_street_name" => isset($_POST["client_street_name"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_street_name"]) : "",
		"client_state" => isset($_POST["client_state"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_state"]) : "",
		"client_zip" => isset($_POST["client_zip"])? preg_replace("/[^0-9]/", "", $_POST["client_zip"]) : "",
		"client_apartment" => isset($_POST["client_apartment"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_apartment"]) : "",
		"client_city" => isset($_POST["client_city"]) ? preg_replace("/[^ \-\_a-zA-Z0-9]/", "", $_POST["client_city"]) : "",
		"client_currency_index" => isset($_POST["client_currency_index"])? preg_replace("/[^0-9]/", "", $_POST["client_currency_index"]) : "",
	));
	
	
	foreach($requiredFields as $requiredField) {
		if ( !$client->getValue($requiredField)) {
			$missingFields[] = $requiredField;
		}
	}
	
	if ($missingFields) {
		$errorMessages[] = '<p class="error">There were some missing fields in the form you submitted. Please complete the fields highlighted below and click Send Details to resend the form.</p>';
	}
		
	if ($errorMessages) {
		displayClientInsertForm($errorMessages, $missingFields, $client);
	} else {
		$client_name=$_POST["client_name"];
		$client->insertClient($client_name);		
		displayClients();
	}
} 


function displayClients() {
	header("Location: manage_clients.php");
	displayPageFooter();
}
?>         
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 
		 