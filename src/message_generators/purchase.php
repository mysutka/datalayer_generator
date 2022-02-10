<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class Purchase extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "purchase",
		];
		parent::__construct($object, $options);
	}

	function getActivity() {
		return "purchase";
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
					"actionField" => [
						"id" => "order#no",
						"affiliation" => "Example e-shop",
						"revenue" => "102", # items price without vat
						"tax" => "21.5",
						"shipping" => "99"
					],
				],
			],
		];
		if ($_event = $this->getEvent()) {
			$out["event"] = $_event;
		}
		return $out;
	}
}
