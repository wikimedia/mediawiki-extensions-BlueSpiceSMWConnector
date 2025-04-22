( function ( mw, $, bs ) {
	mw.loader.using( 'ext.bluespice.visualEditorConnector.standalone.bootstrap' ).done( function () {
		const cfg = {
			placeholder: $( '#pf_free_text' ).text(),
			value: $( '#pf_free_text' ).text(),
			selector: '#pf_free_text.bs-mwvisualeditor',
			id: 'pf-freetext_ve',
			classes: [ 'bs-pf-visualeditor-text' ],
			format: 'wikitext'
		};

		if ( $( cfg.selector ).length === 0 ) {
			return;
		}

		this.text = new bs.ui.widget.TextInputMWVisualEditor( cfg );
		$( cfg.selector ).append( this.text.$element );
		this.text.makeVisualEditor();
		$( cfg.selector ).hide();

		$( '#pfForm' ).on( 'submit', ( e ) => {
			const textToSubmit = bs.vec.getInstance( 'pf-freetext_ve' ).getWikiTextSync();

			if ( textToSubmit === false ) {
				e.PreventDefault();
				return false;
			} else {
				$( '#pf_free_text' ).val( textToSubmit );
			}
		} );
	} ).fail( ( e ) => {
		console.log( e ); // eslint-disable-line no-console
	} );
}( mediaWiki, jQuery, blueSpice ) );
