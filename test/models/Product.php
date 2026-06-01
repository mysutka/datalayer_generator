<?php
class Product extends ElementBase {

	function __construct($values=[]) {
		$values += [
			"catalog_id" => "product-no-1",
			"name" => "dummy name",
			"card" => [
				"brand" => [
					"name" => "Brandy",
				],
			],
		];
		parent::__construct($values);
	}

	function getCatalogId() {
		return $this->values["catalog_id"];
	}
	function getName() {
		return $this->values["name"];
	}
	function getCard() {
		return new Card($this->values["card"]);;
	}
}

