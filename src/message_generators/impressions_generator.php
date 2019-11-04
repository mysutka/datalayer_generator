<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class ImpressionsGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "impressions";
	}

	function getEvent() {
		return null;
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		$objDT = \GoogleTagManager::GetImpressionClass();
		$_activity = $this->getActivity();
		return [
			"ecommerce" => [
				"${_activity}" => [$objDT->getData($this->getObject())],
			],
			"event" => "impressions"
		];
	}

	function getIdsMap() {
		return [];
	}
}
