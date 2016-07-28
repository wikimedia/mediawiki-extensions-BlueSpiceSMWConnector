<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class BSDefinitionReader extends SESP\Definition\DefinitionReader {
	public function getDefinitions() {
		return $GLOBALS["bssDefinitions"];
	}
}
