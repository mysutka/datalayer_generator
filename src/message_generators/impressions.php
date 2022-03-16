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
		foreach($this->getObject() as $_o) {
			$objDT = \DatalayerGenerator\Datatypes\EcDatatype::CreateImpression($_o);
			if ($_out = $objDT->getData()) {
				$out[] = $_out;
			}
		}
		return $out;
	}

	/*
	function getIdsMap() {
		return [];
	}
	 */
}
