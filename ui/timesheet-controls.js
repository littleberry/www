;
$( function() {
	$( '#add-ts-entry-modal' ).dialog({
		autoOpen: false,
		resizable: false,
		height: 360,
		width: 775,
		modal: true,
		buttons: {
			"+ Add Row": function() {
				
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );	
			}
		},
		close: function() {
	        //console.log("close modal");
	    }
	});
});

$( function() {
	$( "#timesheet-tasks-list" ).tablesorter({
		sortList: [[0,1]]
	});
});