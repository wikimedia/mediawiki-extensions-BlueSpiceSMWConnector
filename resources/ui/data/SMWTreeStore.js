bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.SMWTreeStore = function( cfg ) {
	bs.smwconnector.ui.data.SMWTreeStore.parent.call( this, cfg );
	this.node = '';
};

OO.inheritClass( bs.smwconnector.ui.data.SMWTreeStore, OOJSPlus.ui.data.store.RemoteStore );

bs.smwconnector.ui.data.SMWTreeStore.prototype.doLoadData = function() {
	var dfd = $.Deferred();
	this.api.abort();
	var data = {
		action: this.action,
		start: this.offset,
		limit: this.limit,
		filter: this.getFiltersForRemote(),
		query: this.getQuery(),
		sort: this.getSortForRemote(),
		node: this.node || ''
	};
	this.api.get( data ).done( function( response ) {
		if ( response.hasOwnProperty( 'results' ) ) {
			this.total = response.total;
			dfd.resolve( this.indexData( response.results ) );
		}
	}.bind( this ) ).fail( function( e ) {
		dfd.reject( e );
	} );

	return dfd.promise();
};

bs.smwconnector.ui.data.SMWTreeStore.prototype.getSubpages = function( node ) {
	this.node = node;
	return this.reload();
};