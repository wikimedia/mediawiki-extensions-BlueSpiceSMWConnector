//TODO: Move to BSF?
(function(mw, bs, $, undefined) {

	function _makeWikiTextTemplate( templateName, data ) {
		var template = [];
		template.push('{{'+templateName);
		for( var fieldName in data ) {
			var value = data[fieldName];
			var line = '|'+fieldName+' = '+value;
			template.push(line);
		}
		template.push('}}');
		return template.join("\n");
	};

	bs.util.makeWikiTextTemplate = _makeWikiTextTemplate;

}(mediaWiki, bs, jQuery));
