<?php
namespace DatalayerGenerator\MessageGenerators;

class GA4ViewCart extends GA4Event {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "view_cart",
		];
		parent::__construct($object, $options);
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
		foreach($this->items as $idx => $bi) {
			$i = $bi->getProduct();
			$_item = $this->getCommonProductAttributes($i);
			$_item["index"] = $idx;
			$_item["quantity"] = 1;
			$_item["price"] = $bi->getUnitPriceInclVat();
			$out["items"][] = array_filter($_item);
		}
		return array_filter($out);
	}
}


