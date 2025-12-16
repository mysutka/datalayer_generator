<?php
namespace DatalayerGenerator\MessageGenerators\GA4\ItemConverter;

class BannerConverter extends ItemConverterBase {

	function toArray($item, $event) {
		$out = [
			"creative_slot" => null,
			"creative_name" => null,
			"promotion_id" => $item->getHtmlElementId(),
			"promotion_name" => $item->getName(),
		];
		return $out;
	}
}
