<?php
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\GA4\ItemConverter\BannerConverter;

class ViewPromotion extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "view_promotion",
#			"quantity" => 1,
		];
		$options += [
			"item_converter" => new BannerConverter($options),
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = parent::getEcommerceData();
		if ($this->object) {
			$out += [
				"creative_slot" => null,
				"creative_name" => null,
				"promotion_id" => $this->object->getHtmlElementId(),
				"promotion_name" => $this->object->getName(),
			];
		}
		$out = array_filter($out, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		return $out;
	}
}
