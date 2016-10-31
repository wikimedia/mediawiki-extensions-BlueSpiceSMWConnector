<?php

$GLOBALS['smwgNamespaceIndex'] = 700;

require_once "$IP/extensions/SemanticMediaWiki/SemanticMediaWiki.php";
enableSemantics( 'localhost' );

array_push( $GLOBALS['smwgPageSpecialProperties'], array(
	'_CDAT', '_LEDT', '_NEWP', '_MIME', '_MEDIA'
) );
$GLOBALS['smwgEnabledEditPageHelp'] = false;

$GLOBALS['sespSpecialProperties'] = array(
	'_EUSER', '_CUSER', '_REVID', '_PAGEID', '_VIEWS', '_NREV', '_TNREV',
	'_SUBP', '_USERREG', '_USEREDITCNT', '_EXIFDATA'
);
$GLOBALS['sespUseAsFixedTables'] = true;
$GLOBALS['wgSESPExcludeBots'] = true;

require_once "$IP/extensions/SemanticResultFormats/vendor/autoload.php"; //Workaround for not being installed via composer
require_once "$IP/extensions/SemanticResultFormats/SemanticResultFormats.php";
require_once "$IP/extensions/SemanticExtraSpecialProperties/vendor/autoload.php"; //Workaround for not being installed via composer
require_once "$IP/extensions/SemanticExtraSpecialProperties/SemanticExtraSpecialProperties.php";
require_once "$IP/extensions/SemanticForms/SemanticForms.php";
require_once "$IP/extensions/SemanticFormsInputs/SemanticFormsInputs.php";
require_once "$IP/extensions/SemanticInternalObjects/SemanticInternalObjects.php";
require_once "$IP/extensions/SemanticCompoundQueries/SemanticCompoundQueries.php";
require_once "$IP/extensions/ExternalData/ExternalData.php";
require_once "$IP/extensions/PageSchemas/PageSchemas.php";

require_once( "$IP/extensions/BlueSpiceSMWConnector/BlueSpiceSMWConnector.php" );
