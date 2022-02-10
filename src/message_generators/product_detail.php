<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class ProductDetail extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "detail",
		];
		parent::__construct($object, $options);
	}

	function getActivity() {
		return "detail";
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
					"actionField" => [ "list" => "Example list"],
				],
			],
		];
		if ($_event = $this->getEvent()) {
			$out["event"] = $_event;
		}
		return $out;
	}
}
