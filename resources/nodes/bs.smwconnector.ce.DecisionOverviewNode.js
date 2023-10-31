( function( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.ce' );

	bs.smwconnector.ce.DecisionOverviewNode = function () {
		// Parent constructor
		bs.smwconnector.ce.DecisionOverviewNode.super.apply( this, arguments );
	};

	/* Inheritance */

	OO.inheritClass( bs.smwconnector.ce.DecisionOverviewNode, ve.ce.MWInlineExtensionNode );

	/* Static properties */

	bs.smwconnector.ce.DecisionOverviewNode.static.name = 'decisionoverview';

	bs.smwconnector.ce.DecisionOverviewNode.static.primaryCommandName = 'decisionoverview';

	bs.smwconnector.ce.DecisionOverviewNode.static.rendersEmpty = true;

	/* Registration */
	ve.ce.nodeFactory.register( bs.smwconnector.ce.DecisionOverviewNode );

})( mediaWiki, jQuery, document, blueSpice );
