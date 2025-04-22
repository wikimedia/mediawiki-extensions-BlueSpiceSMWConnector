( function ( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.ui' );

	bs.smwconnector.ui.DataQueryInspectorTool = function BsSMWConnectorUiDataQueryInspectorTool( toolGroup, config ) {
		bs.smwconnector.ui.DataQueryInspectorTool.super.call( this, toolGroup, config );
	};
	OO.inheritClass( bs.smwconnector.ui.DataQueryInspectorTool, ve.ui.FragmentInspectorTool );
	bs.smwconnector.ui.DataQueryInspectorTool.static.name = 'dataqueryTool';
	bs.smwconnector.ui.DataQueryInspectorTool.static.group = 'none';
	bs.smwconnector.ui.DataQueryInspectorTool.static.autoAddToCatchall = false;
	bs.smwconnector.ui.DataQueryInspectorTool.static.icon = 'bluespice';
	bs.smwconnector.ui.DataQueryInspectorTool.static.title = OO.ui.deferMsg( 'bs-smwconnector-dataquery-name' );
	bs.smwconnector.ui.DataQueryInspectorTool.static.modelClasses = [ bs.smwconnector.dm.DataQueryNode ];
	bs.smwconnector.ui.DataQueryInspectorTool.static.commandName = 'dataqueryCommand';
	ve.ui.toolFactory.register( bs.smwconnector.ui.DataQueryInspectorTool );

	ve.ui.commandRegistry.register(
		new ve.ui.Command(
			'dataqueryCommand', 'window', 'open',
			{ args: [ 'dataqueryInspector' ], supportedSelections: [ 'linear' ] }
		)
	);

}( mediaWiki, jQuery, document, blueSpice ) );
