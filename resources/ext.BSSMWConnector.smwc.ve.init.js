( function ( mw, $, bs, undefined ) {
	mw.loader.using( 'ext.bluespice.visualEditorConnector.standalone.bootstrap' ).done( function() {
		var cfg = {
			placeholder: $( '#pf_free_text' ).text(),
			value: $( '#pf_free_text' ).text(),
			selector: '#pf_free_text.bs-mwvisualeditor',
			id : 'pf-freetext_ve',
			classes : ['bs-pf-visualeditor-text'],
			format: 'wikitext'
		};

		if ( $( cfg.selector ).length === 0 ) {
			return;
		}

		this.text = new bs.ui.widget.TextInputMWVisualEditor( cfg );
		$( cfg.selector ).append( this.text.$element );
		this.text.makeVisualEditor();
		$( cfg.selector ).hide();

		$( '#pfForm' ).submit( function( e ){
			var textToSubmit = bs.vec.getInstance( 'pf-freetext_ve' ).getWikiTextSync();

			if ( textToSubmit === false ) {
				e.PreventDefault();
				return false;
			} else {
				$( '#pf_free_text' ).val( textToSubmit );
			}
		} );
	} ).fail( function (e ) {
		console.log( e );
	} );
} (mediaWiki, jQuery, blueSpice) );
