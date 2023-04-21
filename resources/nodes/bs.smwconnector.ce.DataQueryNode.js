( function( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.ce' );

	bs.smwconnector.ce.DataQueryNode = function BsSMWConnectorCeDataQueryNode() {
		// Parent constructor
		bs.smwconnector.ce.DataQueryNode.super.apply( this, arguments );
	};

	/* Inheritance */

	OO.inheritClass( bs.smwconnector.ce.DataQueryNode, ve.ce.MWInlineExtensionNode );

	/* Static properties */

	bs.smwconnector.ce.DataQueryNode.static.name = 'dataquery';

	bs.smwconnector.ce.DataQueryNode.static.primaryCommandName = 'dataquery';

	// If body is empty, tag does not render anything
	bs.smwconnector.ce.DataQueryNode.static.rendersEmpty = true;

	/**
	 * @inheritdoc bs.smwconnector.ce.GeneratedContentNode
	 */
	bs.smwconnector.ce.DataQueryNode.prototype.validateGeneratedContents = function ( $element ) {
		if ( $element.is( 'div' ) && $element.children( '.bsErrorFieldset' ).length > 0 ) {
			return false;
		}
		return true;
	};

	/* Registration */
	ve.ce.nodeFactory.register( bs.smwconnector.ce.DataQueryNode );

})( mediaWiki, jQuery, document, blueSpice );
