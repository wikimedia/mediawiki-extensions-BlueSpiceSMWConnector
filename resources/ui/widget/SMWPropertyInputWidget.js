( function () {
	bs.util.registerNamespace( 'bs.swmconnector.ui' );

	bs.swmconnector.ui.SMWPropertyInputWidget = function( config ) {
		config = config || {};
		config.$overlay = true;

		bs.swmconnector.ui.SMWPropertyInputWidget.parent.call( this, $.extend( {}, config, { autocomplete: false } ) );

		OO.ui.mixin.LookupElement.call( this, config );

		this.$element.addClass( 'bs-smwconnector-widget-SMWPropertyInputWidget' );
		this.lookupMenu.$element.addClass( 'bs-smwconnector-widget-SMWPropertyInputWidget-menu' );
	};

	/* Setup */

	OO.inheritClass( bs.swmconnector.ui.SMWPropertyInputWidget, OO.ui.TextInputWidget );
	OO.mixinClass( bs.swmconnector.ui.SMWPropertyInputWidget, OO.ui.mixin.LookupElement );

	/* Methods */

	/**
	 * Handle menu item 'choose' event, updating the text input value to the value of the clicked item.
	 *
	 * @param {OO.ui.MenuOptionWidget} item Selected item
	 */
	bs.swmconnector.ui.SMWPropertyInputWidget.prototype.onLookupMenuChoose = function ( item ) {
		this.closeLookupMenu();
		this.setLookupsDisabled( true );
		this.setValue( item.getData() );
		this.setLookupsDisabled( false );
	};

	/**
	 * @inheritdoc
	 */
	bs.swmconnector.ui.SMWPropertyInputWidget.prototype.focus = function () {
		var retval;

		// Prevent programmatic focus from opening the menu
		this.setLookupsDisabled( true );

		// Parent method
		retval = bs.swmconnector.ui.SMWPropertyInputWidget.parent.prototype.focus.apply( this, arguments );

		this.setLookupsDisabled( false );

		return retval;
	};

	/**
	 * @inheritdoc
	 */
	bs.swmconnector.ui.SMWPropertyInputWidget.prototype.getLookupRequest = function () {
		var inputValue = this.value;

		return new mw.Api().get( {
			action: 'bs-smw-connector-smw-property-store',
			query: inputValue
		} );
	};

	bs.swmconnector.ui.SMWPropertyInputWidget.prototype.getLookupCacheDataFromResponse = function ( response ) {
		return response.results || {};
	};

	bs.swmconnector.ui.SMWPropertyInputWidget.prototype.getLookupMenuOptionsFromData = function ( data ) {
		var i, items = [];

		for ( i = 0; i < data.length; i++ ) {
			var prop = data[i].prop_title.replace( '_', ' ' );
			items.push( new OO.ui.MenuOptionWidget( {
				label: prop,
				data: prop
			} ) );
		}

		return items;
	};

}() );
