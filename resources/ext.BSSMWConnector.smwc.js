( function (mw, $, bs, undefined ) {

	function getSMWFAutoComplete( cfg ) {
		var config = cfg || {};
		if( !config.property ) {
			config.property = 'Name';
		}
		if( !config.fields ) {
			config.fields = ['title'];
		}

		return new Ext.data.Store({
			autoLoad: true,
			proxy: {
				type: 'ajax',
				url: mw.util.wikiScript('api'),
				extraParams: {
					"action":"sfautocomplete",
					"property": config.property,
					"format": "json"
				},
				reader: {
					type: 'json',
					root: 'sfautocomplete'
				}
			},
			fields: config.fields
		});
	}

	bs.smwc =bs.semanticMediaWikiConnector = {
		getSMWFAutoComplete: getSMWFAutoComplete
	};

} (mediaWiki, jQuery, blueSpice) );