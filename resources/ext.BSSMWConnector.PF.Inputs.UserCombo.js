bs_smwc_pf_input_usercombo_init = function ( input_id, params ) { // eslint-disable-line camelcase, no-implicit-globals, no-undef
	mw.loader.using( [ 'ext.BSSMWConnector', 'ext.oOJSPlus.widgets' ] ).done( () => {
		_initUserCombo( input_id, params );
	} );

	function _initUserCombo( input_id, params ) { // eslint-disable-line camelcase, no-shadow, no-underscore-dangle
		const cfg = {
			$overlay: true
		};
		if ( params.groups ) {
			cfg.groups = params.groups;
		}
		if ( params.placeholder ) {
			cfg.placeholder = params.placeholder;
		} else {
			cfg.placeholder = mw.msg( 'bs-smwconnector-user-input-placeholder' );
		}
		const userPicker = new OOJSPlus.ui.widget.UserPickerWidget( cfg );

		// On multitemplate, the container we are rendering to loses its id,
		// so it needs to be recreated
		let $cnt = $( '#' + input_id + '_cnt' ); // eslint-disable-line camelcase
		const $input = $( '#' + input_id ); // eslint-disable-line camelcase
		if ( $cnt.length === 0 ) {
			$input.parent( 'span' ).attr( 'id', input_id + '_cnt' ); // eslint-disable-line camelcase
			$cnt = $input.parent();
		}

		userPicker.connect( this, {
			change: function () {
				const selected = userPicker.getSelectedUser();
				if ( selected ) {
					$input.val( selected.userWidget.user.page_prefixed_text );
				}
			},
			choose: function ( item ) {
				$input.val( item.userWidget.user.page_prefixed_text );
			}
		} );
		$cnt.append( userPicker.$element );

		if ( params.hasOwnProperty( 'username' ) ) {
			userPicker.setValue( params.username );
		}
	}
};
