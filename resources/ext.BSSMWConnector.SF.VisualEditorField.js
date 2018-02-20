mw.loader.using( 'ext.bluespice.visualEditor.tinymce' ).done( function() {
	var BsVisualEditorLoaderUsingDeps = mw.config.get('BsVisualEditorLoaderUsingDeps');
	mw.loader.using(BsVisualEditorLoaderUsingDeps).done(function(){
		var sp = mw.config.get('wgScriptPath');
		tinymce.baseURL =
			sp + '/extensions/BlueSpiceExtensions/VisualEditor/resources/tinymce';

		tinymce.init({
			selector: '#pfForm .bs-visualeditor',
			menubar: false,
			statusbar: false,
            language: BsVisualEditorConfigDefault.language,
			//We silently require "InsertLink" ('bslink') extension to also be enabled. This is not nice, but okay for now.
			toolbar1: 'undo redo | bold italic underline strikethrough | bslink unlink | bullist numlist | outdent indent | styleselect forecolor removeformat',
			plugins: 'textcolor colorpicker table lists', //We don't use 'table', we just need it for 'bsactions'
			external_plugins: {
				'bswikicodetemplateunescape': sp + '/extensions/BlueSpiceSMWConnector/resources/tiny_mce_plugins/bswikicodetemplateunescape/plugin.js',
				'bswikicode': '../tiny_mce_plugins/bswikicode/plugin.js',
				'bswikicodetemplateescape': sp + '/extensions/BlueSpiceSMWConnector/resources/tiny_mce_plugins/bswikicodetemplateescape/plugin.js',
				'bsbehaviour': '../tiny_mce_plugins/bsbehaviour/plugin.js',
				'bsactions': '../tiny_mce_plugins/bsactions/plugin.js'
			},
			init_instance_callback: function ( editor ) {
				editor.on( 'Blur', function () {
					tinymce.triggerSave();
				});
			}
		});
	});
});