<?php
namespace DatalayerGenerator\MessageGenerators\GA4\ItemConverter;
use DatalayerGenerator\MessageGenerators\GA4\EventBase;

class OrderItemConverter extends ItemConverter {

	function getUnitPrice($item, EventBase $event) {
		return $event->_getUnitPrice($item);
	}

	function getAmount($item, EventBase $event) {
		return $item->getAmount();
	}

	function toArray($item, $event) {
		$out = $this->getCommonProductAttributes($item->getProduct());
		$out["quantity"] = $this->getAmount($item, $event);
		$out["price"] = $this->getUnitPrice($item, $event);
		return $out;
	}
}
