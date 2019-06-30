<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;
use GoogleTagManager\Datatypes\Impression;

class ImpressionsGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "impressions";
	}

	function getEvent() {
		return null;
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		$objDT = $this->getImpressionClass();
		$_activity = $this->getActivity();
		return [
			"ecommerce" => [
				"${_activity}" => [$objDT->getData($this->getObject())],
			],
			"event" => "impressions"
		];
	}

	function getImpressionClass() {
		$instance = \GoogleTagManager::GetInstance();
		return $instance->impressionClass;
	}

	function getIdsMap() {
		return [];
	}
}
