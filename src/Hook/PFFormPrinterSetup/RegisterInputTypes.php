<?php

namespace BlueSpice\SMWConnector\Hook\PFFormPrinterSetup;

use BlueSpice\SMWConnector\Hook\PFFormPrinterSetup;
use BlueSpice\SMWConnector\PageForms\Input\Grid;
use BlueSpice\SMWConnector\PageForms\Input\UserCombo;
use BlueSpice\SMWConnector\PageForms\Input\UserTags;

class RegisterInputTypes extends PFFormPrinterSetup {

	protected function doProcess() {
		//$this->formPrinter->registerInputType( Grid::class );
		$this->formPrinter->registerInputType( UserCombo::class );
		//$this->formPrinter->registerInputType( UserTags::class );

		return true;
	}

}