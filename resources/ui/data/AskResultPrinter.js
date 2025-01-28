bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.AskResultPrinter = function( cfg ) {
	this.prepareResults( cfg.records );
	cfg.columns = this.columns;
	cfg.data = this.records;
	cfg.store = cfg.store || this.prepareStore();
	bs.smwconnector.ui.data.AskResultPrinter.parent.call( this, cfg );
};

OO.inheritClass( bs.smwconnector.ui.data.AskResultPrinter, OOJSPlus.ui.data.GridWidget );

bs.smwconnector.ui.data.AskResultPrinter.prototype.prepareResults = function( data ) {
	if (
		!data ||
		!data.hasOwnProperty( 'query' ) ||
		!data.query.hasOwnProperty( 'result' ) ||
		!data.query.result.hasOwnProperty( 'results' )
	) {
		return [];
	}
	var results = [];
	var columns = {
		pageLabel: {
			headerText: data.query.ask.parameters.mainlabel || '',
			type: 'text',
			sticky: true,
			width: 200,
			valueParser: function ( value, row ) {
				if ( row.url ) {
					return new OO.ui.ButtonWidget( {
						label: value,
						href: row.url,
						framed: false
					} );
				}
				return value;
			},
			filter: {
				type: 'text'
			}
		}
	};
	for ( var key in data.query.result.results ) {
		if ( !data.query.result.results.hasOwnProperty( key ) ) {
			continue;
		}
		var result = data.query.result.results[key];
		var row = {
			rawPage: key,
			pageLabel: result.displaytitle || result.fulltext,
			url: result.fullurl || null
		};
		for ( var printoutKey in result.printouts ) {
			if ( !result.printouts.hasOwnProperty( printoutKey ) ) {
				continue;
			}
			row[printoutKey] = result.printouts[printoutKey];
			if ( !columns.hasOwnProperty( printoutKey ) ) {
				var type = this.getTypeFromPrintout( result.printouts[printoutKey] );
				columns[printoutKey] = {
					headerText: printoutKey,
					filter: { type: type },
					sortable: true,
					type: type,
					valueParser: function ( value, row ) {
						if ( typeof value.getDIType === 'function' ) {
							return this.makePrintoutValue( value );
						}
						return value;
					}.bind( this )
				};
			}
		}
		results.push( row );
	}
	this.records = results;
	this.columns = columns;
};

bs.smwconnector.ui.data.AskResultPrinter.prototype.getTypeFromPrintout = function( printout ) {
	for ( var key in printout ) {
		// If key is not a number, skip
		if ( isNaN( key ) ) {
			continue;
		}
		var di = printout[key];
		var type = di.getDIType();
		switch ( type ) {
			case '_wpg' :
				return 'url';
			case '_uri' :
				return 'url';
			case '_dat' :
				return 'date';
			case '_num' :
				return 'number';
			case '_qty' :
			case '_str' :
			case '_txt':
			case '_geo' :
			case '_boo':
			default:
				return 'text';
		}
	}
};

bs.smwconnector.ui.data.AskResultPrinter.prototype.makePrintoutValue = function( printout ) {
	var values = [];
	for ( var key in printout ) {
		// If key is not a number, skip
		if ( isNaN( key ) ) {
			continue;
		}
		var di = printout[key];
		var type = di.getDIType();
		switch ( type ) {
			case '_wpg' :
				values.push( new OO.ui.ButtonWidget( {
					label: di.getFullText(),
					href: di.getUri(),
					framed: false
				} ).$element );
				break;
			case '_uri' :
				values.push( new OO.ui.ButtonWidget( {
					label: di.getUri(),
					href: di.getUri(),
					framed: false
				} ).$element );
				break;
			case '_dat' :
				values.push( di.getDate().toLocaleString() );
				break;
			case '_num' :
				values.push( di.getValue() );
				break;
			case '_qty' :
				values.push( di.getValue() );
				break;
			case '_str' :
			case '_txt':
				values.push( di.getText() );
				break;
			case '_geo' :
				values.push( di.getLatitude() + ', ' + di.getLongitude() );
				break;
			case '_boo':
				values.push(
					di.getValue() === 't' ? mw.msg( 'bs-smwconnector-yes' ) : mw.msg( 'bs-smwconnector-no' )
				);
				break;
			default:
				values.push( di.getValue() );
				break;
		}
	}

	if ( values.every( function ( value ) {
		return typeof value === 'string';
	} ) ) {
		return values.join( ', ' );
	}
	if ( values.length === 1 ) {
		return new OO.ui.HtmlSnippet( values[0] );
	}
	return new OO.ui.HtmlSnippet( $( '<span>' ).html( $( '<ul>' ).append(
		...values.map( ( v ) => ( $( '<li>' ).html( v ) ) )
	) ).html() );
};

bs.smwconnector.ui.data.AskResultPrinter.prototype.prepareStore = function() {
	return new OOJSPlus.ui.data.store.Store( {
		data: this.records,
		// page size determined by query
		pageSize: 99999
	} );
};