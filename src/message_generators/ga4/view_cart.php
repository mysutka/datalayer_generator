<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class ViewCart extends EventBase {

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
			$_item = $this->_itemToArray($i);
			$_item["index"] = $idx;
			$out["items"][] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		}
		return array_filter($out);
	}

	protected function _itemToArray($item) {
		$out = $this->getCommonProductAttributes($item);
		$out["quantity"] = $item->getAmount();
		$out["price"] = $item->getUnitPriceInclVat();
		return $out;
	}
}


