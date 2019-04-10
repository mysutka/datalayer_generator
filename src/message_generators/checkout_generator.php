<?php
namespace GoogleTagManager\MessageGenerators;

class CheckoutGenerator extends DatalayerGenerator implements iMessage {

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		return [
			[
				"name" =>  "example Product Name",       // Name or ID is required.
				"id" =>  "example Product ID",
				"price" => "123.5 CZK",
				"brand" => "Example Nike Brand",
				"category" =>  "Example/Shoes/Sport",
				"variant" => "example Black",
				"quantity" => 1,
			],
			[
				"name" =>  "example 2 Product Name",       // Name or ID is required.
				"id" =>  "example 2 Product ID",
				"price" => "234.5 CZK",
				"brand" => "Example 2 Nike Brand",
				"category" =>  "Example/Shoes/Sport",
				"variant" => "example Red",
				"quantity" => 3,
			],
		];
	}
}
