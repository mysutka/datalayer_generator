<?php
namespace DatalayerGenerator\MessageGenerators;

class Promotion extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "promoView",
			"activity" => "promoView",
		];
		parent::__construct($object, $options);
	}


	function getActivityData() {
		$_objects = $this->getObject();
		$_productsAr = [];
		foreach($_objects as $_o) {
			$objDT = \DatalayerGenerator\Datatypes\ecDatatype::CreatePromotion($_o);
			$_productsAr[] = $objDT->getData();
		}
		return ["promotions" => $_productsAr];
	}
}
