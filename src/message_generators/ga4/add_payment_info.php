<?php
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\GA4\ItemConverter\BasketItemConverter;

class AddPaymentInfo extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "add_payment_info",
		];
		$options["item_converter"] = new BasketItemConverter($options);
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = parent::getEcommerceData();
		$out += [
			"currency" => null,
			"value" => null,
			"coupon" => null,
			"payment_type" => null,
		];
		$_payment_method = $this->getObject()->getPaymentMethod();
		if (is_null($_payment_method)) {
			return null;
		}
#		$out["value"] = $_payment_method->getPriceInclVat();
		$out["payment_type"] = $_payment_method->getLabel();
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


