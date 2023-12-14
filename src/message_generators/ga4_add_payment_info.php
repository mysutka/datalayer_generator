<?php
namespace DatalayerGenerator\MessageGenerators;

class GA4AddPaymentInfo extends GA4Event {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "add_payment_info",
		];
		parent::__construct($object, $options);
	}

	public function getEcommerceData() {
		$out = [
			"currency" => null,
			"value" => null,
			"coupon" => null,
			"payment_type" => null,
			"items" => [],
		];
		$_payment_method = $this->getObject()->getPaymentMethod();
		if (is_null($_payment_method)) {
			return null;
		}
#		$out["value"] = $_payment_method->getPriceInclVat();
		$out["payment_type"] = $_payment_method->getLabel();
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


