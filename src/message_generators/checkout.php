<?php
namespace DatalayerGenerator\MessageGenerators;

class Checkout extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "checkout",
			"activity" => "checkout",
		];
		parent::__construct($object, $options);
	}

	function getItems() {
		return [
			[],
			[],
		];
#		return $this->getObject()->getItems();
	}

	function getActivityData() {
		$_items = $this->getItems();
		$out = [];
		foreach($_items as $_o) {
			$objDT = \DatalayerGenerator\Datatypes\EcDatatype::CreateProduct($_o);
			$out[] = $objDT->getData();
		}
		return ["products" => $out];
	}

	function getActionField() {
		return [
			"step" => 1,
			"option" => "Visa",
		];
	}
}
