<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class PromotionGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "promoView";
	}

	function getEvent() {
		return null;
	}

	/**
	 * @todo use GoogleTagManager::splitObject()
	 */
	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		$objDT = $this->getPromotionClass();
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
					"promotions" => $_productsAr,
				],
			],
			"event" => "promoView"
		];
	}
}
