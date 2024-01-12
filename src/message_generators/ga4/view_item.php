<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class ViewItem extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_item",
#			"quantity" => 1,
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
		foreach($this->items as $idx => $i) {
			$_item = $this->_itemToArray($i);
			$_item["index"] = $idx;
			$out["items"][] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		}
		return array_filter($out);
	}

	protected function _getUnitPrice($product) {
		$price_finder = $this->options["price_finder"];
		if (is_null($price = $price_finder->getPrice($product))) {
			return null;
		}
		return $price->getUnitPriceInclVat();
	}

	protected function _getAmount($product) {
		return 1;
	}
}


