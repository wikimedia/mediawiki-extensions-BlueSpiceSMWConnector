bs.util.registerNamespace( 'bs.swmconnector.ve' );

bs.swmconnector.ve.DataQueryDefinition = function( cfg ) {
	bs.swmconnector.ve.DataQueryDefinition.super.call( this, cfg );
};

OO.inheritClass( bs.swmconnector.ve.DataQueryDefinition, ext.visualEditorPlus.ui.tag.Definition );

bs.swmconnector.ve.DataQueryDefinition.prototype.modifyDataBeforeSetToModel = function ( data ) {
	if ( data.modified !== '+' && data.modifiedDate ) {
		data.modified += ' ' + data.modifiedDate;
		delete( data.modifiedDate );
	}
	return data;
};

bs.swmconnector.ve.DataQueryDefinition.prototype.modifyDataBeforeSetToInspector = function ( data ) {
	let operator = '';
	let date = '';
	if ( data.modified ) {
		for ( let i = 0; i < data.modified.length; i++ ) {
			const char = data.modified.charAt( i );
			if ( isNaN( char ) ) {
				operator += char;
			} else {
				date = data.modified.slice( Math.max( 0, i ) );
				break;
			}
		}
	}

	data.modified = operator || '+';
	if ( operator !== '+' ) {
		if ( date ) {
			data.modifiedDate = date;
		}
	}
	return data;
};

mw.hook( 'ext.visualEditorPlus.tags.registerTags' ).add( ( _registry, tags ) => {
	for ( let i = 0; i < tags.length; i++ ) {
		const tag = tags[ i ];
		if ( tag.tags.includes( 'dataquery' ) ) {
			_registry.registerTagDefinition( new bs.swmconnector.ve.DataQueryDefinition( tag.clientSpecification ) );
		}
	}
} );