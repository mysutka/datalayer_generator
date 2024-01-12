<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class ViewItem extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_item",
#			"quantity" => 1,
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = [
			"currency" => null,
			"value" => "",
			"items" => [],
		];
		$_items = [];
		$out["currency"] = (string)$this->getCurrentCurrency();
		$out["items"] = $this->itemsToArray();
		return $out;
	}

	function _getUnitPrice($product) {
		$price_finder = $this->options["price_finder"];
		if (is_null($price = $price_finder->getPrice($product))) {
			return null;
		}
		return $price->getUnitPriceInclVat();
	}

	function getAmount($product) {
		return 1;
	}
}


