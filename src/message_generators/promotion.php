<?php
namespace GoogleTagManager\MessageGenerators;

class Promotion extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "promoView",
			"activity" => "promoView",
		];
		parent::__construct($object, $options);
	}


	function getActivityData() {
		$objDT = \GoogleTagManager::GetPromotionClass();
		$_objects = $this->getObject();
		is_object($_objects) && ($_objects = [$_objects]);
		$_productsAr = [];
		foreach($_objects as $_o) {
			$_productsAr[] = $objDT->getData($_o);
		}
		return ["promotions" => $_productsAr];
	}
}
