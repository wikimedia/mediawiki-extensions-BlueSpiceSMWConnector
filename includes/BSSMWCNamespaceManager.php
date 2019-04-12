<?php

class BSSMWCNamespaceManager {

	public static function onGetMetaFields( &$aMetaFields ) {
		$aMetaFields[] = [
			'name' => 'smw',
			'type' => 'boolean',
			'label' => wfMessage( 'bs-bssmwconnector-nmmngr-label-smw' )->plain(),
			'filter' => [
				'type' => 'boolean'
			],
		];
		return true;
	}

	public static function onGetNamespaceData( &$aResults ) {
		global $smwgNamespacesWithSemanticLinks;

		$iResults = count( $aResults );
		for ( $i = 0; $i < $iResults; $i++ ) {
			$aResults[ $i ][ 'smw' ] =
				isset( $smwgNamespacesWithSemanticLinks[  $aResults[ $i ][ 'id' ] ] )
				? $smwgNamespacesWithSemanticLinks[  $aResults[ $i ][ 'id' ] ]
				: false;
		}
		return true;
	}

	public static function onEditNamespace( &$aNamespaceDefinitions, &$iNS, $aAdditionalSettings, $bUseInternalDefaults = false ) {
		if ( !$bUseInternalDefaults && isset( $aAdditionalSettings['smw'] ) ) {
			$aNamespaceDefinitions[$iNS][ 'smw' ] = $aAdditionalSettings['smw'];
		} else {
			$aNamespaceDefinitions[$iNS][ 'smw' ] = false;
		}
		return true;
	}

	public static function onWriteNamespaceConfiguration( &$sSaveContent, $sConstName, $iNsID, $aDefinition ) {
		global $smwgNamespacesWithSemanticLinks;

		if ( $iNsID === null ) {
			return true;
		}
		$bCurrentlyActivated = isset( $smwgNamespacesWithSemanticLinks[ $iNsID ] )
			? $smwgNamespacesWithSemanticLinks[ $iNsID ]
			: false;

		$bExplicitlyDeactivated = false;
		if ( isset( $aDefinition[ 'smw' ] ) && $aDefinition[ 'smw' ] === false ) {
			$bExplicitlyDeactivated = true;
		}

		$bExplicitlyActivated = false;
		if ( isset( $aDefinition[ 'smw' ] ) && $aDefinition[ 'smw' ] === true ) {
			$bExplicitlyActivated = true;
		}

		if ( ( $bCurrentlyActivated && !$bExplicitlyDeactivated ) || $bExplicitlyActivated ) {
			$sSaveContent .= "\$GLOBALS['smwgNamespacesWithSemanticLinks'][{$sConstName}] = true;\n";
		} else {
			$sSaveContent .= "\$GLOBALS['smwgNamespacesWithSemanticLinks'][{$sConstName}] = false;\n";
		}

		return true;
	}
}
