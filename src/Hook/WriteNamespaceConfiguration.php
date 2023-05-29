<?php

namespace BlueSpice\SMWConnector\Hook;

use BlueSpice\NamespaceManager\Hook\NamespaceManagerBeforePersistSettingsHook;

class WriteNamespaceConfiguration implements NamespaceManagerBeforePersistSettingsHook {

	/**
	 * @inheritDoc
	 */
	public function onNamespaceManagerBeforePersistSettings(
		array &$configuration, int $id, array $definition, array $mwGlobals
	): void {
		$enabledNamespaces = $mwGlobals['smwgNamespacesWithSemanticLinks'] ?? [];
		$currentlyActivated = in_array( $id, $enabledNamespaces );

		$explicitlyDeactivated = false;
		if ( isset( $definition['smw'] ) && $definition['smw'] === false ) {
			$explicitlyDeactivated = true;
		}

		$explicitlyActivated = false;
		if ( isset( $definition['smw'] ) && $definition['smw'] === true ) {
			$explicitlyActivated = true;
		}

		if ( ( $currentlyActivated && !$explicitlyDeactivated ) || $explicitlyActivated ) {
			$configuration['smwgNamespacesWithSemanticLinks'][$id] = true;
		} else {
			$configuration['smwgNamespacesWithSemanticLinks'][$id] = false;
		}
	}
}
