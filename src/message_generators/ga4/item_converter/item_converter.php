<?php
namespace DatalayerGenerator\MessageGenerators\GA4\ItemConverter;
use DatalayerGenerator\MessageGenerators\GA4\EventBase;

class ItemConverter {

	function __construct($options=[]) {
		$this->options = $options;
	}

	function getCommonProductAttributes($product) {
		$categories = $this->getCategoryNames($product);
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
		];
		return $_i;
	}

	function getCategoryNames($object) {
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

	function getUnitPrice($item, EventBase $event) {
		return $event->_getUnitPrice($item);
	}

	function getAmount($item, EventBase $event) {
		return $item->getAmount();
	}

	function toArray($item, $event) {
		$out = $this->getCommonProductAttributes($item);
		$out["quantity"] = $this->getAmount($item, $event);
		$out["price"] = $this->getUnitPrice($item, $event);
		return $out;
	}
}

