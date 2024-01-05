<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class AddToCart extends EventBase {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "add_to_cart",
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
		$product = $this->items ? $this->items[0] : null;
		if (is_null($product)) {
			return null;
		}
		foreach($this->items as $idx => $i) {
			$_item = $this->getCommonProductAttributes($product);
			$_item["index"] = $idx;
			$_item["price"] = $this->_getUnitPrice($product);
			$out["items"][] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
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


