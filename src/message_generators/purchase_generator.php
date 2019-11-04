<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;
use GoogleTagManager\Datatypes\Impression;

class PurchaseGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "purchase";
	}

	function getEvent() {
		return null;
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
		return [
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
			"event" => "purchase"
		];
	}
}
