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
			label: mw.msg( 'bs-smwconnector-dataquery-category-input-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-category-help' ),
			labelAlign: 'top'
		},
		{
			type: 'text',
			name: 'namespaces',
			label: mw.msg( 'bs-smwconnector-dataquery-namespace-input-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-namespace-help' ),
			labelAlign: 'top'
		},
		{
			type: 'dropdown',
			label: mw.msg( 'bs-smwconnector-dataquery-modified-input-operator-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-modified-operator-help' ),
			name: 'modified',
			labelAlign: 'top',
			options: [
				{ data: '+', label: mw.msg( 'bs-smwconnector-dataquery-modified-all' ) },
				{ data: '>=', label: mw.msg( 'bs-smwconnector-dataquery-modified-since' ) },
				{ data: '<=', label: mw.msg( 'bs-smwconnector-dataquery-modified-before' ) }
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
			widget_$overlay: true,
			labelAlign: 'top'
		},
		{
			type: 'text',
			name: 'printouts',
			label: mw.msg( 'bs-smwconnector-dataquery-printouts-label' ),
			help: mw.msg( 'bs-smwconnector-dataquery-printouts-help' ),
			labelAlign: 'top'
		},
		{
			type: 'dropdown',
			name: 'format',
			label: mw.msg( 'bs-smwconnector-dataquery-format-input-label' ),
			options: [
				{ data: 'ul', label: mw.msg( 'bs-smwconnector-dataquery-format-bulleted-list' ) },
				{ data: 'ol', label: mw.msg( 'bs-smwconnector-dataquery-format-numbered-list' ) }
			],
			value: 'ul',
			labelAlign: 'top'
		},
		{
			type: 'number',
			name: 'count',
			label: mw.msg( 'bs-smwconnector-dataquery-number-input-label' ),
			value: 10,
			required: true,
			labelAlign: 'top'
		}
	];
};
