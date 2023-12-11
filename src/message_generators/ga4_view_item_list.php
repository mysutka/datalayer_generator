<?php
namespace DatalayerGenerator\MessageGenerators;

class GA4ViewItemList extends GA4Event {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "view_item_list",
		];
		parent::__construct($object, $options);
	}

	public function getEcommerceData() {
		$out = [
			"item_list_name" => null,
			"item_list_id" => null,
			"items" => [],
		];

		foreach($this->items as $idx => $c) {
			foreach($c->getProducts() as $i) {
				$_item = $this->getCommonProductAttributes($i);
				$_item["index"] = $idx;
				$_item["price"] = $this->_getUnitPrice($i);
				$_item["quantity"] = 1;
				$out["items"][] = array_filter($_item);
			}
		}

		return array_filter($out);
	}

	protected function _getUnitPrice($product) {
		$price_finder = $this->options["price_finder"];
		if (is_null($price = $price_finder->getPrice($product))) {
			return null;
		}
		return $price->getUnitPriceInclVat();
	}
}


