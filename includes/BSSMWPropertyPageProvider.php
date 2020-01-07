<?php

class BSSMWPropertyPageProvider implements BlueSpice\BookshelfUI\MassAdd\IHandler {
	/**
	 * Property based on which pages
	 * should be retieved
	 *
	 * @var string
	 */
	protected $root;

	/**
	 *
	 * @return array
	 */
	public function getData() {
		$store = \SMW\StoreFactory::getStore();
		$property = new \SMW\DIProperty( $this->root );
		$values = $store->getAllPropertySubjects( $property );

		$pagesRes = [];
		foreach ( $values as $value ) {
			$title = \Title::newFromText( $value->getDBkey(), $value->getNamespace() );
			if ( !( $title instanceof Title ) ) {
				continue;
			}
			$pagesRes[] = [
				'page_id' => $title->getArticleId(),
				'page_title' => $title->getText(),
				'page_namespace' => $title->getNamespace(),
				'prefixed_text' => $title->getPrefixedText()
			];
		}
		return $pagesRes;
	}

	/**
	 * Returns an instance of this handler
	 *
	 * @param string $root Property to retrieve pages by
	 * @return \self
	 */
	public static function factory( $root ) {
		return new self( $root );
	}

	/**
	 *
	 * @param string $root
	 */
	protected function __construct( $root ) {
		$this->root = $root;
	}

}
