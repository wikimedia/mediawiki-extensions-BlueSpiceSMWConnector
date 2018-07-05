/* 2017-07-03, rvogel: "Workaround für Datepicker in Form:Termin" ERM5255#note-76062 */
if( mw.config.get('wgCanonicalSpecialPageName') === "FormEdit" ) {
	mw.loader.load( 'jquery.ui.datepicker' );
	mw.loader.load( 'ext.pageforms.datepicker' );
}