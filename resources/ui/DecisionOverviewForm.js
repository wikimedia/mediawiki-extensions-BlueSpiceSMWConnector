bs.util.registerNamespace( 'bs.swmconnector.ui' );

bs.swmconnector.ui.DecisionOverviewForm = function ( config ) {
	bs.swmconnector.ui.DecisionOverviewForm.super.call( this, {
		definition: {
			buttons: []
		}
	} );
	this.inspector = config.inspector;
};

OO.inheritClass( bs.swmconnector.ui.DecisionOverviewForm, mw.ext.forms.standalone.Form );

bs.swmconnector.ui.DecisionOverviewForm.prototype.makeItems = function () {
	return [
		{
			type: 'text',
			name: 'categories',
			label: mw.msg( 'bs-smwconnector-decision-overview-categories-label' ),
			help: mw.msg( 'bs-smwconnector-decision-overview-categories-help' ),
		},
		{
			type: 'text',
			name: 'namespaces',
			label: mw.msg( 'bs-smwconnector-decision-overview-namespaces-label' ),
			help: mw.msg( 'bs-smwconnector-decision-overview-namespaces-help' ),
			widget_$overlay: true
		},
		{
			type: 'text',
			name: 'prefix',
			label: mw.msg( 'bs-smwconnector-decision-overview-prefix-label' ),
			help: mw.msg( 'bs-smwconnector-decision-overview-prefix-help' ),
			widget_listeners: {
				change: function( value ) {
					this.getItem( 'categories' ).setDisabled( !!value );
					this.getItem( 'namespaces' ).setDisabled( !!value );
				}
			}
		}
	];
};
