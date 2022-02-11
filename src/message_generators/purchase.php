<?php
namespace GoogleTagManager\MessageGenerators;

class Purchase extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
#			"event" => "purchase",
			"activity" => "purchase",
		];
		parent::__construct($object, $options);
	}

	/**
	 * @todo actionField
	 */
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
			"id" => "order#no",
			"affiliation" => "Example e-shop",
			"revenue" => "102", # items price without vat
			"tax" => "21.5",
			"shipping" => "99"
		];
	}
}
