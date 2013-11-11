;
$( function() {
	$( '#add-task-modal' ).dialog({
		autoOpen: false,
		height: 50%,
		width: 50%,
		modal: true
	});
	$( '#add-task-btn').click( function( evt ) {
		$( '#add-task-modal' ).dialog( "open" );
	});
});