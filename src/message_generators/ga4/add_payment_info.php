<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class AddPaymentInfo extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "add_payment_info",
		];
		$options["item_converter"] = new BasketItemConverter();
		parent::__construct($object, $event_params, $options);
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
		foreach($this->items as $idx => $item) {
			$_item = $this->_itemToArray($item);
			$_item["index"] = $idx;
			$out["items"][] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		}
		return array_filter($out);
	}

	protected function _getUnitPrice($basket_item) {
		return $basket_item->getUnitPriceInclVat();
	}

	protected function _getAmount($basket_item) {
		return $basket_item->getAmount();
	}
}


