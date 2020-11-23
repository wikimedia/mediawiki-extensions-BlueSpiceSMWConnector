Ext.define( 'BS.SMWConnector.tree.AsyncResultPrinter', {
	extend: 'Ext.tree.Panel',
	requires: [ 'BS.SMWConnector.model.TreeItem' ],
	useArrows: true,
	rootVisible: false,
	displayField: 'page',
	rootNode: '',
	query: '',
	storeAction: 'bs-smw-connector-tree-ask-store',
	minHeight: 100,

	initComponent: function() {
		this.setLoading( {
			message: mw.message( 'bs-extjs-loading' ).text()
		} );

		this.store = new Ext.data.TreeStore( {
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript( 'api' ),
				reader: {
					type: 'json',
					rootProperty: 'results',
					totalProperty: 'total'
				},
				extraParams: {
					action: this.storeAction,
					format: 'json',
					limit: 999999,
					query: this.query || ''
				}
			},
			defaultRootProperty: 'results',
			model: 'BS.SMWConnector.model.TreeItem',
			root: {
				text: this.rootNode,
				id: this.rootNode,
				expanded: false
			},
			lazyFill: false,
			folderSort: true,
			listeners: {
				load: function() {
					this.setLoading( false );
				}.bind( this )
			}
		} );

		this.columns = [ {
			xtype: 'treecolumn',
			dataIndex: 'page',
			flex: 1,
			renderer: function ( val, meta, record ) {
				return record.get( 'page_link' );
			}
		} ];

		this.callParent( arguments );
	}
});
