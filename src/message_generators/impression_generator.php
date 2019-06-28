<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class ImpressionGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "impressions";
	}

	function getEvent() {
		return null;
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		return [[
			"name" =>  "example Product Name",       // Name or ID is required.
			"id" =>  "example Product ID",
			"price" => "123.5 CZK",
			"brand" => "Example Nike Brand",
			"category" =>  "Example/Shoes/Sport",
			"variant" => "example Black",
			"list" => "example List name",
		]];
	}
	
	function getIdsMap() {
		return [];
	}
}
