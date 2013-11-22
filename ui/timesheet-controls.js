;
var TimesheetItem = function( timesheetItemId, projectId, taskId, personId, timesheetDate, timesheetHours, timesheetNotes ) {
	var tsItem = {
		timesheet_item_id: timesheetItemId,
		project_id: projectId,
		task_id: taskId,
		person_id: personId,
		timesheet_date: timesheetDate,
		timesheet_hours: timesheetHours,
		timesheet_notes: timesheetNotes
	}
	
	return tsItem;
}

function getTimesheet( id, week ) {
	var timesheet = {};
	var startDate = week.start.getFullYear() + "-" + (week.start.getMonth() + 1) + "-" + week.start.getDate();
	var endDate = week.end.getFullYear() + "-" + (week.end.getMonth() + 1) + "-" + week.end.getDate();
	
	var getData = {
		func: "returnTimesheetJSON",
		id: id,
		collection: "person",
		startDate: startDate,
		endDate: endDate
	}
	$.get( "returnJSON.php", getData )
		.done( function( data ) {
			//console.log( data );
			timesheet = $.parseJSON( data );
			$( "#timesheet-tasks-list" ).data( "timesheet_id", timesheet[0].timesheet_id );
			var tsItems = timesheet[0].timesheet_items;
			
			if ( timesheet[0].timesheet_items.length > 0 ) {
				console.log("we have items to display");
				
				for ( var i = 0; i < tsItems.length; i+=7 ) {
					var rowInfo = {
						timesheet_item_id: tsItems[i].timesheet_item_id,
						task_name: tsItems[i].task_name,
						task_id: tsItems[i].task_id,
						project_name: tsItems[i].project_name,
						project_id: tsItems[i].project_id,
						person_id: tsItems[i].person_id
					}
					var days = [];
					for ( var d = i; d < i + 7; d++ ) {
						days[d % 7] = {
							timesheet_date: tsItems[d].timesheet_date,
							timesheet_hours: tsItems[d].timesheet_hours,
							timesheet_notes: tsItems[d].timesheet_notes
						}
					}
					rowInfo.timesheet_days = days;
					addTimesheetRow( rowInfo );
					
				}

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
	var personId = $( "#timesheet-tasks-list" ).data( "person_id" );
	var dates = [];
	var thisWeek = getWeekBookends( ); //adjust later for saving weeks other than current
	for ( var d = 0; d < 7; d++ ) {
		dates[d] = new Date();
		dates[d].setDate( thisWeek.start.getDate() + d );
	}
	//console.log(dates);
	var tsItems = [];
	
	$tsTable.find( 'input' )
		.each( function( index, elem ) {
			var taskData = $( elem ).attr( "name" ).split( "_" );
			var projId = taskData[0].substring(1);
			var taskId = taskData[1].substring(1);
			var day = taskData[2].substring(1); //may need to force conversion to number
			var itemDate = dates[taskData[2].substring(1)].getFullYear() + "-" + (dates[taskData[2].substring(1)].getMonth() + 1) + "-" + dates[taskData[2].substring(1)].getDate();
			tsItems.push( new TimesheetItem(
				tsId,
				taskData[0].substring(1),
				taskData[1].substring(1),
				personId,
				itemDate,
				Number( $( elem ).val() ),
				""
			)); //need to do something about timesheet notes. Where are they saved? sending empty string for now.
		})
	
	$.post( "timesheet.php", {
			func: "saveTimesheet",
			proc_type: "A",
			timesheetItems: JSON.stringify( tsItems )
		})
		.done( function( data ) {
			console.log("done: " + data);
		})
		.fail( function( data ) {
			console.log("fail: " + data);
		});
	//console.log(tsItems);
	
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
	//console.log(row.timesheet_days.length);
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
							.val( function() {
								if ( row.timesheet_days ) {
									return row.timesheet_days[i].timesheet_hours;
								}
							})
							.change( function( evt ) {
								calculateTotals( $( this ) );
							})
							.blur( function( evt ) {
								$( this ).val( function( elem ) {
									return Number( $( this ).val() ).toFixed(2);
								});
								calculateTotals( $( this ) );
							});
					})
			}
			return weekTDs;
		})
		.append( '<td class="total">' )
		.append( function() {
			return $deleteRow
				.clone( true )
				.button({
					icons: {
						primary: "ui-icon-close"
					},
					text: false
				})
				.click(function( evt ) {
					removeTimesheetRow( $( this ).parents( 'tr' ) );
					
					evt.preventDefault();
				})
		});
		
	$table
		.append( $newRow );
}

function removeTimesheetRow( $row ) {
	console.log("remove row");
	$( $row ).remove();
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
				colTotal += Number( $( elem ).children( 'td' ).eq( elemIndex ).children( 'input' ).val() ).toFixed(2);
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
					total += Number( $( this ).text() ).toFixed(2);
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
		});
		
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
