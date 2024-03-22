<?php
class Card extends ElementBase {
	function __construct($values=[]) {
		$values += [
			"name" => "dummy Card name",
			"products" => [],
		];
		parent::__construct($values);
		#error_log(print_r($this->values["brand"], true));
	}

	function getProducts() {
		$out = array_map(function($product_values=[]) {
			$product_values["card"] = $this->values;
			return new Product($product_values);
		}, $this->values["products"]);
		return $out;
	}

	function getCategories() {
		$categories = [
			"Catalog",
			"Books",
			"Human sciences",
			"History",
		];
		return $categories;
	}

	function getBrand() {
		return new Brand($this->values["brand"]);;
	}
}
