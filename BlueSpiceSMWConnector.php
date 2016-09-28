<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

wfLoadExtension( 'BluespiceSmwConnector' );

$GLOBALS['smwgResultFormats']['bsgrid'] = 'BSGridResultPrinter';

/**
 * Dieser Hack ist in der aktuellen Version von BlueSpiceExtensions
 * (REL1_23/d553bb40cfd6b7564fa930ec5889d0dd7ebf1ab0) notwendig, da
 * Notifications::onBeforeCreateEchoEvent zur "init"-Zeit auf das User-Objekt
 * zugreift und damit die Modifikationen am Language-Objekt durch SMW aushebelt.
 * Dadurch werden sämtlichte SMW Namensräume (z.b. "Attribut") vom System nicht
 * mehr erkannt.
 * Alternativ könnte man die Einbindung der SMW-Erweiterungen die derzeit in
 * der BlueSpiceSemantic.php vorgenommen wird auch in der LocalSettings.php
 * oberhalb der BlueSpiceDistribution durchführen.
 *
 * -- RV
 */
$iEchoKey = array_search( 'EchoHooks::initEchoExtension', $wgExtensionFunctions ) ;
if( $iEchoKey !== false ) {
	unset($wgExtensionFunctions[$iEchoKey]);
	$wgExtensionFunctions[] = 'EchoHooks::initEchoExtension';
}
unset( $iEchoKey );
unset( $aResourceModuleTemplate );

$GLOBALS[ 'wgExtensionFunctions' ][] = function() {
	BSExtraPropertyAnnotator::processProperties();
};
