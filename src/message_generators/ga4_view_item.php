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
		$card = $this->getObject();
		$product = $card->getFirstProduct();
		if (is_null($product)) {
			return null;
		}
		$brand = $card->getBrand();
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


