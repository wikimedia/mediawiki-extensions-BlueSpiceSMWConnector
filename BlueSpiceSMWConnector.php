<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$GLOBALS['wgExtensionCredits']['other'][] = array(
	'path' => __FILE__,
	'name' => 'BSSMWConnector',
	'author' => '[mailto:vogel@hallowelt.com Robert Vogel (Hallo Welt! Medienwerkstatt GmbH)]',
	'descriptionmsg' => 'bs-bssmwconnector-desc',
	'version' => '1.23',
	'url' => 'https://www.hallowelt.com',
	'license-name' => 'GPL-2.0+'
);

$GLOBALS['wgMessagesDirs']['BSSMWConnectorHooks'] = __DIR__ .'/i18n';

$GLOBALS['wgAutoloadClasses']['BSSMWConnectorHooks'] = __DIR__.'/includes/BSSMWConnectorHooks.php';
$GLOBALS['wgAutoloadClasses']['BSSMWCNamespaceManager'] = __DIR__.'/includes/BSSMWCNamespaceManager.php';
$GLOBALS['wgAutoloadClasses']['BSSFVisualEditor'] = __DIR__.'/includes/forminputs/BSSFVisualEditor.php';
$GLOBALS['wgAutoloadClasses']['BSGridResultPrinter'] = __DIR__.'/includes/formats/Grid/BSGridResultPrinter.php';
$GLOBALS['wgAutoloadClasses']['BSPropertyRegistry'] = __DIR__.'/includes/BSPropertyRegistry.php';
$GLOBALS['wgAutoloadClasses']['BSDefinitionReader'] = __DIR__.'/includes/BSDefinitionReader.php';
$GLOBALS['wgAutoloadClasses']['BSExtraPropertyAnnotator'] = __DIR__.'/includes/BSExtraPropertyAnnotator.php';

#$GLOBALS['wgExtensionFunctions'][] = 'BSSMWConnectorHooks::setup';
$GLOBALS['wgHooks']['BeforePageDisplay'][] = 'BSSMWConnectorHooks::onBeforePageDisplay';
$GLOBALS['wgHooks']['sfFormPrinterSetup'][] = 'BSSMWConnectorHooks::onSfFormPrinterSetup';
$GLOBALS['wgHooks']['BSBookshelfNodeTag'][] = 'BSSMWConnectorHooks::onBSBookshelfNodeTag';
$GLOBALS['wgHooks']['BSBookshelfExportTag'][] = 'BSSMWConnectorHooks::onBSBookshelfExportTag';

$GLOBALS['wgHooks']['NamespaceManager::getMetaFields'][] = 'BSSMWCNamespaceManager::onGetMetaFields';
$GLOBALS['wgHooks']['NamespaceManager::getNamespaceData'][] = 'BSSMWCNamespaceManager::onGetNamespaceData';
$GLOBALS['wgHooks']['NamespaceManager::editNamespace'][] = 'BSSMWCNamespaceManager::onEditNamespace';
$GLOBALS['wgHooks']['NamespaceManager::writeNamespaceConfiguration'][] = 'BSSMWCNamespaceManager::onWriteNamespaceConfiguration';

$GLOBALS['smwgResultFormats']['bsgrid'] = 'BSGridResultPrinter';

$aResourceModuleTemplate = array(
	'localBasePath' => $IP.'/extensions/BSSMWConnector/resources',
	'remoteExtPath' => 'BSSMWConnector/resources',
);

$GLOBALS['wgResourceModules']['ext.BSSMWConnector.BookshelfUI'] = array(
	'scripts' => array(
		'ext.BSSMWConnector.BookshelfUI.js'
	),
	'messages' => array()
) + $aResourceModuleTemplate;

$GLOBALS['wgResourceModules']['ext.BSSMWConnector'] = array(
	'scripts' => array(
		'ext.BSSMWConnector.js',
		'ext.BSSMWConnector.smwc.js',
		'ext.BSSMWConnector.util.js'
	),
	'dependencies' => array(
		'ext.bluespice.extjs',
		'ext.smw.api'
	)
) + $aResourceModuleTemplate;

$GLOBALS['wgResourceModules']['ext.BSSMWConnector.SF.VisualEditorField'] = array(
	'scripts' => array(
		'ext.BSSMWConnector.SF.VisualEditorField.js'
	)
) + $aResourceModuleTemplate;

$GLOBALS['wgResourceModules']['ext.srf.bsextjsgrid'] = array(
	'scripts' => array(
		'ext.srf.bsextjsgrid.js'
	),
	'dependencies' => array(
		'ext.BSSMWConnector',
		'ext.smw.api'
	)
) + $aResourceModuleTemplate;

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