<?php

class OrderItem extends ElementBase {

	function __construct($values=[]) {
		$values += [
			"amount" => 1,
		];
		parent::__construct($values);
	}

	function getProduct() {
		return new Product($this->values["product"]);;
	}

	function getUnitPriceInclVat() {}
	function getAmount() {
		return $this->values["amount"];
	}
}