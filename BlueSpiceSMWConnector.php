<?php

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

$GLOBALS['wgExtensionCredits']['other'][] = array(
	'path' => __FILE__,
	'name' => 'BlueSpiceSMWConnector',
	'author' => '[mailto:vogel@hallowelt.com Robert Vogel (Hallo Welt! GmbH)]',
	'descriptionmsg' => 'bs-smwconnector-desc',
	'version' => '1.23',
	'url' => 'https://www.hallowelt.com'
);

$GLOBALS['wgMessagesDirs']['SMWConnectorHooks'] = __DIR__ .'/i18n';

$GLOBALS['wgAutoloadClasses']['BSSMWConnectorHooks'] = __DIR__.'/includes/BSSMWConnectorHooks.php';
$GLOBALS['wgAutoloadClasses']['BSSMWCNamespaceManager'] = __DIR__.'/includes/BSSMWCNamespaceManager.php';
$GLOBALS['wgAutoloadClasses']['BSSFVisualEditor'] = __DIR__.'/includes/forminputs/BSSFVisualEditor.php';
$GLOBALS['wgAutoloadClasses']['BSGridResultPrinter'] = __DIR__.'/includes/formats/Grid/BSGridResultPrinter.php';
$GLOBALS['wgAutoloadClasses']['BSExtraPropertyAnnotator'] = __DIR__.'/includes/BSExtraPropertyAnnotator.php';

#$GLOBALS['wgExtensionFunctions'][] = 'SMWConnectorHooks::setup';
$GLOBALS['wgHooks']['BeforePageDisplay'][] = 'BSSMWConnectorHooks::onBeforePageDisplay';
$GLOBALS['wgHooks']['sfFormPrinterSetup'][] = 'BSSMWConnectorHooks::onSfFormPrinterSetup';
$GLOBALS['wgHooks']['BSBookshelfNodeTag'][] = 'BSSMWConnectorHooks::onBSBookshelfNodeTag';
$GLOBALS['wgHooks']['BSBookshelfExportTag'][] = 'BSSMWConnectorHooks::onBSBookshelfExportTag';

$GLOBALS['wgHooks']['NamespaceManager::getMetaFields'][] = 'BSSMWCNamespaceManager::onGetMetaFields';
$GLOBALS['wgHooks']['NamespaceManager::getNamespaceData'][] = 'BSSMWCNamespaceManager::onGetNamespaceData';
$GLOBALS['wgHooks']['NamespaceManager::editNamespace'][] = 'BSSMWCNamespaceManager::onEditNamespace';
$GLOBALS['wgHooks']['NamespaceManager::writeNamespaceConfiguration'][] = 'BSSMWCNamespaceManager::onWriteNamespaceConfiguration';

$GLOBALS['smwgResultFormats']['bsgrid'] = 'BSGridResultPrinter';
$GLOBALS[ 'wgExtensionFunctions' ][] = function() {
	BSExtraPropertyAnnotator::processProperties();
};

$aResourceModuleTemplate = array(
	'localBasePath' => $IP.'/extensions/BlueSpiceSMWConnector/resources',
	'remoteExtPath' => 'BlueSpiceSMWConnector/resources',
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

$GLOBALS['wgResourceModules']['ext.BSSMWConnector.SF.FreeTextVisualEditor'] = array(
		'scripts' => array(
			'ext.BSSMWConnector.SF.FreeTextVisualEditor.js'
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

unset( $aResourceModuleTemplate );
