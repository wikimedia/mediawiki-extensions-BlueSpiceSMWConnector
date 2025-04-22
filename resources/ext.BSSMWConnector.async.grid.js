( function ( $ ) {
	$( '.bs-smw-connector-async-ask-grid-container' ).each( function () {
		const $container = $( this );
		const data = $container.data( 'query' );

		const input = new OO.ui.SearchInputWidget();
		const grid = new bs.smwconnector.ui.data.AsyncResultGrid( {
			data: data
		} );
		input.connect( grid, {
			change: function ( value ) {
				if ( !this.initialized ) {
					return;
				}
				this.store.globalQuery( value );
			}
		} );
		$container.append( input.$element, grid.$element );
	} );
}( jQuery ) );
