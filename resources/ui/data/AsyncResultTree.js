bs.util.registerNamespace( 'bs.smwconnector.ui.data' );

bs.smwconnector.ui.data.AsyncResultTree = function( cfg ) {
	bs.smwconnector.ui.data.AsyncResultTree.parent.call( this, cfg );
	this.store = cfg.store;
};

OO.inheritClass( bs.smwconnector.ui.data.AsyncResultTree, OOJSPlus.ui.data.Tree );

bs.smwconnector.ui.data.AsyncResultTree.prototype.expandNode = function ( name ) {
	var node = this.getItem( name );
	if ( !node ) {
		return;
	}

	var $element = node.$element.find( '> ul.tree-node-list' );
	if ( $( $element[ 0 ] ).children().length === 0 ) {
		this.store.getSubpages( name ).done( function ( result ) {
			var data = this.prepareData( result );
			var nodes = this.build( data, node.level + 1 );

			for ( var nodeElement in nodes ) {
				// eslint-disable-next-line no-prototype-builtins
				if ( !nodes.hasOwnProperty( nodeElement ) ) {
					continue;
				}
				var $li = nodes[ nodeElement ].widget.$element;
				var $labelEl = $( $li ).find( '> div > .oojsplus-data-tree-label' );
				var itemId = $labelEl.attr( 'id' );
				$li.append( this.doDraw( nodes[ nodeElement ].children || {},
					nodes[ nodeElement ].widget, itemId, this.expanded ) );
				$( $element ).append( $li );
				this.reEvaluateParent( nodeElement );
				$( $element ).show();
			}
		}.bind( this ) );
	} else {
		$( $element ).show();
	}
};

bs.smwconnector.ui.data.AsyncResultTree.prototype.prepareData = function ( data ) {
	return Object.values( data );
};