<?php

require_once("common.inc.php");
require_once("Client.class.php");

displayPageHeader("Manage Clients");

//why is this being returned as a multidimensional array?!
//$client = $client[0][0];
print_r($client);

/*$logEntries = LogEntry::getLogEntries($memberId);
displayPageHeader("View member: " . $member->getValueEncoded("firstName") . " " . $member->getValueEncoded("lastName"));
*/
?>

<dl style="width: 30em;">
<dt>Client ID</dt>
<dd><?php //echo $client->getValueEncoded("client_id")?></dd>
<dt> First Name</dt>
<dd><?php //echo $client->getValueEncoded("client_first_name")?></dd>
<dt>Last name</dt>
<dd><?php //echo $client->getValueEncoded("client_last_name")?></dd>
<dt>Address Index</dt>
<dd><?php //echo $client->getValueEncoded("client_address_index")?></dd>
<dt>Currency Index</dt>
<dd><?php //echo $client->getValueEncoded("client_currency_index")?></dd>
</dl>

<a href="add_client.php"> Add client </a>
