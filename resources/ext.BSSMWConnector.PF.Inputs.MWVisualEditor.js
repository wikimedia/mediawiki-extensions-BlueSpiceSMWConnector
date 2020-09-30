bs_smwc_pf_mw_visualeditor_init = function( input_id, params ) {
	mw.loader.using( 'ext.BSSMWConnector.visualEditor' ).done( function() {
		_initMWVisualEditor( input_id, params );
	});

	function _initMWVisualEditor( input_id, params ) {
		var value = '';
		if ( params.current_value ) {
			value = bs_smwc_pf_mw_visualeditor_decode_pipe( params.current_value );
		}

		mw.loader.using( 'ext.bluespice.visualEditorConnector.standalone.bootstrap' ).done( function() {
			var cfg = {
				placeholder: value,
				value: value,
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
		textToSubmit = bs_smwc_pf_mw_visualeditor_encode_pipe( textToSubmit );
		$( '#' + input_id ).val( textToSubmit );
	}

	return true;
};

bs_smwc_pf_mw_visualeditor_encode_pipe = function( text ) {
	return text.split( '|' ).join( '{{!}}' );
};

bs_smwc_pf_mw_visualeditor_decode_pipe = function( text ) {
	return text.split( '{{!}}' ).join( '|' );
};
