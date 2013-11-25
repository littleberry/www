;
var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
var monthsNarrow = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

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
	
	$( "#timesheet-tasks-list" )
		.find( 'tbody' )
		.empty()
		.end()
		.find( 'tfoot td.total, tfoot td.week-total' )
		.text( "" );
	
	$.get( "returnJSON.php", getData )
		.done( function( data ) {
			//console.log( data );
			timesheet = $.parseJSON( data );
			$( "#timesheet-tasks-list" ).data( "timesheet_id", timesheet[0].timesheet_id );
			//console.log( "saved timesheet_id: " + $( "#timesheet-tasks-list" ).data( "timesheet_id" ) );
			var tsItems = timesheet[0].timesheet_items;
			
			if ( timesheet[0].timesheet_items.length > 0 ) {
				//console.log("we have items to display");
				
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
		
		
	//calculateTotals();
	return timesheet;
}

function saveTimesheet( elem, deleteRow ) {
	var $tsTable = $( elem ).find( 'tbody' );
	var tsId = $( "#timesheet-tasks-list" ).data( "timesheet_id" );
	console.log("saving to timesheet_id: " + tsId )
	var personId = $( "#timesheet-tasks-list" ).data( "person_id" );
	var dates = [];
	var thisWeek = getWeekBookends( $( "#timesheet-tasks-list" ).data( "timesheet_start" ) ); //adjust later for saving weeks other than current
	for ( var d = 0; d < 7; d++ ) {
		dates[d] = new Date();
		dates[d].setDate( thisWeek.start.getDate() + d );
	}
	//console.log(dates);
	var tsItems = [];
	
	$tsTable.find( 'input' ).not( '.remove' )
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
	console.log(tsItems);
	if ( deleteRow ) {
		var deleteItems = [];
		$( deleteRow ).find( 'input' )
			.each( function( index, elem ) {
				var taskData = $( elem ).attr( "name" ).split( "_" );
				var projId = taskData[0].substring(1);
				var taskId = taskData[1].substring(1);
				var day = taskData[2].substring(1); //may need to force conversion to number
				var itemDate = dates[taskData[2].substring(1)].getFullYear() + "-" + (dates[taskData[2].substring(1)].getMonth() + 1) + "-" + dates[taskData[2].substring(1)].getDate();
					deleteItems.push( new TimesheetItem(
						tsId,
						taskData[0].substring(1),
						taskData[1].substring(1),
						personId,
						itemDate,
						Number( $( elem ).val() ),
						""
					)); //need to do something about timesheet notes. Where are they saved? sending empty string for now.
			})
	} else {
		var deleteItems = 0;
	}
	//console.log(deleteItems);
	
	$.post( "timesheet.php", {
			func: "saveTimesheet",
			proc_type: "A",
			timesheetItems: JSON.stringify( tsItems ),
			deleteItems: JSON.stringify( deleteItems )
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
	rowTotal = 0;
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
									rowTotal += Number( row.timesheet_days[i].timesheet_hours );
									return row.timesheet_days[i].timesheet_hours;
								} else {
									return 0;
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
		.append( '<td class="total">' + rowTotal.toFixed(2) + '</td>' )
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
					$( this ).parents( 'tr' ).find( 'input' ).addClass( 'remove' );
					saveTimesheet( $( '#timesheet-tasks-list' ),  $( this ).parents( 'tr' ) );
					$( this ).parents( 'tr' )
						.remove();
					evt.preventDefault();
				})
		});
		
	$table
		.append( $newRow );
	
	//$newRow.find( 'input' );
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
			.text( rowTotal.toFixed(2) );
		
		var colTotal = 0;
		var elemIndex = elem.parents('td').index();
		
		$( elem )
			.parents( 'tbody' )
			.find( 'tr' )
			.each( function( index, elem ) {
				colTotal += Number( $( elem ).children( 'td' ).eq( elemIndex ).children( 'input' ).val() );//.toFixed(2);
			})
			.end()
			.siblings( 'tfoot' )
			.find( 'td' ).eq( elemIndex )
			.text( colTotal.toFixed(2) );
		
		$( 'td.week-total' )
			.text( function() {
				var total = 0;
				
				$( this ).prevAll( '.total' )
				.each( function() {
					total += Number( $( this ).text() );//.toFixed(2);
				})
				return total.toFixed(2);
			});	
	}
}

