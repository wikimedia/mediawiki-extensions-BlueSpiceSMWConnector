( function( $ ){
	$( '.bs-smw-connector-async-ask-tree-container' ).each( function() {
		var $container = $( this );
		var data = $container.data( 'query' );

		var store = new bs.smwconnector.ui.data.SMWTreeStore( {
			action: 'bs-smw-connector-tree-ask-store',
			query: data.query,
			pageSize: 500
		} );
		store.load().done( function( data ) {
			var tree = new bs.smwconnector.ui.data.AsyncResultTree( {
				data: Object.values( data ),
				fixed: true,
				expanded: false,
				allowAdditions: false,
				allowDeletions: false,
				store: store
			} );
			$container.html( tree.$element );
		} );
	} );
} )( jQuery );
