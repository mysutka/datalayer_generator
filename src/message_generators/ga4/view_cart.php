<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class ViewCart extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_cart",
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = [
			"currency" => null,
			"value" => null,
			"items" => [],
		];
#		$out["value"] = $this->getObject()->getItemsPriceInclVat();
		$_items = [];
		$out["currency"] = (string)$this->getCurrentCurrency();
		$out["items"] = $this->itemsToArray();
		return $out;
	}

	protected function _getUnitPrice($basket_item) {
		return $basket_item->getUnitPriceInclVat();
	}

	protected function _getAmount($basket_item) {
		return $basket_item->getAmount();
	}
}


