Ext.define( 'BS.SMWConnector.grid.AsyncResultPrinter', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.data.Store', 'Ext.toolbar.Paging', 'BS.store.BSApi',
		'Ext.data.SimpleStore', 'MWExt.form.field.Search'
	],
	frame: true,
	queryData: null,
	// Have space to show loading mask
	minHeight: 200,
	pageSize: 20,
	configDone: false,
	storeAction: 'bs-smw-connector-ask-store',
	pageSizePickers: {},

	initComponent: function() {
		this.store = new BS.store.BSApi( {
			apiAction: this.storeAction,
			proxy:{
				extraParams: {
					limit: this.pageSize,
					props: JSON.stringify( this.queryData.props || {} ),
					query: this.queryData.query || ''
				}
			},
			pageSize: this.pageSize,
			remoteSort: true,
			remoteFilter: true,
			sorters: this.queryData.sort || []
		} );
		this.store.on( 'metachange', function( store, meta ) {
			if ( !this.configDone ) {
				this.reconfigure( store, this.makeColumns( meta ) );
				this.configDone = true;
			}
		}.bind( this ) );

		this.plugins = this.makePlugins();
		this.dockedItems = this.makeDockedItems();

		this.callParent( arguments );
	},

	makePlugins: function() {
		return [
			'gridfilters'
		];
	},

	makeDockedItems: function() {
		var items = [];

		this.makeTopToolbar( items );
		this.makePagingToolbar( items, 'top' );
		this.makePagingToolbar( items );

		return items;
	},

	makeTopToolbar: function( items ) {
		this.sfFilter = new MWExt.form.field.Search( {
			labelAlign: 'right',
			flex: 3,
			store: this.store,
			paramName: '_global',
			listeners: {
				change: function ( field, newValue, oldValue, eOpts ) {
					field.onTrigger2Click();
					return true;
				}
			}
		} );

		var toolBarItems = [
			this.sfFilter
		];

		this.tbTop = new Ext.toolbar.Toolbar({
			dock: 'top',
			items: toolBarItems
		});

		items.push(
			this.tbTop
		);
	},

	makePagingToolbar: function( items, pos ) {
		pos = pos || 'bottom';
		this.pageSizePickers[pos] = new Ext.form.ComboBox({
			fieldLabel: mw.message ( 'bs-extjs-pageSize' ).plain(),
			labelAlign: 'right',
			autoSelect: true,
			forceSelection: true,
			triggerAction: 'all',
			mode: 'local',
			store: new Ext.data.SimpleStore( {
				fields: ['text', 'value'],
				data: [
					['20', 20],
					['50', 50],
					['100', 100],
					['200', 200],
					['500', 500]
				]
			} ),
			value: this.pageSize,
			labelWidth: 120,
			flex: 2,
			valueField: 'value',
			displayField: 'text'
		});

		this.pageSizePickers[pos].on ( 'select', this.onSelectPageSize, this );



		items.push( new Ext.PagingToolbar({
				dock: pos,
				store: this.store,
				displayInfo: true,
				items: [
					this.pageSizePickers[pos]
				]
			})
		);
	},

	makeColumns: function( meta ) {
		var columns = [],
			hiddenColumns = this.queryData.hiddenColumns || [],
			key, def, columnConfig;
		for ( key in meta ) {
			if ( !meta.hasOwnProperty( key ) ) {
				continue;
			}
			if ( key.endsWith( '_link' ) ) {
				continue;
			}
			def = meta[key];
			columnConfig = {
				sortable: def.sortable || false,
				filterable: def.filterable || false,
				dataIndex: key,
				header: def.label || key
			};

			if ( hiddenColumns.indexOf( key ) !== -1 ) {
 				columnConfig.hidden = true;
			}
			if ( key === 'page' ) {
				columnConfig.flex = 1;
				columnConfig.sortable = false;
				if ( this.queryData.mainlabel ) {
					columnConfig.header = this.queryData.mainlabel;
				}
			}
			if ( key === 'page' || def.hasOwnProperty( 'property_type' ) && def.property_type === '_wpg' ) {
				columnConfig.renderer = this.renderPageLink.bind( { key: key } );
			}
			if ( key !== 'page' && def.hasOwnProperty( 'property_type') ) {
				columnConfig.filter = this.makeColumnFilterForDI( def.property_type );
			}

			columns.push( Ext.create( 'Ext.grid.column.Column', columnConfig ) );
		}

		return columns;
	},

	renderPageLink: function( value, meta, record ) {
		if ( record.data.hasOwnProperty( this.key + '_link' ) ) {
			value = record.data[this.key + '_link' ];
			if ( $.isArray( value ) ) {
				return value.join( ', ' );
			}
		}
		return value;
	},

	makeColumnFilterForDI: function( type ) {
		var filter = {
			type: 'string'
		};
		switch ( type) {
			case '_dat':
				filter.type = 'date';
				break;
			case '_num':
			case '_qty':
				filter.type = 'numeric';
				break;
		}
		return filter;
	},

	onSelectPageSize: function (sender, event) {
		if ( this.ignorePageSize ) {
			return;
		}
		var pageSize = sender.getValue();
		this.ignorePageSize = true;
		for ( var pos in this.pageSizePickers ) {
			this.pageSizePickers[pos].setValue( pageSize );
		}
		this.ignorePageSize = false;
		this.store.pageSize = pageSize;
		this.store.proxy.extraParams.limit = pageSize;
		this.store.reload();
	}
} );
