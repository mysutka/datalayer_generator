<?php
namespace DatalayerGenerator\MessageGenerators;

class Purchase extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
#			"event" => "purchase",
			"activity" => "purchase",
		];
		parent::__construct($object, $options);
	}

	/**
	 * We need some additional values under the ecommerce object, so we override the method and append the value.
	 * In this case it is the 'currencyCode'.
	 */
	function getDataLayerMessage() {
		$out = parent::getDataLayerMessage();
		$out["ecommerce"]["currencyCode"] = "EUR";
		return $out;
	}

	function getObjectItems() {
		return [
			[],
			[],
		];
	}

	function getActivityData() {
		$_objects = $this->getObject();
		$_productsAr = [];
		foreach($this->getObjectItems() as $_o) {
			$objDT = \DatalayerGenerator\Datatypes\EcDatatype::CreateProduct($_o);
			$_productsAr[] = $objDT->getData();
		}
		return ["products" => $_productsAr];
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
