<?php
namespace DatalayerGenerator\MessageGenerators;

class ProductDetail extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
#			"event" => "detail",
			"activity" => "detail",
		];
		parent::__construct($object, $options);
	}

	function getActivityData() {
		$objDT = \DatalayerGenerator\Datatypes\ecDatatype::CreateProduct($this->getObject());
		$_productsAr = [];
		if ($_data = $objDT->getData()) {
			$_productsAr[] = $_data;
		}
		return ["products" => $_productsAr];
	}

	function getActionField() {
		return [
			"list" => "Example list",
		];
	}
}
