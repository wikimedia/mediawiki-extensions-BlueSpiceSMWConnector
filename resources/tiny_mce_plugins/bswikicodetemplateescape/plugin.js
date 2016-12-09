
tinymce.PluginManager.add( 'bswikicodetemplateescape', function() {
	return {
		init: function( ed, url ) {
			ed.on( 'getContent', function( e ) {
				e.content = e.content.replace( '|', '{{!}}' );

				/*
				* A WikiText list at the beginning of a template parameter breaks the parser. Therefore we conditionally
				* prepend a dummy element
				*/
				if( e.content.startsWith( '*' ) || e.content.startsWith( '#' ) ) {
					e.content = "<div></div>\n" + e.content
				}
			});
		},

		getInfo: function() {
			var info = {
				longname: 'BlueSpice WikiText Template Escaper',
				author: 'Hallo Welt! GmbH',
				authorurl: 'http://www.hallowelt.com',
				infourl: 'http://www.hallowelt.com'
			};
			return info;
		}
	};
} );