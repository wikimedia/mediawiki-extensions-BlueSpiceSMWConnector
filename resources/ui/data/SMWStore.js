bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.SMWStore = function ( cfg ) {
	bs.smwconnector.ui.data.SMWStore.parent.call( this, cfg );
	this.props = cfg.props;
};

OO.inheritClass( bs.smwconnector.ui.data.SMWStore, OOJSPlus.ui.data.store.RemoteStore );

bs.smwconnector.ui.data.SMWStore.prototype.doLoadData = function () {
	const dfd = $.Deferred();
	this.api.abort();
	const data = {
		action: this.action,
		start: this.offset,
		limit: this.limit,
		filter: this.getFiltersForRemote(),
		query: this.getQuery(),
		sort: this.getSortForRemote(),
		props: JSON.stringify( this.props )
	};
	this.api.get( data ).done( ( response ) => {
		if ( response.hasOwnProperty( 'results' ) ) {
			this.total = response.total;
			dfd.resolve( this.indexData( response.results ), response.metaData );
		}
	} ).fail( ( e ) => {
		dfd.reject( e );
	} );

	return dfd.promise();
};

bs.smwconnector.ui.data.SMWStore.prototype.globalQuery = function ( value ) {
	this.filters._global = new OOJSPlus.ui.data.filter.String( { // eslint-disable-line no-underscore-dangle
		value: value,
		column: '_global'
	} );
	this.reload();
};

bs.smwconnector.ui.data.SMWStore.prototype.load = function () {
	const dfd = $.Deferred();
	this.emit( 'loading' );
	this.doLoadData().done( ( data, meta ) => {
		const formattedRows = {};
		for ( const rowIndex in data ) {
			const formattedRow = {};
			for ( const key in data[ rowIndex ] ) {
				formattedRow[ key.replaceAll( ' ', '_' ) ] = data[ rowIndex ][ key ];
			}
			formattedRows[ rowIndex ] = formattedRow;
		}
		this.data = Object.assign( {}, this.data, formattedRows );
		this.emit( 'buildMeta', meta );
		this.emit( 'loaded', this.data );
		dfd.resolve( this.data );
	} ).fail( ( e ) => {
		this.emit( 'loadFailed', e );
		dfd.reject( e );
	} );

	return dfd.promise();
};
