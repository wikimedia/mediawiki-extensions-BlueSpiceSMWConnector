<?php

use SESP\Annotator\ExtraPropertyAnnotator;
use SESP\Cache\MessageCache;
use SESP\AppFactory;
use SMW\SemanticData;
use SMW\DIProperty;
use SMWDataItem as DataItem;

class BSExtraPropertyAnnotator extends ExtraPropertyAnnotator {

	protected $aPropertyRegistry; //!< @var PropertyRegistry
	protected $aHandlers = array(); //!< @var array
	protected $aConfiguration;  //!< @var array
	protected $propertyRegistry; //!< @var BSPropertyRegistry
	protected $definitions;

	/**
	 * @since 1.0
	 *
	 * @param SemanticData $semanticData
	 * @param Factory $factory
	 * @param array $configuration
	 */
	public function __construct( SemanticData $semanticData, AppFactory $appFactory, array $configuration, BSDefinitionReader $aDefinitionReader, BSPropertyRegistry $propertyRegistry ) {
		parent::__construct( $semanticData, $appFactory, $configuration );
		$this->definitions = $aDefinitionReader->getDefinitions();
		$this->propertyRegistry = $propertyRegistry;
	}

	protected function createDataItemById( $externalId, $property ) {
		call_user_func( $this->definitions[ $externalId ][ 'mapping' ], $this->getSemanticData(), $this->getWikiPage(), $property );
	}

	protected function addPropertyValues() {

		$cachedProperties = array();

		foreach ( $this->configuration[ 'sespSpecialProperties' ] as $externalId ) {

			$propertyId = $this->propertyRegistry->getPropertyId( $externalId );

			if ( $this->hasRegisteredPropertyId( $propertyId, $cachedProperties ) ) {
				continue;
			}

			$propertyDI = new DIProperty( $propertyId );

			if ( $this->getSemanticData()->getPropertyValues( $propertyDI ) !== array() ) {
				$cachedProperties[ $propertyId ] = true;
				continue;
			}

			$cachedProperties[ $propertyId ] = true;
			$this->createDataItemById( $externalId, $propertyDI );
		}

		return true;
	}

	/**
	 *
	 * @param String $pPropertyDefinitionFile
	 */
	public static function processProperties() {

		if(!isset($GLOBALS[ "bssDefinitions" ]) || !isset($GLOBALS[ 'bssSpecialProperties' ])){
			return; //definitions missing
		}
		//check if user selected property is defined from extension
		$arrIntersect = array_intersect(
		  array_keys( $GLOBALS[ "bssDefinitions" ] ), $GLOBALS[ 'bssSpecialProperties' ]
		);

		//catch if something is missing
		if ( count( array_keys( $GLOBALS[ "bssDefinitions" ] ) ) == 0 ||
		  !is_array( $GLOBALS[ 'bssSpecialProperties' ] ) ||
		  count( $GLOBALS[ 'bssSpecialProperties' ] ) == 0 ||
		  count($arrIntersect) == 0
		) {
			return; //exit if no special field requested
		}

		$configuration = self::getConfiguration();

		$aDefinitionReader = new BSDefinitionReader( );

		$aPropertyRegistry = new BSPropertyRegistry(
		  $aDefinitionReader, new MessageCache( $GLOBALS[ 'wgContLang' ] )
		);

		$arrHandlers = self::addHandler( $aPropertyRegistry, $configuration, $aDefinitionReader );

		foreach ( $arrHandlers as $name => $callback ) {
			Hooks::register( $name, $callback );
		}
	}

	public static function getConfiguration() {
		return array(
			'wgDisableCounters' => $GLOBALS[ 'wgDisableCounters' ],
			'sespUseAsFixedTables' => $GLOBALS[ 'sespUseAsFixedTables' ],
			'sespSpecialProperties' => $GLOBALS[ 'bssSpecialProperties' ],
			'wgSESPExcludeBots' => $GLOBALS[ 'wgSESPExcludeBots' ],
			'wgShortUrlPrefix' => $GLOBALS[ 'wgShortUrlPrefix' ],
			'sespCacheType' => $GLOBALS[ 'sespCacheType' ]
		);
	}

	public static function addHandler( BSPropertyRegistry $propertyRegistry, $configuration, BSDefinitionReader $aDefinitionReader ) {

		$arrHandlers = array();

		$arrHandlers[ 'SMW::Property::initProperties' ] = function () use( $propertyRegistry ) {
			return $propertyRegistry->registerPropertiesAndAliases();
		};

		$arrHandlers[ 'SMW::SQLStore::updatePropertyTableDefinitions' ] = function ( &$propertyTableDefinitions ) use( $propertyRegistry, $configuration ) {
			return $propertyRegistry->registerAsFixedTables( $propertyTableDefinitions, $configuration );
		};

		$arrHandlers[ 'SMWStore::updateDataBefore' ] = function ( $store, SemanticData $semanticData ) use ( $configuration, $aDefinitionReader, $propertyRegistry ) {

			$appFactory = new AppFactory( $configuration[ 'wgShortUrlPrefix' ] );

			$propertyAnnotator = new BSExtraPropertyAnnotator(
			  $semanticData, $appFactory, $configuration, $aDefinitionReader, $propertyRegistry
			);

			return $propertyAnnotator->addAnnotation();
		};

		return $arrHandlers;
	}

}
