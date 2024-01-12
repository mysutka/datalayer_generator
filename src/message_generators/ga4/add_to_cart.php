<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class AddToCart extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "add_to_cart",
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
		$product = $this->items ? $this->items[0] : null;
		if (is_null($product)) {
			return null;
		}
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
		return $this->event_params["quantity"];
	}
}


