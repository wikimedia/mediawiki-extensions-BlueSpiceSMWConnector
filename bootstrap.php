<?php

/**
 * Since most of the Semantic MediaWiki related extensions in this legacy branch activate themselves
 * using composer's 'autoload file'-mechanism, issues may occur when someone tries to install a
 * prebuild distribution (like BlueSpice) from scratch.
 *
 * In this case "Extension:SemanticScribunto" depends on "Extension:Scribunto" being activated. But
 * as "Scribunto" ist not activated automatically this will fail during installation.
 *
 * Therefore we mimic "Scribunto" here
 */
if( defined( 'MEDIAWIKI_INSTALL' ) ) {
	if( !defined( 'CONTENT_MODEL_SCRIBUNTO' ) ) {
		define( 'CONTENT_MODEL_SCRIBUNTO', 'Scribunto' );
	}
}