;
$(document).ready( function() {
	var $projectInfoEdit = $( '#project-info' ).clone( true );
	$projectInfoEdit.filter( '.entity-sub-title' )
		.html( 'Edit Project Info' )
		.filter( '.name' )
		.html( '<label for="project-name" <?php //validateField("project_name", $missingFields)?> class="entity-details-label">Project Name:</label><input id="project-name" name="project_name" class="project-name-input" type="text" tabindex="8" value="<?php //echo $project->getValueEncoded("project_name")?>" />' )
		.end()
		.filter( '.client' )
		.html( '<label for="client-id" class="entity-details-label">Select the client:</label><input id="project-name" name="project_name" class="project-name-input" type="text" tabindex="8" value="<?php //echo $project->getValueEncoded("project_name")?>" />' )
		.end()
		.filter( '.project-code' )
		.html( '<label for="project-code" <?php validateField("project_code", $missingFields)?> class="entity-details-label">Project code</label><input id="project-code" name="project_code" class="project-code-input" type="text" tabindex="6" value="<?php echo $project->getValueEncoded("project_code")?>" />' )
		.end()
		.filter( '.link-btn a' )
		.replaceWith( '<a id="save-project-btn" class="" href="#">Save Project Info</a>' );
		
	$( '#edit-project-btn' ).click( function( evt ) {
		$( '#project-info' ).before( $projectInfoEdit );
		console.log($projectInfoEdit);
	});

	$(function() { //tabs interface for project-detail.php
		$( ".tabs" ).tabs();
	});

	$(function(){ //table display/sorter with filter/search for projects.php
		$( "#project-list" ).tablesorter({
			sortList: [[0,0], [1,0]],
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
	
	
});