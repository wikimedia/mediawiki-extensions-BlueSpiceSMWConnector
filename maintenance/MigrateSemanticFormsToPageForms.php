<?php

require '../../../maintenance/Maintenance.php';

class MigrateSemanticFormsToPageForms extends Maintenance {

	/** @var string[] */
	protected $propHasDefaultFormVariants = [
		'لديه استمارة افتراضية',
		'Fa servir el formulari per defecte',
		'Hat Standardformular',
		'Έχει προεπιλεγμένη φόρμα',
		'Has default form',
		'Usa el formulario por defecto',
		'فرم پیش‌فرض دارد',
		'Oletuslomake',
		'Utilise le formulaire',
		'משתמש בטופס',
		'Memiliki formulir bawaan',
		'Usa il modulo predefinito',
		'Heeft standaard formulier',
		'Har standardskjema',
		'预设表单',
		'預設表單'
	];

	/** @var true[] */
	protected $pagesToChange = [];

	public function __construct() {
		parent::__construct();

		$this->requireExtension( 'BlueSpiceSMWConnector' );
	}

	public function execute() {
		$this->output( "Searching for pages with [[Has default form::+]] ..." );
		foreach ( $this->propHasDefaultFormVariants as $propName ) {
			$this->executeAsk( $propName );
		}
		$count = count( $this->pagesToChange );
		$this->output( " done.\nFound: " . $count );
		if ( $count === 0 ) {
			$this->output( "\nNothing to do.\n" );
			return;
		}

		$this->output( "\nModifying..." );
		foreach ( $this->pagesToChange as $pageName => $dummyVal ) {
			$this->replacePropertyByParserFunction( $pageName );
		}
		$this->output( "\n... done.\n" );
	}

	/**
	 *
	 * @param string $propName
	 */
	protected function executeAsk( $propName ) {
		global $wgRequest;
		 $api = new ApiMain(
			new DerivativeRequest(
				$wgRequest,
				[
					'action' => 'askargs',
					'conditions' => "$propName::+",
					'parameters' => 'limit=9999'
				]
			)
		);

		$api->execute();
		$data = $api->getResult()->getResultData();

		if ( !empty( $data['query'] ) && !empty( $data['query']['results'] ) ) {
			foreach ( $data['query']['results'] as $pageName => $printout ) {
				$this->pagesToChange[$pageName] = true;
			}
		}
	}

	/**
	 *
	 * @param string $pageName
	 */
	protected function replacePropertyByParserFunction( $pageName ) {
		$this->output( "\n\t$pageName" );
		$wikiPage = Wikipage::factory( Title::newFromText( $pageName ) );
		$content = $wikiPage->getContent();
		if ( $content instanceof WikitextContent === false ) {
			$this->output( "--> No WikiText. Can not modify." );
			return;
		}
		$wikiText = $content->getNativeData();
		$wikiText = preg_replace_callback(
			'#\[\[(.*?)::(.*?)\]\]#',
			function ( $matches ) {
				// Normalize "Has_default form" --> "Has default form"
				$propName = str_replace( '_', ' ', $matches[1] );
				if ( in_array( $propName, $this->propHasDefaultFormVariants ) ) {
					return '{{#default_form:' . $this->extractFormName( $matches[2] ) . '}}';
				}
				return $matches[0];
			},
			$wikiText
		);
		$status = $wikiPage->doEditContent(
			ContentHandler::makeContent( $wikiText, $wikiPage->getTitle() ),
			"SemanticForms to PageForms, done by " . self::class
		);

		if ( $status->isOK() ) {
			$this->output( ' OK.' );
		} else {
			$this->output( 'Error: ' . $status->getMessage()->plain() );
		}
	}

	/**
	 *
	 * @param string $formNameAndMaybeAlias
	 * @return array
	 */
	protected function extractFormName( $formNameAndMaybeAlias ) {
		// This is not actually necessary as
		// {{#default_form:SomeForm|Alias}} (derived from [[Has default from::SomeForm|Alias]])
		// will work the same as
		// {{#default_form:SomeForm}}
		$parts = explode( '|', $formNameAndMaybeAlias );
		return $parts[0];
	}
}

$maintClass = 'MigrateSemanticFormsToPageForms';
require_once RUN_MAINTENANCE_IF_MAIN;
