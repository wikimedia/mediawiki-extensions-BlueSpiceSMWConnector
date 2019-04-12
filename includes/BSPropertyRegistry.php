<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use SESP\Cache\MessageCache;

class BSPropertyRegistry extends SESP\PropertyRegistry {

	public function __construct( BSDefinitionReader $definitionReader, MessageCache $messageCache ) {
		parent::__construct( $definitionReader, $messageCache );
	}

	/**
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function registerPropertiesAndAliases() {
		$this->registerPropertiesFromList( array_keys( $this->definitions ) );

		return true;
	}

}
