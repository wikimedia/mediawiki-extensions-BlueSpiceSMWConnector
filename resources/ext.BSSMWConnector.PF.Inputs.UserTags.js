bs_smwc_pf_input_usertags_init = function( input_id, params ){
	mw.loader.using( 'ext.BSSMWConnector' ).done( function() {
		Ext.onReady( function(){
			_initUserTags( input_id, params );
		} );
	});

	function _initUserTags( input_id, params ) {
		var userTagField = Ext.create( 'BS.form.field.UserTag', {
			minWidth: 200,
			hideLabel: true,
			style: 'display: inline-block; background-color: transparent'
		} );

		//On multitemplate, the container we are rendering to loses its id,
		//so it needs to be recreated
		if( $( '#' + input_id + '_cnt' ).length == 0 ) {
			$( '#' + input_id ).parent( 'span' ).attr( 'id', input_id + '_cnt' );
		}

		userTagField.render( $( '#' + input_id + '_cnt' )[0] );

		//Update hidden input on change
		userTagField.addListener( 'select', function( sender, record ) {
			if( record.length === 0 ) {
				$( '#' + input_id ).val( '' );
			}
			$( '#' + input_id ).val( userTagField.getValue().join( ',' ) );
		} );

		//Set value
		if( params.current_value ) {
			userTagField.select( params.current_value );
		}
	}
};
