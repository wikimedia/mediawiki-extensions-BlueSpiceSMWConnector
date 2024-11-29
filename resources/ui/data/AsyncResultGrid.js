bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.AsyncResultGrid = function( cfg ) {
	var data = cfg.data || {};
	cfg.store = new bs.smwconnector.ui.data.SMWStore({
		action: data.storeAction,
		pageSize: 25,
		props: data.props || {},
		query: data.query || '',
	} );
	cfg.store.connect( this, {
		buildMeta: 'onBuildMeta'
	} );
	this.initialized = false;
	bs.smwconnector.ui.data.AsyncResultGrid.parent.call( this, cfg );
};

OO.inheritClass( bs.smwconnector.ui.data.AsyncResultGrid, OOJSPlus.ui.data.GridWidget );

bs.smwconnector.ui.data.AsyncResultGrid.prototype.onBuildMeta = function( meta ) {
	if ( !this.initialized ) {
		this.initialize( meta );
	}
};

bs.smwconnector.ui.data.AsyncResultGrid.prototype.initialize = function( meta ) {
	this.initialized = true;
	this.buildColumns( this.prepareColumns( meta ) );
	this.addHeader();
};

bs.smwconnector.ui.data.AsyncResultGrid.prototype.prepareColumns = function( meta ) {
	var columns = {
		page: {
			type: 'text',
			valueParser: function( value, row ) {
				return new OO.ui.HtmlSnippet( row.page_link );
			},
			filter: { type: 'text' }
		}
	};
	for ( var key in meta ) {
		if ( !meta.hasOwnProperty( key ) ) {
			continue;
		}
		if ( key === 'page' || key.endsWith( '_link' ) ) {
			continue;
		}
		var metaItem = meta[key];
		var column = {
			headerText: key,
			type: meta.type === 'float' ? 'number' : meta.type,
		};

		column.valueParser = function( value, row, id ) {
			if ( row.hasOwnProperty( id + '_link' ) ) {
				return new OO.ui.HtmlSnippet( row[id + '_link'] );
			}
			return value;
		};
		if ( metaItem.filterable ) {
			column.filter = {
				type: meta.type === 'float' ? 'number' : meta.type
			};
		}
		if ( metaItem.sortable ) {
			column.sortable = true;
		}
		columns[key.replaceAll( ' ', '_')] = column;
	}
	return columns;
};

