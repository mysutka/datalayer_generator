<?php
class Card extends ElementBase {
	function __construct($values=[]) {
		$values += [
			"name" => "dummy Card name",
		];
		parent::__construct($values);
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
