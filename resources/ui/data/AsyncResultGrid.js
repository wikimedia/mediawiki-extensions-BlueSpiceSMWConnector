bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.AsyncResultGrid = function ( cfg ) {
	const data = cfg.data || {};
	cfg.store = new bs.smwconnector.ui.data.SMWStore( {
		action: data.storeAction,
		pageSize: 25,
		props: data.props || {},
		query: data.query || '',
		sorter: this.prepareSorter( data.sort )
	} );
	cfg.store.connect( this, {
		buildMeta: 'onBuildMeta'
	} );
	this.initialized = false;
	this.hiddenColumns = data.hiddenColumns || [];
	this.mainlabel = data.mainlabel || '';
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

	// Reset noFilter so buildColumns() can register sort/filter options from the
	// async columns (the parent constructor set it to true because columns were
	// not yet available at construction time).
	this.noFilter = false;
	this.buildColumns( this.prepareColumns( meta ) );

	// Mirror the implicit-no-filter check from Grid.js
	if (
		Object.keys( this.externalFilterConfig.sortOptions ).length === 0 &&
		Object.keys( this.externalFilterConfig.filterOptions ).length === 0 &&
		!this.externalFilterConfig.showQueryField
	) {
		this.noFilter = true;
	}

	// Create and mount the ExternalFilter widget, mirroring Grid.js lines 130-150
	if ( !this.noFilter ) {
		this.externalFilter = new OOJSPlus.ui.data.grid.ExternalFilter( this.externalFilterConfig );
		this.externalFilter.connect( this, {
			columnSort: ( column, direction ) => {
				const selector = 'th[data-field="' + column + '"]';
				if ( !this.columns[ column ] || this.columns[ column ].grid.$table.find( selector ).length < 1 ) {
					return;
				}
				const $columnHeader = this.columns[ column ].grid.$table.find( selector )[ 0 ];
				if ( direction.toLowerCase() === 'asc' ) {
					$( $columnHeader ).attr( 'aria-sort', 'ascending' );
				} else if ( direction.toLowerCase() === 'desc' ) {
					$( $columnHeader ).attr( 'aria-sort', 'descending' );
				} else {
					$( $columnHeader ).attr( 'aria-sort', 'none' );
				}
			}
		} );
		this.$filterWidgetCnt.append( this.externalFilter.$element );
	}

	this.addHeader();
	this.updateToolbar();

	// Re-render the current page now that columns are available.
	// dataAppended fired before initialize() was called (before columns existed),
	// so the paginator rendered empty rows. Calling showRange() again fixes that.
	if ( this.paginator && this.paginator.pages && this.paginator.pages.length > 0 ) {
		const range = this.paginator.pages[ this.paginator.currentPageIndex ];
		this.paginator.showRange( range[ 0 ], range[ 1 ] );
	}
};

bs.smwconnector.ui.data.AsyncResultGrid.prototype.prepareColumns = function ( meta ) {
	const columns = {
		page: {
			headerText: this.mainlabel || '',
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

bs.smwconnector.ui.data.AsyncResultGrid.prototype.prepareSorter = function ( sortCfg ) {
	if ( !sortCfg || !Array.isArray( sortCfg ) ) {
		return {};
	}
	const sorter = {};

	sortCfg.forEach( ( sort ) => {
		sorter[ sort.property ] = { direction: sort.direction };
	} );

	return sorter;
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
		( item ) => item instanceof OO.ui.PopupButtonWidget
	);

	if ( toUpdate.length ) {
		// keep the index of the first removed popup so we can reinsert there
		const insertIndex = Math.max( 0, items.indexOf( toUpdate[ 0 ] ) );
		this.toolbar.staticControls.removeItems( toUpdate );
		this.toolbar.staticControls.addItems( [ settingsWidget ], insertIndex );
	}
};
