Ext.define( 'BS.SMWConnector.grid.AskResultPrinter', {
	extend: 'Ext.grid.Panel',
	requires: [ 'Ext.data.Store' ],

	bsAskData: null, //data parsed with smw.Api.parse

	initComponent: function() {
		this.columns = this.makeColumns();
		this.store = this.makeStore( this.columns );

		this.callParent( arguments );
	},

	makeColumns: function() {
		var prs = this.bsAskData.query.result.printrequests;
		var cols = [{
				text: mw.message('bs-extjs-label-page').plain(),
				dataIndex: '__self', //See "makeStore"
				flex: 1,
				renderer: this.renderTitleColumn
		}];
		for( var i = 0; i < prs.length; i++ ) {
			var pr = prs[i];
			var label = pr.label;
			if( label === '' ) {
				continue;
			}

			var col = {
				text: label,
				dataIndex: 'printouts.' + pr.label,
				flex: 1,
				renderer: this.renderDIColumn,
				sortable: false //For the moment...
			};
			cols.push( col );
		}
		return cols;
	},

	makeStore: function( columns ) {
		var res = this.bsAskData.query.result.results;
		var data = [];
		for( var subject in res ) {
			var rawDataset = res[subject];
			//This is pretty ugly but it enables us to have a minimum of
			//manipulation of the original dataset. Otherwise we'd need to
			//build a new dataset from rawDataset on our own
			rawDataset.__self = rawDataset;
			data.push( rawDataset );
		}

		//Build the store fields from the column definition
		var fields = [];
		for( var i = 0; i < columns.length; i++ ) {
			fields.push( columns[i].dataIndex );
		}

		return new Ext.data.Store({
			fields: fields,
			data: {
				items: data
			},
			proxy: {
				type: 'memory',
				reader: {
					type: 'json',
					root: 'items'
				}
			}
		});
	},

	renderTitleColumn: function( value, col, record ) {
		return mw.html.element(
			'a',
			{
				href: value.getUri()
			},
			value.getPrefixedText()
		);
	},

	renderDIColumn: function( value, col, record ) {
		var items = [];
		for( var prop in value ) {
			var html = this.renderDIProperty( value[prop] );
			if( html ) {
				items.push( '<li>' + html + '</li>');
			}
		}

		return '<ul style="list-style-type:none; margin:0">' + items.join('') + '</ul>';
	},

	renderDIProperty: function( di ) {
		if( di.getString ) {
			console.log(di);
			//TODO: Use 'instanceof' to provide renderers for different types:
			//smw.dataItem.number, smw.dataItem.text, smw.dataItem.wikiPage, smw.dataItem.time, smw.dataItem.uri, ...
			return di.getString();
		}
		if( di instanceof smw.dataItem.time ) {
			return di.getMediaWikiDate(); //TODO: Better format
		}

		return false;
	}
} );