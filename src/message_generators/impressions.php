<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class Impressions extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
			"event" => "impressions",
		];
		parent::__construct($object, $options);
	}

	function getActivity() {
		return "impressions";
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		$objDT = \GoogleTagManager::GetImpressionClass();
		$_activity = $this->getActivity();
		$out = [
			"ecommerce" => [
				"${_activity}" => [$objDT->getData($this->getObject())],
			],
		];
		if ($_event = $this->getEvent()) {
			$out["event"] = $_event;
		}
		return $out;

	}

	function getIdsMap() {
		return [];
	}
}
