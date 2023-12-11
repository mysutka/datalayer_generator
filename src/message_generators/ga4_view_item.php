<?php
namespace DatalayerGenerator\MessageGenerators;

class GA4ViewItem extends GA4Event {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "view_item",
			"quantity" => 1,
		];
		parent::__construct($object, $options);
	}

	public function getEcommerceData() {
		$out = [
			"currency" => null,
			"value" => "",
			"items" => [],
		];
		$_items = [];
		$out["currency"] = (string)$this->getCurrentCurrency();
		foreach($this->items as $idx => $i) {
			$_item = $this->getCommonProductAttributes($i);
			$_item["index"] = $idx;
			$_item["quantity"] = 1;
			$_item["price"] = $this->_getUnitPrice($i);
			$out["items"][] = array_filter($_item);
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


