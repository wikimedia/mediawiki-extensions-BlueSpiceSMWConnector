mw.loader.using( 'ext.bluespice.visualEditor.tinymce' ).done( function() {
    var BsVisualEditorLoaderUsingDeps = mw.config.get('BsVisualEditorLoaderUsingDeps');
    var BsVisualEditorConfigDefault = mw.config.get('BsVisualEditorConfigDefault');
    mw.loader.using(BsVisualEditorLoaderUsingDeps).done(function(){
        //START UGLY COPY&PASTE CODE FROM BlueSpiceExtensions/VisualEditor/resoures/bluespice.visualEditor.js
        var currentSiteCSS = [];
        //We collect the CSS Links from this document and set them as content_css
        //for TinyMCE

        $('link[rel=stylesheet]').each(function(){
            var cssBaseURL = '';
            var cssUrl = $(this).attr('href');
            //Conditionally make urls absolute to avoid conflict with tinymce.baseURL
            if( cssUrl.indexOf('/') === 0 ) {
                cssBaseURL = mw.config.get('wgServer');
            }
            //need to check, if the stylesheet is already included
            if (jQuery.inArray(cssBaseURL + cssUrl, currentSiteCSS) === -1)
                currentSiteCSS.push( cssBaseURL + cssUrl );
        });
        //END UGLY COPY&PASTE CODE FROM BlueSpiceExtensions/VisualEditor/resoures/bluespice.visualEditor.js

        VisualEditor.setConfig( 'semanticForms', {
            selector: '#pfForm #pf_free_text.bsvisualeditor',
            auto_focus: false,
            content_css: currentSiteCSS.join(','),
            /* We need to remove some tools from the toolbar that do not make sense in a formedit */
            toolbar1: BsVisualEditorConfigDefault.toolbar1.replace( /bswiki|bssave|fullscreen/gi, '' )
        });
        VisualEditor.startEditors();
    });
});