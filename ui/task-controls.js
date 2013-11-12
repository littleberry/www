;
var yesNoToggle = ["No", "Yes"];
var updateRowCB = function( elem ) {
	console.log(elem);
	var $new = $( elem ).find( '.new' );
	$new.children('td').css({ 
		"background-color": '#A9BFF5'
	})
	.animate({
		"background-color": '#FAF9FF'
	}, 1000)
	$new.removeClass( 'new' );
}

function saveData( dataObj ) {
	var sendData = {};
	for (item in dataObj) {
		sendData[item] = dataObj[item];
	}
	sendData["func"] = "processTask";
	console.log(sendData);
	$.post( "tasks.php", sendData )
		.done( function( getData ) {
			console.log("done");
		})
		.fail( function( getData ) {
			console.log("fail");
		})
		.success( function( getData ) {
			var taskId = $.parseJSON( getData )["task_id"];
			var $row = $( '<tr>' )
				.addClass( 'new' )
				.append( '<td data-task_id="' + taskId + '"><a class="client-info-contact-link" href="#" title="Edit task details">Edit</a>')
				.append( '<td data-task_name="' + dataObj["task_name"] + '">' + dataObj["task_name"] + '</td>' )
				.append( '<td data-task_hourly_rate="' + dataObj["task_hourly_rate"] + '">$' + Number(dataObj["task_hourly_rate"]).toFixed(2) + '</td>' )
				.append( '<td data-task_bill_by_default="' + dataObj["task_bill_by_default"] + '">' + yesNoToggle[dataObj["task_bill_by_default"]] + '</td>' )
				.append( '<td data-task_common="' + dataObj["task_common"] + '">' + yesNoToggle[dataObj["task_common"]] + '</td>' );
			var resort = true;
			$( '#tasks-list' )
				.find( 'tbody' )
				.append( $row )
				.trigger( 'addRows', [ $row, resort, updateRowCB ]);
			//console.log("task saved" +  );
		});
}

$( function() {
	$( '#add-task-modal' ).dialog({
		autoOpen: false,
		resizable: false,
		height: 340,
		width: 775,
		modal: true,
		buttons: {
			"+ Save Task": function() {
				var task = {
					task_id: "",
					task_name: $( '#task-name' ).val(),
					task_hourly_rate: $( '#task-hourly-rate' ).val(),
					task_bill_by_default: $( '#task-billable' ).prop( 'checked' ) ? $( '#task-billable' ).val() : 0,
					task_common: $( '#task-common' ).prop( 'checked' ) ? $( '#task-common' ).val() : 0,
					proc_type: 'A' //$( '#proc-type' ).val()
				}
				saveData( task );
				$( this ).dialog( "close" );
			},
			Cancel: function() {
				$( this ).dialog( "close" );	
			}
		},
		close: function() {
	        console.log("close modal");
	    }
	});
	
	$( '#add-task-btn')
		.click( function( evt ) {
			$( '#add-task-modal' ).dialog( "open" );
			evt.preventDefault();
		});
	
});

$( function() { //table display/sorter with filter/search for projects.php
	$( "#tasks-list" ).tablesorter({
		sortList: [[3,1], [1,0]],
		widgets: ["filter"],
		widgetOptions : {
			
			// If there are child rows in the table (rows with class name from "cssChildRow" option)
			// and this option is true and a match is found anywhere in the child row, then it will make that row
			// visible; default is false
			filter_childRows : false,
			
			// if true, a filter will be added to the top of each table column;
			// disabled by using -> headers: { 1: { filter: false } } OR add class="filter-false"
			// if you set this to false, make sure you perform a search using the second method below
			filter_columnFilters : true,
			
			// extra css class applied to the table row containing the filters & the inputs within that row
			filter_cssFilter : '',
			
			// class added to filtered rows (rows that are not showing); needed by pager plugin
			filter_filteredRow   : 'filtered',
			
			// add custom filter elements to the filter row
			// see the filter formatter demos for more specifics
			filter_formatter : null,
			
			// add custom filter functions using this option
			// see the filter widget custom demo for more specifics on how to use this option
			filter_functions : null,
			
			// if true, filters are collapsed initially, but can be revealed by hovering over the grey bar immediately
			// below the header row. Additionally, tabbing through the document will open the filter row when an input gets focus
			filter_hideFilters : false,
			
			// Set this option to false to make the searches case sensitive
			filter_ignoreCase : true,
			
			// if true, search column content while the user types (with a delay)
			filter_liveSearch : true,
			
			// jQuery selector string of an element used to reset the filters
			filter_reset : 'button.reset',
			
			// Delay in milliseconds before the filter widget starts searching; This option prevents searching for
			// every character while typing and should make searching large tables faster.
			filter_searchDelay : 300,
			
			// if true, server-side filtering should be performed because client-side filtering will be disabled, but
			// the ui and events will still be used.
			filter_serversideFiltering: false,
			
			// Set this option to true to use the filter to find text from the start of the column
			// So typing in "a" will find "albert" but not "frank", both have a's; default is false
			filter_startsWith : false,
			
			// Filter using parsed content for ALL columns
			// be careful on using this on date columns as the date is parsed and stored as time in seconds
			filter_useParsedData : false
			
		}
	});
});