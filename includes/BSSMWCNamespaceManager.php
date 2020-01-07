<?php

class BSSMWCNamespaceManager {

	/**
	 *
	 * @param array &$aMetaFields
	 * @return bool
	 */
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

	/**
	 *
	 * @param array &$aResults
	 * @return bool
	 */
	public static function onGetNamespaceData( &$aResults ) {
		$iResults = count( $aResults );
		for ( $i = 0; $i < $iResults; $i++ ) {
			$aResults[ $i ][ 'smw' ] =
				isset( $GLOBALS['smwgNamespacesWithSemanticLinks'][$aResults[ $i ][ 'id' ]] )
				? $GLOBALS['smwgNamespacesWithSemanticLinks'][$aResults[ $i ][ 'id' ]]
				: false;
		}
		return true;
	}

	/**
	 *
	 * @param array &$aNamespaceDefinitions
	 * @param int &$iNS
	 * @param array $aAdditionalSettings
	 * @param bool $bUseInternalDefaults
	 * @return bool
	 */
	public static function onEditNamespace( &$aNamespaceDefinitions, &$iNS,
		$aAdditionalSettings, $bUseInternalDefaults = false ) {
		if ( !$bUseInternalDefaults && isset( $aAdditionalSettings['smw'] ) ) {
			$aNamespaceDefinitions[$iNS][ 'smw' ] = $aAdditionalSettings['smw'];
		} else {
			$aNamespaceDefinitions[$iNS][ 'smw' ] = false;
		}
		return true;
	}

	/**
	 *
	 * @param string &$sSaveContent
	 * @param string $sConstName
	 * @param int $iNsID
	 * @param array $aDefinition
	 * @return bool
	 */
	public static function onWriteNamespaceConfiguration( &$sSaveContent, $sConstName, $iNsID,
		$aDefinition ) {
		if ( $iNsID === null ) {
			return true;
		}
		$bCurrentlyActivated = isset( $GLOBALS['smwgNamespacesWithSemanticLinks'][ $iNsID ] )
			? $GLOBALS['smwgNamespacesWithSemanticLinks'][ $iNsID ]
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
