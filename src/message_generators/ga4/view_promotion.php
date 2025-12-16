<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class ViewPromotion extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_promotion",
#			"quantity" => 1,
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		if (!$this->object) {
			return null;
		}
		$out = parent::getEcommerceData();
		$out += [
			"creative_slot" => null,
			"creative_name" => null,
			"promotion_id" => $this->object->getHtmlElementId(),
			"promotion_name" => $this->object->getName(),
			"items" => [],
		];
			$out = array_filter($out, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		return $out;
	}

	function _getUnitPrice($product) {
		$price_finder = $this->options["price_finder"];
		if (is_null($price = $price_finder->getPrice($product))) {
			return null;
		}
		return $price->getUnitPriceInclVat();
	}

	function getAmount($product) {
		return 1;
	}
}


