( function( $ ){
	Ext.onReady( function(){
		$( '.bs-smw-connector-async-ask-tree-container' ).each( function() {
			var $container = $( this );
			var data = $container.data( 'query' );

			Ext.require( 'BS.SMWConnector.tree.AsyncResultPrinter', function() {
				Ext.create( 'BS.SMWConnector.tree.AsyncResultPrinter', {
					renderTo: $container[0],
					query: data.query || '',
					rootNode: data.rootNode || '',
					storeAction: data.storeAction
				} );
			} );
		} );
	} );
} )( jQuery );
