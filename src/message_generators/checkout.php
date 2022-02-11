<?php
namespace GoogleTagManager\MessageGenerators;

class Checkout extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "checkout",
			"activity" => "checkout",
		];
		parent::__construct($object, $options);
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		$objDT = \GoogleTagManager::GetProductClass();
		$_activity = $this->getActivity();
		$_objects = $this->getObject();
		is_object($_objects) && ($_objects = [$_objects]);
		$_productsAr = [];
		foreach($_objects as $_o) {
			$_productsAr[] = $objDT->getData($_o);
		}
		$out = [
			"ecommerce" => [
				"${_activity}" => [
					"products" => $_productsAr,
				],
			],
		];
		return $out;
	}

	function getActionField() {
		return [
			"step" => 1,
			"option" => "Visa",
		];
	}
}
