bs_smwc_pf_mw_visualeditor_init = function( input_id, params ) {
	mw.loader.using( 'ext.BSSMWConnector' ).done( function() {
			_initMWVisualEditor( input_id, params );
	});

	function _initMWVisualEditor( input_id, params ) {
		mw.loader.using( 'ext.bluespice.visualEditorConnector' ).done( function() {
			var valueText = _getHtmlFromWikitext_Sync( params.current_value );
			var cfg = {
				placeholder: valueText,
				value: valueText,
				selector: '#' + input_id,
				id : input_id + '_ve',
				classes : ['bs-pf-visualeditor-text'],
				// we do the wikitext conversion here in the code, so use html
				// to prevent processing
				format: 'html'
			};

			this.text = new bs.ui.widget.TextInputMWVisualEditor( cfg );
			$( cfg.selector + "_cnt" ).append( this.text.$element );
			this.text.makeVisualEditor();
			$( cfg.selector + "_ve" ).hide();
		});
	}

	function _getHtmlFromWikitext_Sync( wikitextToSubmit ) {
		var converterUrl = mw.config.get( 'bsgVisualEditorConnectorWikiTextConverterUrl', '' )
				+ "v3/transform/wikitext/to/html/";
		var requestHtml = $.ajax({
			type: "POST",
			url: converterUrl,
			async: false,
			data: {
				wikitext: wikitextToSubmit
			}
		});
		if ( requestHtml.status != "200" ) {
			return false;
		} else {
			return requestHtml.responseText;
		}
	}
};

bs_smwc_pf_mw_visualeditor_validate = function( input_id, params ) {
	var textToSubmit = bs.vec.getInstance( input_id + '_ve' ).getSurface().getHtml();
	var converterUrl = mw.config.get( 'bsgVisualEditorConnectorWikiTextConverterUrl', '' )
					+ "v3/transform/html/to/wikitext/";

	var requestWikiText = $.ajax({
		type: "POST",
		url: converterUrl,
		async: false,
		data: {
			html: textToSubmit
		}
	});
	if ( requestWikiText.status != "200" ) {
		return false;
	} else {
		$( '#' + input_id ).val( requestWikiText.responseText );
	}
	return true;
};