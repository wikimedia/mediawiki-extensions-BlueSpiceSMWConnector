bs_smwc_pf_input_usertags_init = function ( input_id, params ) { // eslint-disable-line camelcase, no-implicit-globals, no-undef
	mw.loader.using( [ 'ext.BSSMWConnector', 'ext.oOJSPlus.widgets' ] ).done( () => {
		_initUserTags( input_id, params );
	} );

	function _initUserTags( input_id, params ) { // eslint-disable-line camelcase, no-shadow, no-underscore-dangle
		const cfg = {
			$overlay: true
		};
		if ( params.groups ) {
			cfg.groups = params.groups;
		}
		cfg.placeholder = params.placeholder;
		const userPicker = new OOJSPlus.ui.widget.UsersMultiselectWidget( cfg );
		userPicker.$element.css( 'min-width', '200px' );

		// On multitemplate, the container we are rendering to loses its id,
		// so it needs to be recreated
		let $cnt = $( '#' + input_id + '_cnt' ); // eslint-disable-line camelcase
		const $input = $( '#' + input_id ); // eslint-disable-line camelcase
		if ( $cnt.length === 0 ) {
			$input.parent( 'span' ).attr( 'id', input_id + '_cnt' ); // eslint-disable-line camelcase
			$cnt = $input.parent();
		}

		userPicker.connect( this, {
			change: function ( value ) {
				const promises = [];
				for ( let i = 0; i < value.length; i++ ) {
					promises.push( mws.commonwebapis.user.getByUsername( value[ i ].getData() ) );
				}
				$.when.apply( $, promises ).done( function () {
					const users = [];
					for ( let i = 0; i < arguments.length; i++ ) {
						users.push( arguments[ i ].page_prefixed_text );
					}
					$input.val( users.join( ',' ) );
				} );
			}
		} );
		$cnt.append( userPicker.$element );

		if ( params.hasOwnProperty( 'users' ) ) {
			userPicker.setValue( params.users );
		}
	}
};
