<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class AddShippingInfo extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "add_shipping_info",
		];
		$options["item_converter"] = new BasketItemConverter();
		parent::__construct($object, $event_params, $options);
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
#		$out["value"] = $_delivery_method->getPriceInclVat();
		$out["shipping_tier"] = $_delivery_method->getLabel();
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


