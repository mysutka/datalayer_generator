<?php
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\GA4\ItemConverter\BasketItemConverter;

class ViewCart extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_cart",
		];
		$options += [
			"item_converter" => new BasketItemConverter($options),
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = parent::getEcommerceData();
		$out += [
			"currency" => null,
			"value" => null,
			"items" => [],
		];
#		$out["value"] = $this->getObject()->getItemsPriceInclVat();
		$out["currency"] = (string)$this->getCurrentCurrency();
		return $out;
	}

	function _getUnitPrice($basket_item) {
		return $basket_item->getUnitPriceInclVat();
	}

	function getAmount($basket_item) {
		return $basket_item->getAmount();
	}
}


