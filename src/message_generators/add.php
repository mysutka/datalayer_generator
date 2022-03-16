<?php
namespace DatalayerGenerator\MessageGenerators;

class Add extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "addToCart",
			"activity" => "add",
		];
		parent::__construct($object, $options);
	}

	function getActivityData() {
		$_objects = $this->getObject();
		is_object($_objects) && ($_objects = [$_objects]);
		$out = [];
		foreach($_objects as $_o) {
			$objDT = \DatalayerGenerator\Datatypes\EcDatatype::CreateProduct($_o);
			$out[] = $objDT->getData($_o);
		}
		return ["products" => $out];
	}
}
