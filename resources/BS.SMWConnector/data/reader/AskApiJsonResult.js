//http://mycreativedesign.co.uk/2013/03/24/creating-custom-proxy-reader-extjs-4/
//http://www.sencha.com/forum/showthread.php?261182-How-to-implement-custom-JSON-reader
//http://stackoverflow.com/questions/8437212/store-with-json-reader-is-not-working

Ext.define('BS.SMWConnector.data.reader.AskApiJsonResult', {
	extend : 'Ext.data.reader.Json',
	alias : 'reader.bs-smwc-askapijsonresult',
	root: 'query',

	read: function(object) {
		var response = Ext.decode(object.responseText);

		var newData = {};
		newData[this.root] = [];

		if( response.query && response.query.results ) {
			for ( var pageName in response.query.results ) {
				var origDataSet = response.query.results[pageName];
				var dataSet = {
					title: {
						fulltext: origDataSet.fulltext,
						fullurl: origDataSet.fullurl
					}
				};

				for( var propertyName in origDataSet.printouts ) {
					//SMW printouts are always arrays! even if there is only
					//one value
					var propertyValues = origDataSet.printouts[propertyName];
					dataSet[propertyName] = propertyValues;
				}

				newData[this.root].push(dataSet);
			}
		}

		return this.callParent([newData]);
	}
	/*,
	//Wird nach this.read aufgerufen. Würde es ermöglichen auf mehr Funktionen
	//der Basisklasse zurückzugreifen asl dies bei this.read der Fall ist
	getResponseData: function(response) {
		console.log('getResponseData');
		var data = this.callParent(arguments);
		console.log(arguments);
		console.log(data);
		//do stuff here

		return data;
	}*/
});