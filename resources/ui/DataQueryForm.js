bs.util.registerNamespace( 'bs.swmconnector.ui' );

bs.swmconnector.ui.DataQueryForm = function ( config ) {
	bs.swmconnector.ui.DataQueryForm.super.call( this, {
		definition: {
			buttons: []
		}
	} );
	this.inspector = config.inspector;
};

OO.inheritClass( bs.swmconnector.ui.DataQueryForm, mw.ext.forms.standalone.Form );

bs.swmconnector.ui.DataQueryForm.prototype.makeItems = function () {
	return [
		{
			type: 'text',
			name: 'categories',
			label: mw.msg( 'bs-smwconnector-dataquery-category-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-category-help' )
		},
		{
			type: 'text',
			name: 'namespaces',
			label: mw.msg( 'bs-smwconnector-dataquery-namespace-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-namespace-help' )
		},
		{
			type: 'dropdown',
			label: mw.msg( 'bs-smwconnector-dataquery-modified-operator-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-modified-operator-help' ),
			name: 'modified',
			options: [
				{ data: '+', label: mw.msg( 'bs-smwconnector-dataquery-modified-operator-all' ) },
				{ data: '>=', label: mw.msg( 'bs-smwconnector-dataquery-modified-operator-since' ) },
				{ data: '<=', label: mw.msg( 'bs-smwconnector-dataquery-modified-operator-before' ) }
			],
			widget_listeners: {
				change: function( value ) {
					if ( value !== '+' ) {
						this.showItem('modifiedDate');
					} else {
						this.hideItem('modifiedDate');
					}
				}
			},
		},
		{
			type: 'date',
			name: 'modifiedDate',
			label: mw.msg( 'bs-smwconnector-dataquery-modified-date-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-modified-date-help' ),
			hidden: true,
			widget_$overlay: true
		},
		{
			type: 'text',
			name: 'printouts',
			label: mw.msg( 'bs-smwconnector-dataquery-printouts-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-printouts-help' )
		},
		{
			type: 'dropdown',
			name: 'format',
			options: [
				{ data: 'ul', label: mw.msg( 'bs-smwconnector-dataquery-format-bulleted-list' ) },
				{ data: 'ol', label: mw.msg( 'bs-smwconnector-dataquery-format-numbered-list' ) }
			],
			value: 'ul'
		},
		{
			type: 'number',
			name: 'count',
			value: 10,
			required: true
		}
	];
};
