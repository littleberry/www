;
function getTimesheet( id, week ) {
	var timesheet = {};

	var getData = {
		func: "returnTimesheetJSON",
		id: id,
		collection: "person",
		startDate: week.start,
		endDate: week.end
	}
	$.get( "returnJSON.php", getData )
		.done( function( data ) {
			timesheet = $.parseJSON( data );
			$( "#timesheet-tasks-list" ).data( "timesheet_id", timesheet[0].timesheet_id );
			if ( timesheet[0].timesheet_items ) {
				console.log("we have items to display");
			} else {
				console.log("no items to display yet");
			}
			//console.log("done");
		})
		.fail( function( data ) {
			console.log("fail: " + data);
		});
		
	return timesheet;
}
function saveTimesheet( elem ) {
	var $tsTable = $( elem ).find( 'tbody' );
	var tsId = $( "#timesheet-tasks-list" ).data( "timesheet_id" );
	var tsItems = [];
	
	
	console.log(tsId);
	
}

function getTasksForProject( id ) {
	$.get( "returnJSON.php", {
			func: "returnTasksJSON" ,
			id: id,
			collection: "project"
		})
		.done( function( data ) {
			//console.log("done: " + data);
		})
		.fail( function( data ) {
			console.log("fail: " + data);
		})
		.success( function( data ) {
			var $taskSelect = $( "#task-name" )
				.empty();
			
			data = $.parseJSON( data );
			//console.log(data.length);
			
			for ( var i = 0; i < data.length; i++ ) {
				//console.log(data[i].task_name);
				$taskSelect.append( function() {
					return $( "<option>" )
						.val( data[i].task_id )
						.text( data[i].task_name );
				})
			}
		})
}

function addTimesheetRow( row ) {
	$timeInput = $( '<input type="text" class="time-entry-input" />' );
	$deleteRow = $( '<a href="#" class="ui-button delete">x</a>' );
	$table = $( "#timesheet-tasks-list tbody" );
	$newRow = $( '<tr>' )
		.append( "<td>" + row.project_name + "<br />" + row.task_name + "</td>" )
		.append( function() {
			var weekTDs = [];
			for ( var i = 0; i < 7; i++ ) {
				weekTDs[i] = $( '<td class="day">' )
					.append( function() {
						var inputName = "p" + row.project_id + "_t" + row.task_id + "_d" + i;
						return $timeInput.clone()
							.attr( "name", inputName )
							.change( function( evt ) {
								calculateTotals( $( this ) );
							})
							.blur( function( evt ) {
								calculateTotals( $( this ) );
							});
					})
			}
			return weekTDs;
		})
		.append( '<td class="total">' )
		.append( function() {
			return $deleteRow
				.clone()
				.button({
					icons: {
						primary: "ui-icon-close"
					},
					text: false
				})
		});
		
	$table
		.append( $newRow );
}

function calculateTotals( elem ) {
	if ( elem ) {
		//called when updating a time entry
		var rowTotal = 0;
		$( elem )
			.parents( 'tr' )
			.find( 'input' )
			.each( function( index, inputElem ) {
				rowTotal += Number( $( inputElem ).val() );
			})
			.end()
			.find( '.total' )
			.text( rowTotal );
		
		var colTotal = 0;
		var elemIndex = elem.parents('td').index();
		
		$( elem )
			.parents( 'tbody' )
			.find( 'tr' )
			.each( function( index, elem ) {
				colTotal += Number( $( elem ).children( 'td' ).eq( elemIndex ).children( 'input' ).val() );
			})
			.end()
			.siblings( 'tfoot' )
			.find( 'td' ).eq( elemIndex )
			.text( colTotal );
		
		$( 'td.week-total' )
			.text( function() {
				var total = 0;
				
				$( this ).prevAll( '.total' )
				.each( function() {
					total += Number( $( this ).text() );
				})
				return total;
			});
					
	} else {
		//called first time to tally existing timesheet
		
		
	}
}

function getWeekBookends( date ) {
	var bookends = {};
	var workWeekStart = 1; //Work week starts on Monday
	var workWeekLength = 6; //Last day of the week (0..7);
	var weekStart = new Date();
	var weekEnd = new Date();

	if ( date ) {
		weekStart.setDate( date.getDate() - date.getDay() + workWeekStart );
		weekEnd.setDate( weekStart.getDate() + workWeekLength );
		
	} else {
		var today = new Date();
		
		weekStart.setDate( today.getDate() - today.getDay() + workWeekStart );
		weekEnd.setDate( weekStart.getDate() + workWeekLength );
	
		//console.log ( weekStart + ", " + today + ", " + weekEnd );
	}
	bookends["start"] = weekStart;
	bookends["end"] = weekEnd;

	return bookends;
}

$( function() {
	var thisWeek = getWeekBookends(); //leave blank to get current week
	var timesheetData = getTimesheet( $( "#timesheet-tasks-list" ).data( "person_id" ), thisWeek );

	getTasksForProject( $( "#project-name" ).val() );
	$( "#project-name" )
		.change( function( evt ) {
			getTasksForProject( $( this ).val() );
		})
		
	$( '#add-ts-entry-modal' )
		.dialog({
			autoOpen: false,
			resizable: false,
			height: 360,
			width: 600,
			modal: true,
			buttons: {
				"+ Add Row": function() {
					var rowInfo = {
						task_id: $( "#task-name" ).val(),
						task_name: $( "#task-name option:selected" ).text(),
						project_id: $( "#project-name" ).val(),
						project_name: $( "#project-name option:selected" ).text()
					}
					addTimesheetRow( rowInfo );
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
	
	$( '.new-time-entry' ).click( function ( evt ) {
		
		if ( $( evt.currentTarget ).is( '.add-row' ) ) {
			
			//$( '#add-task-modal' )
				

		} else {
			
				
		}
		$( '#add-ts-entry-modal' ).dialog( 'open' );

		evt.preventDefault();
	});
	
});


$( function() {
	$( ".ui-button" )
		.button()
		.click( function( evt ) {
			evt.preventDefault();
		});
		
	$( ".ui-button.save" )
		.button( "option", "disabled", false )
		.click( function( evt ) {
			saveTimesheet( $( '#timesheet-tasks-list' ) );
			evt.preventDefault();
		} );
		
	$( "#time-display" )
		.buttonset();
		
	$( "#time-period" )
		.buttonset()
		.find( ".previous-date" )
		.button({
			icons: {
			primary: "ui-icon-triangle-1-w"
		},
			text: false
		}).end()
		.find( ".next-date" )
		.button({
			icons: {
			primary: "ui-icon-triangle-1-e"
		},
			text: false
		});
		
			
	/*
$( "#date-picker" )
		.datepicker({
			showOn: "both",
			buttonImage: "libraries/images/calendar-icon.png",
			buttonImageOnly: true
		})
		.button();
*/
		
	/*
$( ".date-picker-btn" )
		.button({
	 		icons: {
	     		primary: "ui-icon-calendar"
	 		},
	 		text: false
		})
		.click( function( evt ) {
			$( "#date-picker" ).datepicker({ showAnim: "slideDown" });
			evt.preventDefault();
		});
*/

});
