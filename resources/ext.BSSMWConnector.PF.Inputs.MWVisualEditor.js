bs_smwc_pf_mw_visualeditor_init = function ( input_id, params ) { // eslint-disable-line camelcase, no-implicit-globals, no-undef
	mw.loader.using( 'ext.BSSMWConnector.visualEditor' ).done( () => {
		_initMWVisualEditor( input_id, params );
	} );

	function _initMWVisualEditor( input_id, params ) { // eslint-disable-line camelcase, no-shadow, no-underscore-dangle
		let value = '';
		if ( params.current_value ) {
			value = bs_smwc_pf_mw_visualeditor_decode_pipe( params.current_value ); // eslint-disable-line no-undef
		}

		mw.loader.using( 'ext.bluespice.visualEditorConnector.standalone.bootstrap' ).done( function () {
			const cfg = {
				placeholder: value,
				value: value,
				selector: '#' + input_id, // eslint-disable-line camelcase
				id: input_id + '_ve', // eslint-disable-line camelcase
				classes: [ 'bs-pf-visualeditor-text' ],
				format: 'wikitext'
			};

			this.text = new bs.ui.widget.TextInputMWVisualEditor( cfg );
			$( cfg.selector + '_cnt' ).append( this.text.$element );
			this.text.makeVisualEditor();
			$( cfg.selector + '_ve' ).hide();
		} );
	}
};

bs_smwc_pf_mw_visualeditor_validate = function ( input_id, params ) { // eslint-disable-line camelcase, no-implicit-globals, no-undef, no-unused-vars
	let textToSubmit = bs.vec.getInstance( input_id + '_ve' ).getWikiTextSync(); // eslint-disable-line camelcase

	if ( textToSubmit === false ) {
		return false;
	} else {
		textToSubmit = bs_smwc_pf_mw_visualeditor_encode_pipe( textToSubmit ); // eslint-disable-line no-undef
		$( '#' + input_id ).val( textToSubmit ); // eslint-disable-line camelcase
	}

	return true;
};

bs_smwc_pf_mw_visualeditor_encode_pipe = function ( text ) { // eslint-disable-line camelcase, no-implicit-globals, no-undef
	return text.split( '|' ).join( '{{!}}' );
};

bs_smwc_pf_mw_visualeditor_decode_pipe = function ( text ) { // eslint-disable-line camelcase, no-implicit-globals, no-undef
	return text.split( '{{!}}' ).join( '|' );
};
