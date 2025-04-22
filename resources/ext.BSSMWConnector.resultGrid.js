( function ( mw, $, bs ) {
	$( '.srf-bssmwconnector-result-grid' ).each( function () {

		// The container and data object are specified as super-local
		// object, this ensures that for this context instance any update
		// is made made available for any other local function within the same
		// instance
		const $context = $( this );
		const smwApi = new smw.Api();
		const data = smwApi.parse( mw.config.get( $context.attr( 'id' ) ) );

		const grid = new bs.smwconnector.ui.data.AskResultPrinter( {
			records: data
		} );
		$( this ).html( grid.$element );
	} );
}( mediaWiki, jQuery, blueSpice ) );
