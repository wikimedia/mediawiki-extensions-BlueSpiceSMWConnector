bs_smwc_pf_input_usercombo_init = function( input_id, params ) {
	mw.loader.using( 'ext.BSSMWConnector' ).done( function() {
		Ext.onReady( function(){
			_initUserCombo( input_id, params );
		} );
	});

	function _initUserCombo( input_id, params ) {
		var userCombo = Ext.create( 'BS.form.UserCombo', {
			hideLabel: true,
			style: 'display: inline-block; background-color: transparent',
			storeFilters: [
				//filter by groups
				function( record ) {
					if( !params.groups || params.groups.length == 0 ) {
						return true;
					}

					var userGroups = record.data.groups;
					if( userGroups.length == 0 ) {
						return false;
					}

					for( var idx in params.groups ) {
						var group = params.groups[idx];
						if( $.inArray( group, userGroups ) ) {
							return true;
						}
					}

					return false;
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
			if( record.length === 0 ) {
				$( '#' + input_id ).val('');
			}

			record = record[0];
			$( '#' + input_id ).val( record.data.user_name );
		} );

		//Set value
		if( params.current_value ) {
			userCombo.setValueByUserName( params.current_value );
			$( '#' + input_id ).val( params.current_value );
		}
	}
};