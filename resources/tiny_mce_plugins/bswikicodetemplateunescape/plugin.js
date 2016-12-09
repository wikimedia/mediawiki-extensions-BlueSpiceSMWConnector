
tinymce.PluginManager.add( 'bswikicodetemplateunescape', function() {
	return {
		init: function( ed, url ) {
			ed.on( 'beforeSetContent', function( e ) {
				e.content = e.content.replace( /^<div><\/div>\n/i, '' ); //Remove potential dummy element from the beginning
				e.content = e.content.replace( '{{!}}', '|' ).trim();
			});
		},

		getInfo: function() {
			var info = {
				longname: 'BlueSpice WikiText Template Unescaper',
				author: 'Hallo Welt! GmbH',
				authorurl: 'http://www.hallowelt.com',
				infourl: 'http://www.hallowelt.com'
			};
			return info;
		}
	};
} );