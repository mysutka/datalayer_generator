<?php
namespace GoogleTagManager\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
class Product extends EcDatatype {

	/**
		* @param $productObject objekt produktu
		* @return array this is the dummy class and so return dummy data in array
	 */
	function getData($productObject) {
		parent::getData($productObject);
		return [
			"name" =>  "example Product Name",       // Name or ID is required.
			"id" =>  "example Product ID",
			"price" => "123.5 CZK",
			"brand" => "Example Nike Brand",
			"category" =>  "Example/Shoes/Sport",
			"variant" => "example Black",
			"list" => "example List name",
		];
	}
}
