<?php

namespace BlueSpice\SMWConnector;

use BlueSpice\Discovery\ITitleActionPrimaryActionModifier;

class TitleActionModifier implements ITitleActionPrimaryActionModifier {

	/**
	 * @param array $ids
	 * @param string $primaryId
	 * @return string
	 */
	public function getActionId( array $ids, string $primaryId ): string {
		if ( in_array( 'ca-formedit', $ids ) ) {
			return 'ca-formedit';
		}

		return $primaryId;
	}
}
