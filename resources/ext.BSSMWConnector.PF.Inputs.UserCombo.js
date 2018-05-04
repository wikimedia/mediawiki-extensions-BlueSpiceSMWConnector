mw.loader.using( 'ext.BSSMWConnector' ).done( function() {
	bs.smwc.pf.input.usercombo = {
		init: initUserCombo
	}

	function initUserCombo( input_id, params ) {
		Ext.onReady( function(){
			var userCombo = Ext.create( 'BS.form.UserCombo', {
				hideLabel: true,
				style: 'display: inline-block; background-color: white',
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

						for( idx in params.groups ) {
							var group = params.groups[idx];
							if( $.inArray( group, userGroups ) != -1 ) {
								return true;
							}
						}

						return false;
					}
				]
			} );

			//On multitemplate container we are rendering to loses its id,
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
				$( '#' + input_id ).val( record.data.page_prefixed_text );
			} );

			//Set value
			if( params.current_value ) {
				userCombo.setValueByUserName( params.current_value );
			}
		} );
	}
});