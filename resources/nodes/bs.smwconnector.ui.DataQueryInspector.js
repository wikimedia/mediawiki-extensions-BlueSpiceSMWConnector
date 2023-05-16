( function( mw, $, d, bs ) {
	bs.util.registerNamespace( 'bs.smwconnector.ui' );
	bs.smwconnector.ui.DataQueryInspector = function BsSMWConnectorUiDataQueryInspector( config ) {
		// Parent constructor
		bs.smwconnector.ui.DataQueryInspector.super.call( this, ve.extendObject( { padded: true }, config ) );
	};

	/* Inheritance */

	OO.inheritClass( bs.smwconnector.ui.DataQueryInspector, ve.ui.MWLiveExtensionInspector );

	/* Static properties */

	bs.smwconnector.ui.DataQueryInspector.static.name = 'dataqueryInspector';

	bs.smwconnector.ui.DataQueryInspector.static.title = OO.ui.deferMsg(
		'bs-smwconnector-dataquery-name'
	);

	bs.smwconnector.ui.DataQueryInspector.static.modelClasses = [ bs.smwconnector.dm.DataQueryNode ];

	bs.smwconnector.ui.DataQueryInspector.static.dir = 'ltr';

	//This tag does not have any content
	bs.smwconnector.ui.DataQueryInspector.static.allowedEmpty = true;
	bs.smwconnector.ui.DataQueryInspector.static.selfCloseEmptyBody = true;

	/* Methods */

	/**
	 * @inheritdoc
	 */
	bs.smwconnector.ui.DataQueryInspector.prototype.initialize = function () {
		// Parent method
		bs.smwconnector.ui.DataQueryInspector.super.prototype.initialize.call( this );
		this.input.$element.remove();
		// Index layout
		this.indexLayout = new OO.ui.PanelLayout( {
			scrollable: false,
			expanded: false
		} );

		this.categoriesInput = new OO.ui.TextInputWidget();
		this.namespacesInput = new OO.ui.TextInputWidget();
		this.modifiedOperatorInput = new OO.ui.DropdownWidget( {
			menu: {
				items: [
					new OO.ui.MenuOptionWidget( {
						data: '+',
						label: ve.msg( 'bs-smwconnector-dataquery-modified-operator-all' )
					} ),
					new OO.ui.MenuOptionWidget( {
						data: '>=',
						label: ve.msg( 'bs-smwconnector-dataquery-modified-operator-since' )
					} ),
					new OO.ui.MenuOptionWidget( {
						data: '<=',
						label: ve.msg( 'bs-smwconnector-dataquery-modified-operator-before' )
					} )
				]
			}
		} );
		this.modifiedOperatorInput.getMenu().selectItemByData( '+' );
		this.modifiedDateInput = new mw.widgets.DateInputWidget( {
			$overlay: true
		} );
		this.printoutsInput = new OO.ui.TextInputWidget();
		this.formatInput = new OO.ui.DropdownWidget( {
			menu: {
				items: [
					new OO.ui.MenuOptionWidget( {
						data: 'ul',
						label: ve.msg( 'bs-smwconnector-dataquery-format-bulleted-list' )
					} ),
					new OO.ui.MenuOptionWidget( {
						data: 'ol',
						label: ve.msg( 'bs-smwconnector-dataquery-format-numbered-list' )
					} )
				]
			}
		} );
		this.formatInput.getMenu().selectItemByData( 'ul' );
		this.countInput = new OO.ui.NumberInputWidget( {
			value: 10
		} );

		this.categoriesLayout = new OO.ui.FieldLayout( this.categoriesInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-category-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-category-help' )
		} );
		this.namespacesLayout = new OO.ui.FieldLayout( this.namespacesInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-namespace-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-namespace-help' )
		} );
		this.modifiedOperatorLayout = new OO.ui.FieldLayout( this.modifiedOperatorInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-modified-operator-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-modified-operator-help' )
		} );
		this.modifiedDateLayout = new OO.ui.FieldLayout( this.modifiedDateInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-modified-date-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-modified-date-help' ),
			toggle: false
		} );
		this.printoutsLayout = new OO.ui.FieldLayout( this.printoutsInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-printouts-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-printouts-help' )
		} );
		this.formatLayout = new OO.ui.FieldLayout( this.formatInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-format-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-format-help' )
		} );
		this.countLayout = new OO.ui.FieldLayout( this.countInput, {
			align: 'left',
			label: ve.msg( 'bs-smwconnector-dataquery-count-label' ),
			help: ve.msg( 'bs-smwconnector-dataquery-count-help' )
		} );

		this.indexLayout.$element.append(
			this.categoriesLayout.$element,
			this.namespacesLayout.$element,
			this.modifiedOperatorLayout.$element,
			this.modifiedDateLayout.$element,
			this.printoutsLayout.$element,
			this.formatLayout.$element,
			this.countLayout.$element,
			this.generatedContentsError.$element
		);
		this.form.$element.append(
			this.indexLayout.$element
		);
	};

	/**
	 * @inheritdoc
	 */
	bs.smwconnector.ui.DataQueryInspector.prototype.getSetupProcess = function ( data ) {
		return bs.smwconnector.ui.DataQueryInspector.super.prototype.getSetupProcess.call( this, data )
			.next( function () {
				var attributes = this.selectedNode.getAttribute( 'mw' ).attrs;

				if( attributes.categories ) {
					this.categoriesInput.setValue( attributes.categories );
				}
				if( attributes.namespaces ) {
					this.namespacesInput.setValue( attributes.namespaces );
				}

				var date = '';
				this.modifiedDateLayout.toggle( false );
				if( attributes.modified ) {
					var operator = '';
					for ( var i = 0; i < attributes.modified.length; i++ ) {
						var char = attributes.modified.charAt(i);
						if ( isNaN( char ) ) {
							operator += char;
						} else {
							date = attributes.modified.substring(i);
							break;
						}
					}
					this.modifiedOperatorInput.getMenu().selectItemByData( operator );
					if( operator != '+' ) {
						this.modifiedDateLayout.toggle( true );
						if( date ) {
							this.modifiedDateInput.setValue( date );
						}
					}
				}

				if( attributes.printouts ) {
					this.printoutsInput.setValue( attributes.printouts );
				}
				if( attributes.format ) {
					this.formatInput.getMenu().selectItemByData( attributes.format );
				}
				if( attributes.count ) {
					this.countInput.setValue( attributes.count );
				}
				this.categoriesInput.on( 'change', this.onChangeHandler );
				this.namespacesInput.on( 'change', this.onChangeHandler );

				this.modifiedOperatorInput.getMenu().on( 'select', function() {
					modifiedOperatorValue = this.modifiedOperatorInput.getMenu().findSelectedItem().getData();
					if ( modifiedOperatorValue == '+' ) {
						this.modifiedDateInput.setValue( '' );
						this.modifiedDateLayout.toggle( false );
					} else {
						this.modifiedDateInput.setValue( date );
						this.modifiedDateLayout.toggle( true );
					}
				}.bind( this ) );

				this.modifiedDateInput.on( 'change', this.onChangeHandler );
				this.printoutsInput.on( 'change', this.onChangeHandler );

				this.formatInput.getMenu().on( 'select', function() {
					formatValue = this.formatInput.getMenu().findSelectedItem().getData();
				}.bind( this ) );

				this.countInput.on( 'change', this.onChangeHandler );

				//Get this out of here
				this.actions.setAbilities( { done: true } );
			}, this );
	};

	bs.smwconnector.ui.DataQueryInspector.prototype.updateMwData = function ( mwData ) {
		// Parent method
		bs.smwconnector.ui.DataQueryInspector.super.prototype.updateMwData.call( this, mwData );

		if ( this.categoriesInput.getValue() ) {
			mwData.attrs.categories = this.categoriesInput.getValue();
		} else {
			delete( mwData.attrs.categories );
		}

		if ( this.namespacesInput.getValue() ) {
			mwData.attrs.namespaces = this.namespacesInput.getValue();
		} else {
			delete( mwData.attrs.namespaces );
		}

		mwData.attrs.modified = this.modifiedOperatorInput.getMenu().findSelectedItem().getData();
		if ( this.modifiedDateInput.getValue() ) {
			mwData.attrs.modified += this.modifiedDateInput.getValue();
		}

		if ( this.printoutsInput.getValue() ) {
			mwData.attrs.printouts = this.printoutsInput.getValue();
		} else {
			delete( mwData.attrs.printouts );
		}

		mwData.attrs.format = this.formatInput.getMenu().findSelectedItem().getData();

		if ( this.countInput.getValue() ) {
			mwData.attrs.count = this.countInput.getValue();
		} else {
			delete( mwData.attrs.count );
		}
	};

	/**
	 * @inheritdoc
	 */
	bs.smwconnector.ui.DataQueryInspector.prototype.formatGeneratedContentsError = function ( $element ) {
		return $element.text().trim();
	};

	/**
	 * Append the error to the current tab panel.
	 */
	bs.smwconnector.ui.DataQueryInspector.prototype.onTabPanelSet = function () {
		this.indexLayout.getCurrentTabPanel().$element.append( this.generatedContentsError.$element );
	};

	/* Registration */

	ve.ui.windowFactory.register( bs.smwconnector.ui.DataQueryInspector );

})( mediaWiki, jQuery, document, blueSpice );
