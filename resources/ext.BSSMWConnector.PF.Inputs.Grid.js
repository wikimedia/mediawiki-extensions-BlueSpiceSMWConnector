bs_smwc_pf_input_grid_init = function( input_id, params ) {
	mw.loader.using( [ 'ext.BSSMWConnector', 'ext.bluespice.extjs']  ).done( function() {
		Ext.onReady( function(){
			_initGrid( input_id, params );
		} );
	});

	function _initGrid( input_id, params ) {
		var $hiddenField = $( '#' + input_id );
		var $container = $hiddenField.parents( '.bs-grid-field-container' );

		Ext.require( 'BS.SMWConnector.grid.GridInputField', function() {
			new BS.SMWConnector.grid.GridInputField({
				renderTo: $container[0],
				$formField: $hiddenField,
				templateName: $container.data( 'template' ),
				colDef: $container.data( 'coldef' )
			});
		});
	}
};