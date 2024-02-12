<?php
namespace DatalayerGenerator\MessageGenerators\GA4\ItemConverter;

class ProductConverter extends ItemConverter {

	function getCategoryNames($object) {
		if ($object instanceof \Product) {
			$object = $object->getCard();
		}
		$cat = $object->getCategories(array("consider_invisible_categories" => false, "consider_filters" => false, "deduplicate" => true));
		$cat = array_shift($cat);
		if (is_null($cat)) {
			return [];
		}
		$_items = \Category::GetInstancesOnPath($cat->getPath());
		$_items = array_map(function($i) {
			return $i->getName();
		}, $_items);
		return array_values($_items);
	}

	function getCommonProductAttributes($product) {
		$categories = $this->getCategoryNames($product);
		$card = $product->getCard();
		$brand = $card->getBrand();
		$_i = [
			"item_id" => $product->getCatalogId(),
			"item_name" => $product->getName(),
			"affiliation" => \SystemParameter::ContentOn("app.name.short"),
			"coupon" => "",
			"discount" => "",
			"index" => 0,
			"item_brand" => (string)$brand,
			"item_category" => (isset($categories[0]) ? $categories[0] : null) ,
			"item_category2" => (isset($categories[1]) ? $categories[1] : null) ,
			"item_category3" => (isset($categories[2]) ? $categories[2] : null) ,
			"item_category4" => (isset($categories[3]) ? $categories[3] : null) ,
			"item_category5" => (isset($categories[4]) ? $categories[4] : null) ,
			"item_list_id" => "",
			"item_list_name" => "",
			"item_variant" => "",
			"location_id" => "",
		];
		return $_i;
	}
}
