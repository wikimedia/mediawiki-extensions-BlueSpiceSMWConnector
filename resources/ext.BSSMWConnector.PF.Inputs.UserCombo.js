bs_smwc_pf_input_usercombo_init = function( input_id, params ) {
	mw.loader.using( [ 'ext.BSSMWConnector', 'ext.bluespice.extjs' ] ).done( function() {
		Ext.onReady( function(){
			_initUserCombo( input_id, params );
		} );
	});

	function _initUserCombo( input_id, params ) {
		var userCombo = Ext.create( 'BS.form.UserCombo', {
			hideLabel: true,
			value_field: 'user_name',
			style: 'display: inline-block; background-color: transparent',
			storeFilters: [
				{
					type: 'list',
					comparison: 'ct',
					property: 'groups',
					value: params.groups
				}
			]
		} );

		//On multitemplate, the container we are rendering to loses its id,
		//so it needs to be recreated
		if( $( '#' + input_id + '_cnt' ).length == 0 ) {
			$( '#' + input_id ).parent( 'span' ).attr( 'id', input_id + '_cnt' );
		}

		userCombo.render( $( '#' + input_id + '_cnt' )[0] );

		//Update hidden input on change
		userCombo.addListener( 'select', function( sender, record ) {
			if( !record || !record.hasOwnProperty( 'data' ) ) {
				$( '#' + input_id ).val('');
			}
			$( '#' + input_id ).val( record.get( 'page_prefixed_text' ) );
		} );

		//Set value
		if( params.hasOwnProperty( 'userRecord' ) ) {
			userCombo.setValue( Ext.create( 'BS.model.User', params.userRecord ) );
		}
	}
};