<?php
namespace GoogleTagManager\MessageGenerators;

class Impressions extends ActionBase implements iMessage {

	function __construct($object, $options=[]) {
		$options += [
#			"event" => "impressions",
			"activity" => "impressions",
		];
		parent::__construct($object, $options);
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
		return $out;

	}

	function getIdsMap() {
		return [];
	}
}
