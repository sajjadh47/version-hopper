jQuery( document ).ready( function ( $ ) {
	$( document ).on( 'click', '.apply_package', function ( e ) {
		e.preventDefault();

		var $btn       = $( this );
		var $tr        = $btn.closest( 'tr' );
		var $form      = $btn.closest( 'form' );
		var $select    = $form.find( 'select[name="vhversionhopper_version"]' );
		var version    = $select.val();

		if ( ! version ) {
			alert( VersionHopper.versionNotSelected );
			return;
		}

		if ( ! confirm( VersionHopper.confirm ) ) {
			return;
		}

		$( '.apply_package' ).prop( 'disabled', true );
		
		$btn.text( VersionHopper.applying );

		var data = {
			action: 'vhversionhopper_apply_package',
			nonce: VersionHopper.nonce,
			vhversionhopper_type: $form.find( 'input[name="vhversionhopper_type"]' ).val(),
			vhversionhopper_slug: $form.find( 'input[name="vhversionhopper_slug"]' ).val(),
			vhversionhopper_file: $form.find( 'input[name="vhversionhopper_file"]' ).val(),
			vhversionhopper_version: version,
		};

		$.post( VersionHopper.ajaxurl, data, function ( response ) {
			if ( response.success ) {
				$( '.vhversionhopper_messages' ).remove();

				var html = '<div class="wrap vhversionhopper_messages">';

				response.data.messages.forEach( function( msg ){
					html += '<p>' + msg + '</p>';
				} );

				html += '<div>';

				$( '.wrap.vhversionhopper_hopper' ).prepend( html );

				$tr.find( 'td.current.column-current strong' ).text( version );

				$select.val( '' );
			} else {
				alert( response.data || VersionHopper.failed );
			}
		} )
		.fail( function () {
			alert( VersionHopper.failed );
		} )
		.always( function() {
			$( '.apply_package' ).prop( 'disabled', false );
			$btn.text( VersionHopper.btn );
		} );
	} );
} );