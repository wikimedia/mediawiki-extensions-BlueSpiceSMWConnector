( function( mw, jQuery, bs ) {
	Ext.onReady(function(){
		mw.hook( 'ext.bookshelfui.addmass.create' ).add( function( dialog ){
			var cbSMWProperty = Ext.create( 'BS.SMWConnector.field.SMWCombo' );

			dialog.registerSource( 'smwproperty', 'bs-dlg-choosesmwprop-type-smwprop', cbSMWProperty );
		});
	});
})( mediaWiki, jQuery, blueSpice );