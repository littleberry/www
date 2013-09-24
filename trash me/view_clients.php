<?php

require_once("common.inc.php");
require_once("Client.class.php");


list($clients) = Client::getClients();
displayPageHeader("Manage Clients");
?>
<button onclick="window.open('add_client.php');" id="Create Client" name="Create Client">Create Client</button>
<button onclick="#" name="add_contact" id="Add Contact">Add Contact</button>
<?php
foreach ($clients as $client) {
	$client_id = Client::getClientId($client->getValueEncoded("client_name"));
	?>

	<dl style="width: 30em;">
	<dd><?php echo $client->getValueEncoded("client_name")?><a href="edit_client.php?client_id=<?php echo $client_id->getValueEncoded("client_id")?>">Edit</a><a href="#">Add</a>
    </dd>
	</dl>
<?php } ?>
