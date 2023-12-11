<?php
namespace DatalayerGenerator\MessageGenerators;

class GA4AddShippingInfo extends GA4Event {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "add_shipping_info",
		];
		parent::__construct($object, $options);
	}

	public function getEcommerceData() {
		$out = [
			"currency" => null,
			"value" => null,
			"coupon" => null,
			"shipping_tier" => null,
			"items" => [],
		];
		$_delivery_method = $this->getObject()->getDeliveryMethod();
		if (is_null($_delivery_method)) {
			return null;
		}
		$out["value"] = $_delivery_method->getPriceInclVat();
		$out["shipping_tier"] = $_delivery_method->getLabel();
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