function getWeekBookends( date ) {
	var bookends = {};
	var workWeekStart = 1; //Work week starts on Monday
	var workWeekLength = 6; //Last day of the week (0..6);
	var weekStart = new Date();
	var weekEnd = new Date();

	if ( !date ) {
		var date = new Date();
	} else {
		date = new Date( date );
	}
	
	if ( date.getDay() == 1 ) {
		weekStart = date;
	} else if ( date.getDay() == 0 ) {
		weekStart.setDate( date.getDate() - ( date.getDay() + 7 ) + workWeekStart ); //compensating for Monday start of work week.
	} else {
		weekStart = new Date( date.getFullYear(), date.getMonth(), ( date.getDate() - date.getDay() + workWeekStart) );
		//console.log("weekStart is: " + weekStart);
	}
	weekEnd = new Date(weekStart);
	weekEnd.setDate( weekEnd.getDate() + workWeekLength );

	bookends = {
		start: weekStart,
		end: weekEnd
	}
	//console.log ( "bookends: " + bookends.start + " - " + bookends.end );
	
	$( '.page-title' ).text( function() {
		var dateTitle = "";
		dateTitle += months[weekStart.getMonth()] + " " + weekStart.getDate() + " - ";
		if ( weekEnd.getMonth() != weekStart.getMonth() ) {
			dateTitle += months[weekEnd.getMonth()];
		}
		dateTitle += " " + weekEnd.getDate() + ", " + weekEnd.getFullYear();
		return dateTitle;
	});
	
	//add dates to table header
	var today = new Date();
	$( "#timesheet-tasks-list th.day" ).not( '.total' )
		.each( function( index ) {
			$( this ).find( 'span' ).remove();
			var thdate = new Date(bookends.start);
			thdate.setDate( bookends.start.getDate() + index );
			if ( thdate == today ) {
				$( this ).addClass( 'today' );
			}
			
			//console.log("the month is: " + thdate.getMonth());
			$( this ).append( "<span>" + monthsNarrow[thdate.getMonth()] + " " + thdate.getDate() + "</span>" );
		})

	
	$( "#timesheet-tasks-list" ).data( "timesheet_start", bookends.start.toDateString() );
	//console.log("date: " + date + ", " + "weekStart: " + $( "#timesheet-tasks-list" ).data( "timesheet_start"));
	
	return bookends;
}

$( function() {
	console.log("start");
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
		})
		.click( function( evt ) {
			saveTimesheet( $( '#timesheet-tasks-list' ) );
			var date = new Date( $( "#timesheet-tasks-list" ).data( "timesheet_start" ) );
			console.log("date when prev clicked: " + date);
			date.setDate( new Date(date).getDate() - 7 );
			console.log("date after setting for prev week: " + date);
			var prevWeek = getWeekBookends( date );
			var timesheetData = getTimesheet( $( "#timesheet-tasks-list" ).data( "person_id" ), prevWeek );
			evt.preventDefault();
		})
		.end()
		.find( ".next-date" )
		.button({
			icons: {
			primary: "ui-icon-triangle-1-e"
		},
			text: false
		})
		.click( function( evt ) {
			saveTimesheet( $( '#timesheet-tasks-list' ) );
			var date = new Date( $( "#timesheet-tasks-list" ).data( "timesheet_start" ) );
			console.log("date when next clicked: " + date);
			date.setDate( new Date(date).getDate() + 7 );
			console.log("date after setting for next week: " + date);
			var nextWeek = getWeekBookends( date );
			var timesheetData = getTimesheet( $( "#timesheet-tasks-list" ).data( "person_id" ), nextWeek );
			evt.preventDefault();
		})
		.end()
		.find( ".current-date" )
		.click( function( evt ) {
			saveTimesheet( $( '#timesheet-tasks-list' ) );
			var thisWeek = getWeekBookends( );
			var timesheetData = getTimesheet( $( "#timesheet-tasks-list" ).data( "person_id" ), thisWeek );
			evt.preventDefault();
		});
		
			
	$( "#date-picker" )
		.datepicker({
			onSelect: function( date ) {
				saveTimesheet( $( '#timesheet-tasks-list' ) );
				var date = date;
				var showWeek = getWeekBookends( date );
				var timesheetData = getTimesheet( $( "#timesheet-tasks-list" ).data( "person_id" ), showWeek );
				
			}
		})
		.button();
		
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
