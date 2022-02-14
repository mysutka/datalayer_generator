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

	function getActivityData() {
		$objDT = \GoogleTagManager::GetProductClass();
		$_objects = $this->getObject();
		is_object($_objects) && ($_objects = [$_objects]);
		$_productsAr = [];
		foreach($_objects as $_o) {
			$_productsAr[] = $objDT->getData($_o);
		}
		return ["products" => $_productsAr];
	}

	function getActionField() {
		return [
			"list" => "Example list",
		];
	}
}
