<?php
/**
 * Base class for generators
 * For enhanced ecommerce.
 */
namespace DatalayerGenerator\MessageGenerators;

/**
 * @todo extend another class as it contains methods useless for GA4.
 */
class GA4Event extends ActionBase {
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
		$basket = $collector->controller->_get_basket();
		return $basket->getCurrency();
	}

	protected function _getCategoryNames($object) {
		if ($object instanceof \Product) {
			$object = $object->getCard();
		}
		$cat = $object->getCategories(array("consider_invisible_categories" => false, "consider_filters" => false, "deduplicate" => true));
		$cat = array_shift($cat);
		if (is_null($cat)) {
			return [];
		}
		$_items = \Category::GetInstancesOnPath($cat->getPath());
		$_items = array_map(function($i) {
			return $i->getName();
		}, $_items);
		return array_values($_items);
	}

	function getCommonProductAttributes($product) {

		$categories = $this->_getCategoryNames($product);
		$card = $product->getCard();
		$brand = $card->getBrand();
		$_i = [
			"item_id" => $product->getCatalogId(),
			"item_name" => $product->getName(),
			"affiliation" => \SystemParameter::ContentOn("app.name.short"),
			"coupon" => "",
			"discount" => "",
			"index" => 0,
			"item_brand" => (string)$brand,
			"item_category" => $categories[0],
			"item_category2" => $categories[1],
			"item_category3" => $categories[2],
			"item_category4" => $categories[3],
			"item_category5" => $categories[4],
			"item_list_id" => "",
			"item_list_name" => "",
			"item_variant" => "",
			"location_id" => "",
			"quantity" => $this->options["quantity"],
		];
		return $_i;
	}
}

