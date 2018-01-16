Ext.define( 'BS.SMWConnector.field.SMWCombo', {
	extend: 'Ext.form.field.ComboBox',
	require: [ 'BS.SMWConnector.store.SMWPropertyStore' ],

	displayField: 'prop_title',
	valueField: 'prop_title',
	allowBlank: false,
	forceSelection: true,
	store: this.store,
	hidden: true,
	fieldLabel: mw.message( 'bs-dlg-choosesmwprop-label' ).plain(),
	labelAlign: 'right',

	constructor: function() {
		this.callParent( arguments );
	},

	initComponent: function() {
		this.makeStore();
		this.callParent( arguments );
	},

	makeStore: function() {
		this.store = Ext.create( 'BS.SMWConnector.store.SMWPropertyStore' );
	}
} );