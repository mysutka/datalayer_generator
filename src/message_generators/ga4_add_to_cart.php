<?php
namespace DatalayerGenerator\MessageGenerators;

class GA4AddToCart extends GA4Event {

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
		$product = $this->getObject();
		if (is_null($product)) {
			return null;
		}
		$card = $product->getCard();
		$_i = $this->getCommonAttributes($product);
		$out["items"][] = array_filter($_i);
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


