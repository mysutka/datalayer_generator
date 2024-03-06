<?php
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\GA4\ItemConverter\BasketItemConverter;

class AddShippingInfo extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "add_shipping_info",
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
			"shipping_tier" => null,
			"items" => [],
		];
		$_delivery_method = $this->getObject()->getDeliveryMethod();
		if (is_null($_delivery_method)) {
			return null;
		}
#		$out["value"] = $_delivery_method->getPriceInclVat();
		$out["shipping_tier"] = $_delivery_method->getLabel();
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


