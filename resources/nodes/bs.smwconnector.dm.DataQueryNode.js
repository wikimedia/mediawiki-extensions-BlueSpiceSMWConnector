( function( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.dm' );

	bs.smwconnector.dm.DataQueryNode = function BsSMWConnectorDmDataQueryNode() {
		// Parent constructor
		bs.smwconnector.dm.DataQueryNode.super.apply( this, arguments );
	};

	/* Inheritance */

	OO.inheritClass( bs.smwconnector.dm.DataQueryNode, ve.dm.MWInlineExtensionNode );

	/* Static members */

	bs.smwconnector.dm.DataQueryNode.static.name = 'dataquery';

	bs.smwconnector.dm.DataQueryNode.static.tagName = 'dataquery';

	// Name of the parser tag
	bs.smwconnector.dm.DataQueryNode.static.extensionName = 'dataquery';


	// This tag renders without content
	bs.smwconnector.dm.DataQueryNode.static.childNodeTypes = [];
	bs.smwconnector.dm.DataQueryNode.static.isContent = false;


	/* Registration */

	ve.dm.modelRegistry.register( bs.smwconnector.dm.DataQueryNode );

})( mediaWiki, jQuery, document, blueSpice );
