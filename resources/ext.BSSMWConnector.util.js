// TODO: Move to BSF?
( function ( bs ) {

	function _makeWikiTextTemplate( templateName, data ) { // eslint-disable-line no-underscore-dangle
		const template = [];
		template.push( '{{' + templateName );
		for ( const fieldName in data ) {
			const value = data[ fieldName ];
			const line = '|' + fieldName + ' = ' + value;
			template.push( line );
		}
		template.push( '}}' );
		return template.join( '\n' );
	}

	bs.util.makeWikiTextTemplate = _makeWikiTextTemplate;

}( bs ) );
