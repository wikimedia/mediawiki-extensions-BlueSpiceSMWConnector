//http://skirtlesden.com/articles/custom-proxies

Ext.define('BS.SMWConnector.data.proxy.AskApi', {
	extend : 'Ext.data.proxy.Ajax',
	requires: [
		'BS.SMWConnector.data.reader.AskApiJsonResult'
	],
	alias : 'proxy.bs-smwc-askapi',

	askConditions: [
		'Category:Nothing'
	],
	askPrintouts: [
		'Modification date'
	],
	//TODO: allow javaobjects {'order': 'desc'}
	askParameters: [
		'sort=Modification date',
		'order=desc',
	],

	constructor: function(config) {
		var cfg = config || {};
		this.url = cfg.url || mw.util.wikiScript( 'api' ),
		this.reader = cfg.reader || {
			type: 'bs-smwc-askapijsonresult'
		};

		this.callParent([cfg]);

		this.extraParams = cfg.extraParams || {
			action: 'askargs',
			format: 'json'
		};
	},

	buildUrl: function(request) {
		var url = Ext.String.urlAppend(
			this.url,
			'conditions=' + encodeURIComponent(this.askConditions.join('|')) +
			'&printouts=' + encodeURIComponent(this.askPrintouts.join('|')) +
			'&parameters='+ encodeURIComponent(
				'|limit=' + request.params.limit +
				'|offset=' + request.params.start +
				'|' + this.askParameters.join('|'))
		);

		//Remove non-api conformant querystring parameters
		delete(request.params.page);
		delete(request.params.limit);
		delete(request.params.start);

		return url;
	}/*,
	//Maybe this interface is better than "this.buildUrl"
	buildRequest: function(operation) {
		var request = this.callParent(arguments);
		console.log('buildRequest:operation');
		console.log(operation);
		console.log('buildRequest:request');
		console.log(request);

		return request;
	}
	*/
});