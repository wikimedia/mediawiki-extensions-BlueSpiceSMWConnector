( function (mw, $, bs, undefined ) {

	function getSMWFAutoComplete( cfg ) {
		var config = cfg || {};
		if( !config.property ) {
			config.property = 'Name';
		}
		if( !config.fields ) {
			config.fields = ['title'];
		}

		return new Ext.data.Store({
			autoLoad: true,
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript('api'),
				extraParams: {
					"action":"sfautocomplete",
					"property": config.property,
					"format": "json"
				},
				reader: {
					type: 'json',
					rootProperty: 'sfautocomplete'
				}
			},
			fields: config.fields
		});
	}

	bs.smwc = bs.semanticMediaWikiConnector = {
		getSMWFAutoComplete: getSMWFAutoComplete,
		pf: {
			input: {}
		}
	};

} (mediaWiki, jQuery, blueSpice) );

( function ( mw, $, bs, undefined ) {
	mw.loader.using( 'ext.bluespice.visualEditorConnector' ).done( function() {
		var cfg = {
			placeholder: $( '#pf_free_text' ).text(),
			value: $( '#pf_free_text' ).text(),
			selector: '#pf_free_text.bs-mwvisualeditor',
			id : 'pf-freetext_ve',
			classes : ['bs-pf-visualeditor-text'],
			format: 'wikitext'
		};

		if ( $( cfg.selector ).length == 0 ) {
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
		});
	});
} (mediaWiki, jQuery, blueSpice) );