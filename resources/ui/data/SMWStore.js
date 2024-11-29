bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.SMWStore = function( cfg ) {
	bs.smwconnector.ui.data.SMWStore.parent.call( this, cfg );
	this.props = cfg.props;
};

OO.inheritClass( bs.smwconnector.ui.data.SMWStore, OOJSPlus.ui.data.store.RemoteStore );

bs.smwconnector.ui.data.SMWStore.prototype.doLoadData = function() {
	var dfd = $.Deferred();
	this.api.abort();
	var data = {
		action: this.action,
		start: this.offset,
		limit: this.limit,
		filter: this.getFiltersForRemote(),
		query: this.getQuery(),
		sort: this.getSortForRemote(),
		props: JSON.stringify( this.props ),
	};
	this.api.get( data ).done( function( response ) {
		if ( response.hasOwnProperty( 'results' ) ) {
			this.total = response.total;
			dfd.resolve( this.indexData( response.results ), response.metaData );
		}
	}.bind( this ) ).fail( function( e ) {
		dfd.reject( e );
	} );

	return dfd.promise();
};

bs.smwconnector.ui.data.SMWStore.prototype.globalQuery = function( value ) {
	this.filters['_global'] = new OOJSPlus.ui.data.filter.String( {
		value: value,
		column: '_global',
	} );
	this.reload();
};

bs.smwconnector.ui.data.SMWStore.prototype.load = function() {
	var dfd = $.Deferred();
	this.emit( 'loading' );
	this.doLoadData().done( function( data, meta ) {
		var formattedRows = {};
		for ( var rowIndex in data ) {
			var formattedRow = {};
			for ( var key in data[rowIndex] ) {
				formattedRow[key.replaceAll( ' ', '_')] = data[rowIndex][key];
			}
			formattedRows[rowIndex] = formattedRow;
		}
		this.data = $.extend( {}, this.data, formattedRows );
		this.emit( 'buildMeta', meta );
		this.emit( 'loaded', this.data );
		dfd.resolve( this.data );
	}.bind( this ) ).fail( function( e ) {
		this.emit( 'loadFailed', e );
		dfd.reject( e );
	}.bind( this ) );

	return dfd.promise();
};