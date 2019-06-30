<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class ProductDetailGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "detail";
	}

	function getEvent() {
		return null;
	}

	/**
	 * @todo actionField
	 */
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
		return
			[
				"ecommerce" => [
					"${_activity}" => [
						"products" => $_productsAr,
						"actionField" => [ "list" => "Example list"],
					],
				],
				"event" => "detail",
			];
	}
}
