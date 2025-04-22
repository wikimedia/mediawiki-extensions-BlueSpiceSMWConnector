( function ( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.dm' );

	bs.smwconnector.dm.DecisionOverviewNode = function BsSMWConnectorDmDecisionOverviewNode() {
		// Parent constructor
		bs.smwconnector.dm.DecisionOverviewNode.super.apply( this, arguments );
	};

	/* Inheritance */

	OO.inheritClass( bs.smwconnector.dm.DecisionOverviewNode, ve.dm.MWInlineExtensionNode );

	/* Static members */

	bs.smwconnector.dm.DecisionOverviewNode.static.name = 'decisionoverview';

	bs.smwconnector.dm.DecisionOverviewNode.static.tagName = 'decisionoverview';

	// Name of the parser tag
	bs.smwconnector.dm.DecisionOverviewNode.static.extensionName = 'decisionoverview';

	// This tag renders without content
	bs.smwconnector.dm.DecisionOverviewNode.static.childNodeTypes = [];
	bs.smwconnector.dm.DecisionOverviewNode.static.isContent = false;

	/* Registration */

	ve.dm.modelRegistry.register( bs.smwconnector.dm.DecisionOverviewNode );

}( mediaWiki, jQuery, document, blueSpice ) );
