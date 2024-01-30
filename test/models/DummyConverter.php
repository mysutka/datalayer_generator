<?php

class DummyConverter extends DatalayerGenerator\MessageGenerators\GA4\ItemConverter\ItemConverter {
	function getCommonProductAttributes($product) {

		$categories = $this->getCategoryNames($product);
		$card = $product->getCard();
		$brand = $card->getBrand();
		return [
			"item_id" => $product->getCatalogId(),
			"item_name" => $product->getName(),
			"affiliation" => "Dummies e-shop",
			"coupon" => "",
			"discount" => "",
			"index" => 0,
			"item_brand" => (string)$brand,
			"item_category" => $categories[0],
			"item_category2" => $categories[1],
			"item_category3" => $categories[2],
			"item_category4" => $categories[3],
			"item_category5" => null,
			"item_list_id" => "",
			"item_list_name" => "",
			"item_variant" => "",
			"location_id" => "",
			"quantity" => 1,
		];
	}

	function getCategoryNames($product) {
		return [
			"Catalog",
			"Books",
			"Human sciences",
			"History",
		];
	}

	function getUnitPrice($product, DatalayerGenerator\MessageGenerators\GA4\EventBase $event_base) {
		return 123.45;
	}

	function getAmount($item, $event) {
		return $event->getAmount($item);
	}
}
