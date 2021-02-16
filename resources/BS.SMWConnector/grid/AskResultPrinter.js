Ext.define( 'BS.SMWConnector.grid.AskResultPrinter', {
	extend: 'Ext.grid.Panel',
	requires: [
		'Ext.data.Store', 'Ext.toolbar.Paging',
		'Ext.ux.data.PagingMemoryProxy'
	],
	frame: true,

	bsAskData: null, //data parsed with smw.Api.parse
	storeFields: [],
	pageSize: 20,

	initComponent: function() {
		this.plugins = this.makePlugins();
		this.columns = this.makeColumns();
		this.store = this.makeStore();
		this.dockedItems = this.makeDockedItems();

		this.callParent( arguments );
	},

	makePlugins: function() {
		return [
			'gridfilters'
		];
	},

	makeColumns: function() {
		var prs = this.bsAskData.query.result.printrequests;
		var cols = [{
				text: mw.message('bs-extjs-label-page').plain(),
				dataIndex: '__subjectDisplayText',
				flex: 2,
				renderer: this.renderTitleColumn,
				filter: {
					type: 'string'
				}
		}];
		this.storeFields = [{
			name: '__id'
		},{
			name: '__subjectDisplayText'
		},{
			name: '__subjectValue'
		},{
			name: '__subjectExists'
		},{
			name: '__subjectFulltext'
		},{
			name: '__subjectFullurl'
		},{
			name: '__subjectNs'
		}];

		for( var i = 0; i < prs.length; i++ ) {
			var pr = prs[i];
			var label = pr.label;
			if( label === '' ) {
				continue;
			}

			var col = this.makeColumnForPrintout( label, pr );
			cols.push( col );

			var valueField = {
				name: 'printout' + pr.label + 'Value'
			};
			this.storeFields.push( valueField );
			var rawValueField = {
				name: 'printout' + pr.label + 'ValueRaw'
			};
			this.storeFields.push( rawValueField );
		}

		return cols;
	},

	makeColumnForPrintout: function( label, pr ) {
		var col = {
			text: label,
			dataIndex: 'printout' + pr.label + 'Value',
			flex: 1,
			filter: this.makeColumnFilterForDI( pr ),
			renderer: this.renderDIColumn
		};

		if( pr.typeid === '_dat' ) {
			col.xtype = 'datecolumn';
			if( pr.format ) {
				col.format = pr.format.replace( /-F\[(.*?)\]/, '$1' );
			}
		}

		return col;
	},

	makeColumnFilterForDI: function( pr ) {
		var filter = {
			type: 'string'
		};
		if( pr.typeid ) {
			switch ( pr.typeid ) {
				case '_dat':
					filter.type = 'date';
					break;
				case '_num':
					filter.type = 'number';
					break;
			}
		}
		return filter;
	},

	makeStore: function() {
		var res = this.bsAskData.query.result.results;
		var data = [];
		for( var subject in res ) {
			var rawDataset = res[subject];
			var row = {
				__id: subject,
				__subjectDisplayText: rawDataset.displaytitle || rawDataset.fulltext,
				__subjectValue: rawDataset.title,
				__subjectExists: rawDataset.exists,
				__subjectFulltext: rawDataset.fulltext,
				__subjectFullurl: rawDataset.fullurl,
				__subjectNs: rawDataset.ns
			};
			var field = {};
			for( var label in rawDataset.printouts ) {
				var printout = rawDataset.printouts[label];
				var value = this.makePrintoutValue( printout );
				row['printout' + label + 'Value'] = value;
				row['printout' + label + 'ValueRaw'] = printout;
			}

			data.push( row );
		}

		return new Ext.data.Store({
			fields: this.storeFields,
			data: {
				items: data
			},
			proxy: {
				type: 'memory',
				reader: {
					type: 'json',
					rootProperty: 'items',
					useSimpleAccessors: true
				},
				enablePaging: true
			},
			pageSize: this.pageSize,
			remoteGroup: true,
			remoteSort: true,
			remoteFilter: true
		});
	},

	makePrintoutValue: function( printoutValues ) {
		var values = [];
		for( var element in printoutValues ) {
			if ( !Ext.isNumeric( element ) ) {
				continue;
			}

			var dataItem = printoutValues[element];
			values.push( this.getDataValueFromDI( dataItem ) );
		}

		/**
		 * This is especially important for values of type date, as otherwise we'd loos the ability
		 * to sort and filter
		 */
		if( values.length === 1 ) {
			return values[0];
		}

		return values.join( ' ' );
	},

	renderTitleColumn: function( value, col, record ) {
		return mw.html.element(
			'a',
			{
				title: record.get( '__subjectFulltext' ),
				class: record.get( '__subjectExists' ) === '1' ? '': 'new',
				href: record.get( '__subjectFullurl' ),
				'bs-data-title': record.get( '__subjectFulltext' )
			},
			record.get( '__subjectDisplayText' )
		);
	},

	renderDIColumn: function( value, metaData, record, rowIndex, colIndex, store, view ) {
		var colDataIndex = metaData.column.dataIndex;
		var di = record.get( colDataIndex + 'Raw' );
		return this.makeDisplayTextFromDI( di, metaData );
	},

	makeDisplayTextFromDI: function( di, metaData ) {
		var values = [];
		for( var element in di ) {
			if ( !Ext.isNumeric( element ) ) {
				continue;
			}

			var dataItem = di[element];
			values.push( this.getHtmlFromDI( dataItem, metaData ) );
		}
		if ( values.length === 0 ) {
			return '-';
		}
		if ( values.length === 1 ) {
			return values[0];
		}
		var items = [];
		for ( var i = 0; i < values.length; i++ ) {
			items.push( '<li>' + values[i] + '</li>' );
		}

		return '<ul style="list-style-type:none; margin:0">' + items.join('') + '</ul>';
	},

	getHtmlFromDI: function( di, metaData ) {
		var type = di.getDIType();
		var html = '';
		switch ( type ) {
			case '_wpg' :
				html = di.getHtml();
				break;
			case '_uri' :
				html = di.getHtml();
				break;
			case '_dat' :
				html = di.getValue();
				if ( metaData.column.format ) {
					html = Ext.Date.format( di.getDate(), metaData.column.format );
				}
				break;
			case '_num' :
				html = di.getNumber();
				break;
			case '_qty' :
				html = di.getValue() + ' ' + di.getUnit();
				break;
			case '_str' :
			case '_txt':
				html = di.getText();
				break;
			case '_geo' :
				html = di.getGeo();
				break;
			default:
				html = di.getValue();
				break;
		}
		return html;
	},

	makeDockedItems: function() {
		var items = [];

		this.makePagingToolbar( items );

		return items;
	},

	makePagingToolbar: function( items ) {
		this.cbPageSize = new Ext.form.ComboBox({
			fieldLabel: mw.message ( 'bs-extjs-pageSize' ).plain(),
			labelAlign: 'right',
			autoSelect: true,
			forceSelection: true,
			triggerAction: 'all',
			mode: 'local',
			store: new Ext.data.SimpleStore({
				fields: ['text', 'value'],
				data: [
					['20', 20],
					['50', 50],
					['100', 100],
					['200', 200],
					['500', 500]
				]
			}),
			value: this.pageSize,
			labelWidth: 120,
			flex: 2,
			valueField: 'value',
			displayField: 'text'
		});

		this.cbPageSize.on ('select', this.onSelectPageSize, this);

		items.push( new Ext.PagingToolbar({
				dock: 'bottom',
				store: this.store,
				displayInfo: true,
				items: [
					this.cbPageSize
				]
			})
		);
	},

	getDataValueFromDI: function( di ) {
		var type = di.getDIType();
		var plainText = '';
		switch ( type ) {
			case '_wpg' :
				plainText = di.getFullText();
				break;
			case '_uri' :
				plainText = di.getUri();
				break;
			case '_dat' :
				//This is bad, as it is MWTimestamp format by default
				plainText = di.getDate();
				break;
			case '_num' :
				plainText = di.getNumber();
				break;
			case '_qty' :
				plainText = di.getValue() + ' ' + di.getUnit();
				break;
			case '_str' :
			case '_txt':
				plainText = di.getText();
				break;
			case '_geo' :
				plainText = di.getGeo();
				break;
			default:
				plainText = di.getValue();
				break;
		}
		return plainText;
	},

	onSelectPageSize: function (sender, event) {
		var pageSize = this.cbPageSize.getValue();
		this.store.pageSize = pageSize;
		this.store.proxy.extraParams.limit = pageSize;
		this.store.reload();
	}
} );
