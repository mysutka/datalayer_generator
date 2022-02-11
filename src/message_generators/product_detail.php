<?php
namespace GoogleTagManager\MessageGenerators;

class ProductDetail extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
#			"event" => "detail",
			"activity" => "detail",
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
			"list" => "Example list",
		];
	}
}
