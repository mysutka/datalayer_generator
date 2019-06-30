<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class CheckoutGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "checkout";
	}

	function getEvent() {
		return null;
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		$objDT = $this->getProductClass();
		$_activity = $this->getActivity();
		$_objects = $this->getObject();
		is_object($_objects) && ($_objects = [$_objects]);
		$_productsAr = [];
		foreach($_objects as $_o) {
			$_productsAr[] = $objDT->getData($_o);
		}
		return [
			"ecommerce" => [
				"${_activity}" => [
					"products" => $_productsAr,
					"actionField" => [
						"step" => 1,
						"option" => "Visa",
					],
				],
			],
			"event" => "checkout"
		];
	}
}
