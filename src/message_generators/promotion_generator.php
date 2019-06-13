<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class PromotionGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "promoView";
	}

	function getEvent() {
		return null;
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		return [
			"id" => "example-banner-id-1",
			"name" => "Example Summer Sale",
			"creative" => "Example Just Banner",
			"position" => "example: slot 1",
		];
	}
}
