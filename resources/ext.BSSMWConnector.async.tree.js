( function ( $ ) {
	$( '.bs-smw-connector-async-ask-tree-container' ).each( function () {
		const $container = $( this );
		const data = $container.data( 'query' );

		const store = new bs.smwconnector.ui.data.SMWTreeStore( {
			action: 'bs-smw-connector-tree-ask-store',
			query: data.query,
			pageSize: 500
		} );
		store.load().done( ( data ) => { // eslint-disable-line no-shadow
			const tree = new bs.smwconnector.ui.data.AsyncResultTree( {
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
}( jQuery ) );
