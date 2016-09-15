#create bluespicesemantic package for delivery

#!/bin/sh

#extensions for integration in extensions-BlueSpiceSemantic:
# SemanticMediaWiki
# ExternalData
# PageSchemas
# SemanticFormsInputs
# SemanticInternalObjects
# OpenLayers
# SemanticCompoundQueries
# SemanticExtraSpecialProperties
# SemanticForms
# SemanticResultFormats
# SemanticBreadcrumbLinks
# BSSMWConnector

#build zip package with standalone working smw extensions to be extracted in mw-extensions folder

#define vars

DIR_BUILD="/tmp/mediawiki/extensions";
FILE_BUILD="/tmp/BlueSpiceSemantic.zip";

# SemanticMediaWiki
URL_SMW="https://github.com/SemanticMediaWiki/SemanticMediaWiki/releases/download/2.4.1/Semantic_MediaWiki_2.4.1_and_dependencies.zip"
OUT_SMW="$DIR_BUILD/smw_stable.zip"

# ExternalData
URL_ExternalData="https://github.com/wikimedia/mediawiki-extensions-ExternalData/archive/1.8.3.zip"
OUT_ExternalData="$DIR_BUILD/ExternalData_stable.zip"
REAL_PATH_ExternalData="mediawiki-extensions-ExternalData-1.8.3"
FIX_PATH_ExternalData="ExternalData"

# PageSchemas
URL_PageSchemas="https://github.com/wikimedia/mediawiki-extensions-PageSchemas/archive/d60eb53efd0c1c17ba5ab07dbdb9b9e15a165838.zip"
OUT_PageSchemas="$DIR_BUILD/PageSchemas_stable.zip"
REAL_PATH_PageSchemas="mediawiki-extensions-PageSchemas-d60eb53efd0c1c17ba5ab07dbdb9b9e15a165838"
FIX_PATH_PageSchemas="PageSchemas"

# SemanticFormsInputs
URL_SemanticFormsInputs="https://github.com/wikimedia/mediawiki-extensions-SemanticFormsInputs/archive/0.10.1.zip"
OUT_SemanticFormsInputs="$DIR_BUILD/SemanticFormsInputs_stable.zip"
REAL_PATH_SemanticFormsInputs="mediawiki-extensions-SemanticFormsInputs-0.10.1"
FIX_PATH_SemanticFormsInputs="SemanticFormsInputs"

# SemanticInternalObjects
URL_SemanticInternalObjects="https://extdist.wmflabs.org/dist/extensions/SemanticInternalObjects-REL1_27-5f1c282.tar.gz"
OUT_SemanticInternalObjects="$DIR_BUILD/SemanticInternalObjects_stable.tar.gz"

# OpenLayers
URL_OpenLayers="https://github.com/wikimedia/mediawiki-extensions-OpenLayers/archive/REL1_27.zip"
OUT_OpenLayers="$DIR_BUILD/OpenLayers_stable.zip"
REAL_PATH_OpenLayers="mediawiki-extensions-OpenLayers-REL1_27"
FIX_PATH_OpenLayers="OpenLayers"

# SemanticCompoundQueries
URL_SemanticCompoundQueries="https://github.com/SemanticMediaWiki/SemanticCompoundQueries/archive/0.4.1.zip"
OUT_SemanticCompoundQueries="$DIR_BUILD/SemanticCompoundQueries_stable.zip"
REAL_PATH_SemanticCompoundQueries="SemanticCompoundQueries-0.4.1"
FIX_PATH_SemanticCompoundQueries="SemanticCompoundQueries"

# SemanticExtraSpecialProperties
URL_SemanticExtraSpecialProperties="https://github.com/SemanticMediaWiki/SemanticExtraSpecialProperties/archive/master.zip"
OUT_SemanticExtraSpecialProperties="$DIR_BUILD/SemanticExtraSpecialProperties_dev.zip"
REAL_PATH_SemanticExtraSpecialProperties="SemanticExtraSpecialProperties-master"
FIX_PATH_SemanticExtraSpecialProperties="SemanticExtraSpecialProperties"

# SemanticForms
URL_SemanticForms="https://github.com/wikimedia/mediawiki-extensions-SemanticForms/archive/3.6.zip"
OUT_SemanticForms="$DIR_BUILD/SemanticForms_stable.zip"
REAL_PATH_SemanticForms="mediawiki-extensions-SemanticForms-3.6"
FIX_PATH_SemanticForms="SemanticForms"

# SemanticResultFormats
URL_SemanticResultFormats="https://github.com/SemanticMediaWiki/SemanticResultFormats/archive/master.zip"
OUT_SemanticResultFormats="$DIR_BUILD/SemanticResultFormats_dev.zip"
REAL_PATH_SemanticResultFormats="SemanticResultFormats-master"
FIX_PATH_SemanticResultFormats="SemanticResultFormats"

# BSSMWConnector
URL_BSSMWConnector="https://github.com/wikimedia/mediawiki-extensions-BlueSpiceSMWConnector/archive/master.zip"
OUT_BSSMWConnector="$DIR_BUILD/BSSMWConnector_dev.zip"
REAL_PATH_BSSMWConnector="mediawiki-extensions-BlueSpiceSMWConnector-master"
FIX_PATH_BSSMWConnector="BlueSpiceSMWConnector"


