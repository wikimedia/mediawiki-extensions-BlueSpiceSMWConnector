Ext.define( 'BS.SMWConnector.store.SMWPropertyStore', {
	extend: 'Ext.data.Store',
	require: [ 'BS.SMWConnector.model.SMWProperty' ],
	proxy: {
		type: 'ajax',
		url: mw.util.wikiScript('api'),
		extraParams: {
			format: 'json',
			action: 'bs-smw-connector-smw-property-store',
			limit: 9999
		},
		reader: {
			type: 'json',
			rootProperty: 'results',
			idProperty: 'id',
			totalProperty: 'total'
		}
	},
	autoLoad: true,
	remoteSort: true,
	sortInfo: {
		field: 'id',
		direction: 'ASC'
	},
	model: 'BS.SMWConnector.model.SMWProperty'
} );