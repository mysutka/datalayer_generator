<?php
namespace GoogleTagManager\MessageGenerators;

class PromotionGenerator extends DatalayerGenerator implements iMessage {

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
