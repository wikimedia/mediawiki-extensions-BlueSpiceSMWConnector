Ext.define( 'BS.SMWConnector.model.TreeItem', {
	extend: 'Ext.data.TreeModel',
	fields: [
		{
			name: 'page', type: 'string'
		},
		{
			name: 'page_link', type: 'string'
		},
		{
			name: 'type', type: 'string'
		},
		{
			name: 'leaf', type: 'boolean'
		},
		{
			name: 'loaded', type: 'boolean', convert: function ( value, record ) {
				return record.get( 'leaf' );
			}
		},
		{
			name: 'id', type: 'string'
		}
	]
} );
