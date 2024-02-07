<?php
/**
 * Base class for generators
 * For enhanced ecommerce.
 */
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\GA4\ItemConverter\ProductConverter;
use DatalayerGenerator\MessageGenerators\ActionBase;

/**
 * @todo extend another class as it contains methods useless for GA4.
 */
class EventBase extends ActionBase {

	/**
	 * 
	 */
	protected $item_converter = null;

	protected $options = [];

	protected $event_params = [];

	protected $object = null;

	protected $items = [];

	/**
	 * @param array $event_params
	 * - event_name - custom event name; defaults to value recognized by Universal Analytics tag in Google Tag Manager to support automatic Enhance Ecommerce events processing
	 */
	function __construct($object, $event_params=[], $options=[]) {

		$options += [
			"item_converter" => null,
		];

		$event_params += [
			"event_name" => null,
			"quantity" => 1,
			"items" => [],
		];

		if (!isset($options["item_converter"])) {
			$options["item_converter"] = new ProductConverter(["price_finder" => $options["price_finder"]]);
		}
		$this->object = $object;
		$this->item_converter = $options["item_converter"];
		$this->event_params = $event_params;
		$this->options = $options;
		$this->items = $event_params["items"];
	}

	function getObject() {
		return $this->object;
	}

	function getEventName() {
		return $this->event_params["event_name"];
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

	function getCommonProductAttributes(\Product $product) {
		return $this->getItemConverter()->getCommonProductAttributes($product);
	}

	function getItemConverter() {
		return $this->item_converter;
	}

	function setItemConverter($item_converter) {
		$this->item_converter = $item_converter;
	}

	function getEcommerceData() {
		$out = [
			"items" => $this->itemsToArray(),
		];
		return $out;
	}

	protected function itemsToArray() {
		$out = [];
		foreach($this->items as $idx => $i) {
			$_item = $this->_itemToArray($i);
			$_item["index"] = $idx;
			$out[] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		}
		return array_filter($out);
	}

	protected function _itemToArray($item) {
		$out = $this->getItemConverter()->toArray($item, $this);
		return $out;
	}

	/**
	 * Our own array_filter alternative which leaves keys containing '0'.
	 */
	protected function _arrayFilter($value) {
		return (!is_null($value) && ($value!==false) && ($value!==""));
	}
}

