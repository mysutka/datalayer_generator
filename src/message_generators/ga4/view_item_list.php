<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class ViewItemList extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_item_list",
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = [
			"item_list_name" => null,
			"item_list_id" => null,
			"items" => [],
		];

		foreach($this->items as $idx => $c) {
			foreach($c->getProducts() as $item) {
				$_item = $this->_itemToArray($i);
				$_item["index"] = $idx;
				$out["items"][] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
			}
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


