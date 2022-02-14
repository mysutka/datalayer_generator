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

	function getActivityData() {
		$objDT = \GoogleTagManager::GetImpressionClass();
		return [$objDT->getData($this->getObject())];
	}

	function getIdsMap() {
		return [];
	}
}
