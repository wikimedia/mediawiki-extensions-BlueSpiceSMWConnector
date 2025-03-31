bs_smwc_pf_input_usercombo_init = function( input_id, params ) {
	mw.loader.using( [ 'ext.BSSMWConnector', 'ext.oOJSPlus.widgets' ] ).done( function() {
		_initUserCombo( input_id, params );
	});

	function _initUserCombo( input_id, params ) {
		var cfg = {
			$overlay: true
		};
		if ( params.groups ) {
			cfg.groups = params.groups;
		}
		var userPicker = new OOJSPlus.ui.widget.UserPickerWidget( cfg );

		//On multitemplate, the container we are rendering to loses its id,
		//so it needs to be recreated
		let $cnt = $( '#' + input_id + '_cnt' );
		const $input = $( '#' + input_id );
		if ( $cnt.length === 0 ) {
			$input.parent( 'span' ).attr( 'id', input_id + '_cnt' );
			$cnt = $input.parent();
		}

		userPicker.connect( this, {
			change: function ( value ) {
				var selected = userPicker.getSelectedUser();
				if ( selected ) {
					$input.val( selected.userWidget.user.page_prefixed_text );
				} else {
					$input.val( '' );
				}
			},
			choose: function ( item ) {
				$input.val( item.userWidget.user.page_prefixed_text );
			}
		} );
		$cnt.append( userPicker.$element );

		if( params.hasOwnProperty( 'username' ) ) {
			userPicker.setValue( params.username );
		}
	}
};