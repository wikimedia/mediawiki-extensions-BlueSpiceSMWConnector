bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.AsyncResultTree = function ( cfg ) {
	bs.smwconnector.ui.data.AsyncResultTree.parent.call( this, cfg );
	this.store = cfg.store;
};

OO.inheritClass( bs.smwconnector.ui.data.AsyncResultTree, OOJSPlus.ui.data.Tree );

bs.smwconnector.ui.data.AsyncResultTree.prototype.expandNode = function ( name ) {
	const node = this.getItem( name );
	if ( !node ) {
		return;
	}

	const $element = node.$element.find( '> ul.tree-node-list' );
	if ( $( $element[ 0 ] ).children().length === 0 ) {
		this.store.getSubpages( name ).done( ( result ) => {
			const data = this.prepareData( result );
			const nodes = this.build( data, node.level + 1 );

			for ( const nodeElement in nodes ) {

				if ( !nodes.hasOwnProperty( nodeElement ) ) {
					continue;
				}
				const $li = nodes[ nodeElement ].widget.$element;
				const $labelEl = $( $li ).find( '> div > .oojsplus-data-tree-label' );
				const itemId = $labelEl.attr( 'id' );
				$li.append( this.doDraw( nodes[ nodeElement ].children || {},
					nodes[ nodeElement ].widget, itemId, this.expanded ) );
				$( $element ).append( $li );
				this.reEvaluateParent( nodeElement );
				$( $element ).show();
			}
		} );
	} else {
		$( $element ).show();
	}
};

bs.smwconnector.ui.data.AsyncResultTree.prototype.prepareData = function ( data ) {
	return Object.values( data );
};
