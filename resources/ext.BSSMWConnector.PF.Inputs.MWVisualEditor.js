bs_smwc_pf_mw_visualeditor_init = function( input_id, params ) {
	mw.loader.using( 'ext.BSSMWConnector' ).done( function() {
			_initMWVisualEditor( input_id, params );
	});

	function _initMWVisualEditor( input_id, params ) {
		mw.loader.using( 'ext.bluespice.visualEditorConnector' ).done( function() {
			var cfg = {
				placeholder: params.current_value,
				value: params.current_value,
				selector: '#' + input_id,
				id : input_id + '_ve',
				classes : ['bs-pf-visualeditor-text'],
				format: 'wikitext'
			};

			this.text = new bs.ui.widget.TextInputMWVisualEditor( cfg );
			$( cfg.selector + "_cnt" ).append( this.text.$element );
			this.text.makeVisualEditor();
			$( cfg.selector + "_ve" ).hide();
		});
	}
};

bs_smwc_pf_mw_visualeditor_validate = function( input_id, params ) {
	var textToSubmit = bs.vec.getInstance( input_id + '_ve' ).getWikiTextSync();

	if ( textToSubmit === false ) {
		return false;
	} else {
		$( '#' + input_id ).val( textToSubmit );
	}

	return true;
};