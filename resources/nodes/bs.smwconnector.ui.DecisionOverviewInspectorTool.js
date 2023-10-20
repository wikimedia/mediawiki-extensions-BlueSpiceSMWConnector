( function( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.ui' );

	bs.smwconnector.ui.DecisionOverviewInspectorTool = function BsSMWConnectorUiDecisionOverviewInspectorTool( toolGroup, config ) {
		bs.smwconnector.ui.DecisionOverviewInspectorTool.super.call( this, toolGroup, config );
	};
	OO.inheritClass( bs.smwconnector.ui.DecisionOverviewInspectorTool, ve.ui.FragmentInspectorTool );
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.name = 'decisionOverviewTool';
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.group = 'none';
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.autoAddToCatchall = false;
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.icon = 'bluespice';
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.title = mw.message( 'bs-smwconnector-decision-overview-title' ).text();
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.modelClasses = [ bs.smwconnector.dm.DecisionOverviewNode ];
	bs.smwconnector.ui.DecisionOverviewInspectorTool.static.commandName = 'decisionOverviewCommand';
	ve.ui.toolFactory.register( bs.smwconnector.ui.DecisionOverviewInspectorTool );

	ve.ui.commandRegistry.register(
		new ve.ui.Command(
			'decisionOverviewCommand', 'window', 'open',
			{ args: [ 'decisionOverviewInspector' ], supportedSelections: [ 'linear' ] }
		)
	);

})( mediaWiki, jQuery, document, blueSpice );
