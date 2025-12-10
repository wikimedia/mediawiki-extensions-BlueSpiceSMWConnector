bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.SMWTreeStore = function ( cfg ) {
	bs.smwconnector.ui.data.SMWTreeStore.parent.call( this, cfg );
	this.node = cfg.node || '';
};

OO.inheritClass( bs.smwconnector.ui.data.SMWTreeStore, OOJSPlus.ui.data.store.RemoteStore );

bs.smwconnector.ui.data.SMWTreeStore.prototype.doLoadData = function () {
	const dfd = $.Deferred();
	this.api.abort();
	const data = {
		action: this.action,
		start: this.offset,
		limit: this.limit,
		filter: this.getFiltersForRemote(),
		query: this.getQuery(),
		sort: this.getSortForRemote(),
		node: this.node || ''
	};
	this.api.get( data ).done( ( response ) => {
		if ( response.hasOwnProperty( 'results' ) ) {
			this.total = response.total;
			const results = response.results.map( ( item ) => {
				if ( item.page_url ) {
					item.href = item.page_url;
				}
				return item;
			} );

			dfd.resolve( this.indexData( results ) );
		}
	} ).fail( ( e ) => {
		dfd.reject( e );
	} );

	return dfd.promise();
};

bs.smwconnector.ui.data.SMWTreeStore.prototype.getSubpages = function ( node ) {
	this.node = node;
	return this.reload();
};
