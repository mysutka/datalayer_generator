<?php
namespace DatalayerGenerator\MessageGenerators;

class Impressions extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
#			"event" => "impressions",
			"activity" => "impressions",
		];
		parent::__construct($object, $options);
	}

	function getActivityData() {
		$out = [];
		$_position = 1;
		foreach($this->getObject() as $_o) {
			$options = [
				"position" => $_position,
			];
			$objDT = \DatalayerGenerator\Datatypes\EcDatatype::CreateImpression($_o, $options);
			if ($_out = $objDT->getData()) {
				$out[] = $_out;
			}
			$_position++;
		}
		return $out;
	}

	/*
	function getIdsMap() {
		return [];
	}
	 */
}
