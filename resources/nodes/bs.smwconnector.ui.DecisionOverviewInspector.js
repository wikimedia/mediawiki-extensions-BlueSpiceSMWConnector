( function ( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.ui' );
	bs.smwconnector.ui.DecisionOverviewInspector = function BsSMWConnectorUiDecisionOverviewInspector( config ) {
		// Parent constructor
		bs.smwconnector.ui.DecisionOverviewInspector.super.call( this, ve.extendObject( { padded: true }, config ) );
	};

	/* Inheritance */

	OO.inheritClass( bs.smwconnector.ui.DecisionOverviewInspector, ve.ui.MWLiveExtensionInspector );

	/* Static properties */

	bs.smwconnector.ui.DecisionOverviewInspector.static.name = 'decisionOverviewInspector';

	bs.smwconnector.ui.DecisionOverviewInspector.static.title = OO.ui.deferMsg(
		'bs-smwconnector-decision-overview-title'
	);

	bs.smwconnector.ui.DecisionOverviewInspector.static.modelClasses = [ bs.smwconnector.dm.DecisionOverviewNode ];

	bs.smwconnector.ui.DecisionOverviewInspector.static.dir = 'ltr';

	// This tag does not have any content
	bs.smwconnector.ui.DecisionOverviewInspector.static.allowedEmpty = true;
	bs.smwconnector.ui.DecisionOverviewInspector.static.selfCloseEmptyBody = false;

	/* Methods */

	/**
	 * @inheritdoc
	 */
	bs.smwconnector.ui.DecisionOverviewInspector.prototype.initialize = function () {
		// Parent method
		bs.smwconnector.ui.DecisionOverviewInspector.super.prototype.initialize.call( this );
		this.input.$element.remove();
		// Index layout
		this.indexLayout = new OO.ui.PanelLayout( {
			scrollable: false,
			expanded: false,
			padded: true
		} );
		this.indexLayoutPrefix = new OO.ui.PanelLayout( {
			scrollable: false,
			expanded: false,
			padded: true
		} );

		this.categoriesInput = new OO.ui.TextInputWidget();
		this.namespacesInput = new mw.widgets.NamespacesMultiselectWidget( {
			$overlay: true
		} );
		this.prefixInput = new OO.ui.TextInputWidget();

		this.categoriesLayout = new OO.ui.FieldLayout( this.categoriesInput, {
			align: 'left',
			label: mw.message( 'bs-smwconnector-decision-overview-categories-label' ).text(),
			help: mw.message( 'bs-smwconnector-decision-overview-categories-help' ).text()
		} );
		this.namespacesLayout = new OO.ui.FieldLayout( this.namespacesInput, {
			align: 'left',
			label: mw.message( 'bs-smwconnector-decision-overview-namespaces-label' ).text(),
			help: mw.message( 'bs-smwconnector-decision-overview-namespaces-help' ).text()
		} );

		this.prefixLayout = new OO.ui.FieldLayout( this.prefixInput, {
			align: 'left',
			label: mw.message( 'bs-smwconnector-decision-overview-prefix-label' ).text(),
			help: mw.message( 'bs-smwconnector-decision-overview-prefix-help' ).text()
		} );

		this.indexLayout.$element.append(
			this.categoriesLayout.$element,
			this.namespacesLayout.$element
		);
		this.indexLayoutPrefix.$element.append(
			this.prefixLayout.$element
		);
		this.form.$element.append(
			this.indexLayout.$element,
			this.indexLayoutPrefix.$element
		);
	};

	/**
	 * @inheritdoc
	 */
	bs.smwconnector.ui.DecisionOverviewInspector.prototype.getSetupProcess = function ( data ) {
		return bs.smwconnector.ui.DecisionOverviewInspector.super.prototype.getSetupProcess.call( this, data )
			.next( function () {
				const attributes = this.selectedNode.getAttribute( 'mw' ).attrs;

				this.categoriesInput.on( 'change', this.toggleInput.bind( this ) );
				this.namespacesInput.on( 'change', this.toggleInput.bind( this ) );
				this.prefixInput.on( 'change', () => {
					if ( this.prefixInput.getValue() !== '' ) {
						this.categoriesInput.setDisabled( true );
						this.namespacesInput.setDisabled( true );
					} else {
						this.categoriesInput.setDisabled( false );
						this.namespacesInput.setDisabled( false );
					}
				} );

				if ( attributes.categories ) {
					this.categoriesInput.setValue( attributes.categories );
				}
				if ( attributes.namespaces ) {
					const namespaceData = attributes.namespaces.split( '|' );
					this.namespacesInput.clearItems();
					for ( const namespace in namespaceData ) {
						const namespaceItem = this.namespacesInput.menu.findItemFromData(
							namespaceData[ namespace ]
						);
						this.namespacesInput.addTag(
							namespaceItem.getData(), namespaceItem.getLabel()
						);
					}
				} else {
					this.namespacesInput.clearItems();
				}
				if ( attributes.prefix ) {
					this.prefixInput.setValue( attributes.prefix );
				}

				// Get this out of here
				this.actions.setAbilities( { done: true } );
			}, this );
	};

	bs.smwconnector.ui.DecisionOverviewInspector.prototype.toggleInput = function () {
		if ( this.categoriesInput.getValue() !== '' || this.namespacesInput.getValue().length > 0 ) {
			this.prefixInput.setDisabled( true );
		} else {
			this.prefixInput.setDisabled( false );
		}
	};

	bs.smwconnector.ui.DecisionOverviewInspector.prototype.updateMwData = function ( mwData ) {
		// Parent method
		bs.smwconnector.ui.DecisionOverviewInspector.super.prototype.updateMwData.call( this, mwData );

		if ( this.categoriesInput.getValue() !== '' ) {
			mwData.attrs.categories = this.categoriesInput.getValue();
		} else {
			delete ( mwData.attrs.categories );
		}

		if ( this.namespacesInput.getValue() !== '' ) {
			mwData.attrs.namespaces = this.namespacesInput.getValue().join( '|' );
		} else {
			delete ( mwData.attrs.namespaces );
		}

		if ( this.prefixInput.getValue() ) {
			mwData.attrs.prefix = this.prefixInput.getValue();
		} else {
			delete ( mwData.attrs.prefix );
		}

	};

	/**
	 * @inheritdoc
	 */
	bs.smwconnector.ui.DecisionOverviewInspector.prototype.formatGeneratedContentsError = function ( $element ) {
		return $element.text().trim();
	};

	/**
	 * Append the error to the current tab panel.
	 */
	bs.smwconnector.ui.DecisionOverviewInspector.prototype.onTabPanelSet = function () {
		this.indexLayout.getCurrentTabPanel().$element.append( this.generatedContentsError.$element );
	};

	/* Registration */

	ve.ui.windowFactory.register( bs.smwconnector.ui.DecisionOverviewInspector );

}( mediaWiki, jQuery, document, blueSpice ) );
