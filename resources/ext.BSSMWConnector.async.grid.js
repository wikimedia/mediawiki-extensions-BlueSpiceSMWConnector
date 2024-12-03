( function( $ ){
	Ext.onReady( function(){
		$( '.bs-smw-connector-async-ask-grid-container' ).each( function() {
			var $container = $( this );
			var data = $container.data( 'query' );

			Ext.require( 'BS.SMWConnector.grid.AsyncResultPrinter', function() {
				Ext.create( 'BS.SMWConnector.grid.AsyncResultPrinter', {
					renderTo: $container[0],
					queryData: data,
					storeAction: data.storeAction
				} );
			} );
		} );
	} );
} )( jQuery );
