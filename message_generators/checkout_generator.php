<?php
namespace GoogleTagManager\MessageGenerators;

class CheckoutGenerator extends MessageGenerator implements iMessage {

	function toObject() {
		parent::toObject();
		return [[
			"name" =>  "example Product Name",       // Name or ID is required.
			"id" =>  "example Product ID",
			"price" => "123.5 CZK",
			"brand" => "Example Nike Brand",
			"category" =>  "Example/Shoes/Sport",
			"variant" => "example Black",
			"quantity" => 1,
		]];
	}
}
