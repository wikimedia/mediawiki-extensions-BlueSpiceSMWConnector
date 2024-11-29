( function( $ ){
	$( '.bs-smw-connector-async-ask-grid-container' ).each( function() {
		var $container = $( this );
		var data = $container.data( 'query' );

		var input = new OO.ui.SearchInputWidget();
		var grid = new bs.smwconnector.ui.data.AsyncResultGrid( {
			data: data
		} );
		input.connect( grid, {
			change: function( value ) {
				if ( !this.initialized ) {
					return;
				}
				this.store.globalQuery( value );
			}
		} );
		$container.append( input.$element, grid.$element );
	} );
} )( jQuery );
