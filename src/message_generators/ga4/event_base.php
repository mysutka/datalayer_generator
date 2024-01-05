<?php
/**
 * Base class for generators
 * For enhanced ecommerce.
 */
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\ActionBase;

/**
 * @todo extend another class as it contains methods useless for GA4.
 */
class EventBase extends ActionBase {

	protected $itemizer;

	/**
	 * @param array $options
	 * - event_name - custom event name; defaults to value recognized by Universal Analytics tag in Google Tag Manager to support automatic Enhance Ecommerce events processing
	 */
	function __construct($object, $options=[]) {

		$options += [
			"event_name" => null,
			"quantity" => 1,
			"items" => [],
		];
		// Not yet in Collector class as it remains platform independent.
		$collector = \DatalayerGenerator\Collector::GetInstance();
		if (!isset($collector->itemizer)) {
			$collector->itemizer = new Itemizer;
		}
		$this->object = $object;
		$this->options = $options;
		$this->items = $options["items"];
	}

	function getObject() {
		return $this->object;
	}

	function getEventName() {
		return $this->options["event_name"];
	}

	function getDataLayerMessage() {
		/* Hlasku vypsat jen kdyz je volana trida DatalayerGenerator nebo kdyz volana trida nema metodu getDataLayerMessage()
		if (get_called_class() == get_class() || !in_array("getDataLayerMessage" , get_class_methods(get_called_class()))) {
		}
		 */
		if (get_called_class() == get_class()) {
			trigger_error(sprintf("%s: do not use this class directly", get_called_class()));
		}

		$out = [
			"event" => $this->getEventName(),
			"ecommerce" => $this->getEcommerceData(),
		];

		return array_filter($out);
	}

	function getCurrentCurrency() {
		$collector = \DatalayerGenerator\Collector::GetInstance();
		if (!isset($collector->controller)) {
			return null;
		}
		$basket = $collector->controller->_get_basket();
		return $basket->getCurrency();
	}

	function getCommonProductAttributes($product) {
		return $this->getItemizer()->getCommonProductAttributes($product);
	}

	function getItemizer() {
		$collector = \DatalayerGenerator\Collector::GetInstance();
		return $collector->itemizer;
	}

	/**
	 * Our own array_filter alternative which leaves keys containing '0'.
	 */
	protected function _arrayFilter($value) {
		return (!is_null($value) && ($value!==false) && ($value!==""));
	}
}

