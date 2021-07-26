Ext.define( 'BS.SMWConnector.grid.GridInputField', {
	extend: 'Ext.grid.Panel',

	$formField: null,
	templateName: '',
	colDef: [],

	initComponent: function() {
		this.dockedItems = this.makeDockedItems();
		this.columns = this.makeColumns();
		this.store = this.makeStore();
		this.plugins = this.makePlugins();

		this.on( 'validateedit', this.onValidateEdit, this );
		this.on( 'edit', this.onEdit, this );

		return this.callParent( arguments );
	},

	makeDockedItems: function() {
		this.btnAdd = new Ext.Button( {
			iconCls: 'bs-icon-plus-circle contructive',
			handler: this.onAddClick,
			scope: this
		} );

		return [
			new Ext.toolbar.Toolbar({
				dock: 'bottom',
				items: [
					'->',
					this.btnAdd
				]
			})
		];
	},

	makeColumns: function() {
		var actionColumn = this.makeActionColumn();

		for( var i = 0; i < this.colDef.length; i++ ) {
			if( !this.colDef[i].editor ) {
				continue;
			}
			//give a name to grid editors or there will be real input fields
			//named after the dataIndex in the dom, that will be posted and may
			//override other inputs from page forms
			this.colDef[i].editor.name = 'dummy_' +
				this.colDef[i].dataIndex || '';
		}
		this.colDef.push( actionColumn );
		return this.colDef;
	},

	makeActionColumn: function() {
		return {
			xtype: 'actioncolumn',
			width: 30,
			sortable: false,
			menuDisabled: true,
			items: [{
				iconCls: 'bs-extjs-actioncolumn-icon bs-icon-cross destructive',
				glyph: true, //Needed to have the "BS.override.grid.column.Action" render an <span> instead of an <img>,
				scope: this,
				handler: this.onRemoveClick
			}]
		};
	},

	makeStore: function() {
		var fields = [];
		for( var i = 0; i < this.colDef.length; i++ ) {
			if( !this.colDef[i].dataIndex ) {
				continue;
			}
			fields.push( this.colDef[i].dataIndex );
		}
		return new Ext.data.JsonStore({
			fields: fields,
			data: this.makeStoreDataFromFieldValue()
		} );
	},

	makePlugins: function() {
		this.cellEditing = new Ext.grid.plugin.CellEditing( {
			clicksToEdit: 1
		});

		return [
			this.cellEditing
		];
	},

	onValidateEdit: function ( editor, e, eOpts ) {
		if( !e.value || !e.value.indexOf ) {
			return true;
		}

		if( e.value.indexOf( '|' ) !== -1 ) {
			return false;
		}

		if( e.value.indexOf( '{{' ) !== -1 ) {
			return false;
		}

		if( e.value.indexOf( '}}' ) !== -1 ) {
			return false;
		}

		return true;
	},

	onEdit: function( editor, e ) {
		this.setFieldValueFromStoreData();
	},

	makeStoreDataFromFieldValue: function() {
		var wikiText = this.$formField.val();
		var records = [];


		wikiText.replace( /{{(.*?)}}/g, function( fullMatch, inner ) {
			var record = {};
			var kvPairs = inner.split( '|' );
			kvPairs.shift();
			for ( var i = 0; i < kvPairs.length; i++ ) {
				var kvPair = kvPairs[i].split( '=' );
				record[kvPair[0]] = kvPair[1];
			}
			records.push( record );
		} );

		return records;
	},

	setFieldValueFromStoreData: function() {
		var records = this.store.getRange();
		var wikiText = '';
		var wikiTextTemplateCall = '';

		for( var i = 0; i < records.length; i++ ) {
			var record = records[i];
			wikiTextTemplateCall = '{{' + this.templateName;

			for( var fieldName in record.getData() ) {
				// Skip dynamically generated store IDs. No need to persist them into the wikitext
				if ( fieldName === record.getIdProperty() ) {
					continue;
				}
				var val = '';
				if( typeof record.get( fieldName ) !== "undefined" ) {
					val = record.get( fieldName );
				}
				wikiTextTemplateCall += '|' + fieldName + '=' + val;
			}
			wikiTextTemplateCall += '}}';
			wikiText += "\n" + wikiTextTemplateCall;
		}
		this.$formField.val( wikiText );
	},

	onAddClick: function() {
		this.getStore().add( {
			id: this.getStore().getCount() + 1
		} );
		this.cellEditing.startEditByPosition({
			row: this.getStore().getCount() - 1,
			column: 0
		});
	},

	onRemoveClick: function( grid, rowIndex){
		this.getStore().removeAt( rowIndex );
		this.setFieldValueFromStoreData();
	}
} );
