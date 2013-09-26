;
$(document).ready( function() {
	var $infoSyncBtn = $( '#contact-info-sync' );

	$infoSyncBtn.click( function() {
		
		var clientInfo = $( '.client-contact-info-input' );
		var contactInfo = $( '.contact-contact-info-input' );
		var contactData = [];
		if (this.checked) {
			clientInfo.each( function( index ) {
				contactData.push( $( this ).val() );
			});
			contactInfo.each( function( index ) {
				$( this ).val( contactData[index] );
			});
		} else {
			contactInfo.each( function( index) {
				$( this ).val( "" );
			});
		}
	});
	
	var contactCtr = 0;
	var incrementIDs = function( i, attr ) {
		var indexPat = /(-[0-9]+)$/;
		if ( indexPat.test( attr ) ) {
			//console.log(attr.search(indexPat));
			attr.replace( indexPat, "" );
			//console.log(attr);
		}
		return attr += "-" + contactCtr;
	};
	
	var $contactInputs = $( '#contact-details' );
	$contactInputs.attr( 'id', incrementIDs )
			.find( 'input' )
			.each( function( index ) {
				//console.log($( this ).attr('id'));
				$( this ).attr( 'id', incrementIDs );
			})
			.end()
			.find( 'label' )
			.each( function( index ) {
				$( this ).attr( 'for', incrementIDs );
				//console.log($(this).attr('for'));
			});

	var $cancelContact = $( '.cancel-additional' )
		.addClass( 'disabled' );

	$( '#add-additional-link' ).click( function( evt ) {
		contactCtr = $( '.contact-details-entry' ).not( '#contact-save' ).length;
		$cancelContact.removeClass( 'disabled' );
		var $newContactDetailsForm = $( this )
			.parents( '.contact-details-entry' )
			.prev()
			.clone( true );
		
		$newContactDetailsForm
			.attr( 'id', incrementIDs )
			.find( 'input, a' )
			.each( function( index ) {
				$( this ).attr( 'id', incrementIDs );
			})
			.end()
			.find( 'label' )
			.each( function( index ) {
				$( this ).attr( 'for', incrementIDs );
				//console.log($(this).attr('for'));
			});
		
		$newContactDetailsForm
			.find( '[type="checkbox"]' )
			.prop( 'checked', false )
			.end()
			.find( '[type="text"]' )
			.val( '' );
		
		$( '.contact-details-entry' )
			.last()
			.before( $newContactDetailsForm );
		
		
		
		$( '#contact-save-btn' ).val( '+ Save Contacts' );
		
		evt.preventDefault();
	});
	
	

		/*
.click( function( evt ) {
			console.log('cancel');
			$( '.contact-details-list' ).find( '[type="checkbox"]' ).prop( 'checked', false );
			$ ( this ).prop( 'checked', true )
			if ( $( this ).parent() ) {			
				if ( $( this ).parent().next().has( '#add-additional-link' ) ) {
				var addNewContactLink = $( this ).parent().next( ).detach();
				$( this ).parents( '.contact-details-entry' ).remove();
				$( '#contact-detail' ).last().find( '.contact-details-list' ).append( addNewContactLink );
			}

*/
			
/*			evt.preventDefault();
		})
		
*/
	


	
	
	
});