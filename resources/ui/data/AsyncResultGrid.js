bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.AsyncResultGrid = function ( cfg ) {
	const data = cfg.data || {};
	cfg.store = new bs.smwconnector.ui.data.SMWStore( {
		action: data.storeAction,
		pageSize: 25,
		props: data.props || {},
		query: data.query || ''
	} );
	cfg.store.connect( this, {
		buildMeta: 'onBuildMeta'
	} );
	this.initialized = false;
	this.hiddenColumns = data.hiddenColumns || [];
	bs.smwconnector.ui.data.AsyncResultGrid.parent.call( this, cfg );
};

OO.inheritClass( bs.smwconnector.ui.data.AsyncResultGrid, OOJSPlus.ui.data.GridWidget );

bs.smwconnector.ui.data.AsyncResultGrid.prototype.onBuildMeta = function ( meta ) {
	if ( !this.initialized ) {
		this.initialize( meta );
	}
};

bs.smwconnector.ui.data.AsyncResultGrid.prototype.initialize = function ( meta ) {
	this.initialized = true;
	this.buildColumns( this.prepareColumns( meta ) );
	this.addHeader();
	this.updateToolbar();
};

bs.smwconnector.ui.data.AsyncResultGrid.prototype.prepareColumns = function ( meta ) {
	const columns = {
		page: {
			type: 'text',
			valueParser: function ( value, row ) {
				return new OO.ui.HtmlSnippet( row.page_link );
			},
			filter: { type: 'text' }
		}
	};
	for ( const key in meta ) {
		if ( !meta.hasOwnProperty( key ) ) {
			continue;
		}
		if ( key === 'page' || key.endsWith( '_link' ) ) {
			continue;
		}
		const metaItem = meta[ key ];
		const column = {
			headerText: key,
			type: meta.type === 'float' ? 'number' : meta.type
		};

		column.valueParser = function ( value, row, id ) {
			if ( row.hasOwnProperty( id + '_link' ) ) {
				return new OO.ui.HtmlSnippet( row[ id + '_link' ] );
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
		columns[ key.replaceAll( ' ', '_' ) ] = column;

		if ( this.hiddenColumns.includes( metaItem.property_name ) ) {
			column.hidden = true;
		}
	}

	return columns;
};

/**
 * Update settings button in toolbar after async retrieval of columns
 */
bs.smwconnector.ui.data.AsyncResultGrid.prototype.updateToolbar = function () {
	const settingsWidget = this.getGridSettingsWidget();
	if ( !( settingsWidget instanceof OO.ui.PopupButtonWidget ) ) {
		return;
	}

	const items = this.toolbar.staticControls.getItems();
	const toUpdate = items.filter(
		item => item instanceof OO.ui.PopupButtonWidget
	);

	if ( toUpdate.length ) {
		// keep the index of the first removed popup so we can reinsert there
		const insertIndex = Math.max( 0, items.indexOf( toUpdate[0] ) );
		this.toolbar.staticControls.removeItems( toUpdate );
		this.toolbar.staticControls.addItems( [ settingsWidget ], insertIndex );
	}
};
