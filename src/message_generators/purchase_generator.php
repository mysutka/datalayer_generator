<?php
namespace GoogleTagManager\MessageGenerators;
use GoogleTagManager\DatalayerGenerator;

class PurchaseGenerator extends DatalayerGenerator implements iMessage {

	function getActivity() {
		return "purchase";
	}

	function getEvent() {
		return null;
	}

	function getDatalayerMessage() {
		parent::getDatalayerMessage();
		return [
			"actionField" => [
				"id" => "order#no",
				"affiliation" => "Example e-shop",
				"revenue" => "102", # items price without vat
				"tax" => "21.5",
				"shipping" => "99"
			],
			"products" => [
				[
					"id" =>  "Purchased Product ID",
					"name" =>  "Purchased Product Name",       // Name or ID is required.
					"variant" => "Purchased Product Variant",
					"category" => "Shoes / Children",
					"sku" => "",
					"price" => "123.5", # unit price including vat
					"quantity" => "3"
				],
			]
		];
	}
}