#cleanup old files, create build dir
rm $DIR_BUILD $FILE_BUILD -Rf
mkdir $DIR_BUILD -p

#download files
wget $URL_SMW 					-O $OUT_SMW
wget $URL_ExternalData 				-O $OUT_ExternalData
wget $URL_PageSchemas 				-O $OUT_PageSchemas
wget $URL_SemanticFormsInputs 			-O $OUT_SemanticFormsInputs
wget $URL_SemanticInternalObjects 		-O $OUT_SemanticInternalObjects
wget $URL_OpenLayers 				-O $OUT_OpenLayers
wget $URL_SemanticCompoundQueries 		-O $OUT_SemanticCompoundQueries
wget $URL_SemanticExtraSpecialProperties 	-O $OUT_SemanticExtraSpecialProperties
wget $URL_SemanticForms 			-O $OUT_SemanticForms
wget $URL_SemanticResultFormats			-O $OUT_SemanticResultFormats
wget $URL_BSSMWConnector			-O $OUT_BSSMWConnector


#extract to build folder
#pack all together
cd $DIR_BUILD
find -name "*.zip" -exec unzip {} \;
find -name "*.tar.gz" -exec tar xzvf {} \;
rm *.zip *.tar.gz
#fix paths
mv $REAL_PATH_ExternalData 			$FIX_PATH_ExternalData
mv $REAL_PATH_PageSchemas 			$FIX_PATH_PageSchemas
mv $REAL_PATH_SemanticFormsInputs 		$FIX_PATH_SemanticFormsInputs
mv $REAL_PATH_OpenLayers 			$FIX_PATH_OpenLayers
mv $REAL_PATH_SemanticCompoundQueries 		$FIX_PATH_SemanticCompoundQueries
mv $REAL_PATH_SemanticExtraSpecialProperties 	$FIX_PATH_SemanticExtraSpecialProperties
mv $REAL_PATH_SemanticForms 			$FIX_PATH_SemanticForms
mv $REAL_PATH_SemanticResultFormats		$FIX_PATH_SemanticResultFormats
mv $REAL_PATH_BSSMWConnector			$FIX_PATH_BSSMWConnector

#create composer autoload files
cd $FIX_PATH_SemanticExtraSpecialProperties
composer dumpautoload
cd $DIR_BUILD
cd $FIX_PATH_SemanticResultFormats
composer dumpautoload
cd $DIR_BUILD

#create install readme
cat <<EOT >> LocalSettings.BlueSpiceSemantic.php.template
<?php
//Copy LocalSettings.BlueSpiceSemantic.php.template to mediawiki main directory: /LocalSettings.BlueSpiceSemantic.php
//Add to LocalSettings.php to activate all Modules
/*
cp LocalSettings.BlueSpiceSemantic.php.template ../LocalSettings.BlueSpiceSemantic.php
echo 'require_once "LocalSettings.BlueSpiceSemantic.php";' | tee --append ../LocalSettings.php
*/
//finaly append to bottom of LocalSettings.php, set host properly and Activate smw with: enableSemantics( 'localhost' );

require_once "\$IP/extensions/SemanticMediaWiki/SemanticMediaWiki.php";
require_once "\$IP/extensions/ExternalData/ExternalData.php";
require_once "\$IP/extensions/PageSchemas/PageSchemas.php";
require_once "\$IP/extensions/SemanticFormsInputs/SemanticFormsInputs.php";
require_once "\$IP/extensions/SemanticInternalObjects/SemanticInternalObjects.php";
require_once "\$IP/extensions/OpenLayers/OpenLayers.php";
require_once "\$IP/extensions/SemanticCompoundQueries/SemanticCompoundQueries.php";
require_once "\$IP/extensions/SemanticExtraSpecialProperties/vendor/autoload.php";
require_once "\$IP/extensions/SemanticForms/SemanticForms.php";
require_once "\$IP/extensions/SemanticResultFormats/SemanticResultFormats.php";
require_once "\$IP/extensions/BlueSpiceSMWConnector/BlueSpiceSMWConnector.php";

enableSemantics( 'localhost' );

if ( !defined( \$GLOBALS[ 'smwgNamespaceIndex' ] ) ) {
	\$GLOBALS[ 'smwgNamespaceIndex' ] = 700;
}

\$GLOBALS[ 'smwgPageSpecialProperties' ] = array_merge(
	\$GLOBALS[ 'smwgPageSpecialProperties' ],
	array( '_CDAT', '_LEDT', '_NEWP', '_MIME', '_MEDIA' )
);

\$GLOBALS[ 'smwgEnabledEditPageHelp' ] = false;

\$GLOBALS[ 'sespSpecialProperties' ] = array(
	'_EUSER', '_CUSER', '_REVID', '_PAGEID', '_VIEWS', '_NREV', '_TNREV',
	'_SUBP', '_USERREG', '_USEREDITCNT', '_EXIFDATA'
);

\$GLOBALS[ 'bssSpecialProperties' ] = array(
	'_RESPEDITOR'
);

\$GLOBALS[ 'sespUseAsFixedTables' ] = true;

\$GLOBALS[ 'wgSESPExcludeBots' ] = true;

EOT

zip -r $FILE_BUILD *


