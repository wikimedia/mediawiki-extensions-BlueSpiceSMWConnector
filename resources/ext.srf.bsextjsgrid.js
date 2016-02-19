(function( mw, $, bs, d, undefinded ){
	Ext.onReady( function(){
		$( '.srf-bsextjsgrid' ).each( function() {

			// The container and data object are specified as super-local
			// object, this ensures that for this context instance any update
			// is made made available for any other local function within the same
			// instance
			var $context = $( this );
			var smwApi = new smw.Api();
			var data = smwApi.parse( mw.config.get( $context.attr('id') ) );

			Ext.create( 'BS.SMWConnector.grid.AskResultPrinter', {
				renderTo: this,
				bsAskData: data
			});
		});
	});
})( mediaWiki, jQuery, blueSpice, document );