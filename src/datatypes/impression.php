<?php
namespace GoogleTagManager\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
class Impression extends EcDatatype {

	/**
		* @param $impressionObject objekt produktu
		* @return array this is the dummy class and so return dummy data in array
	 */
	function getData($impressionObject) {
		parent::getData($impressionObject);
		return [
			"id" =>  "example Product ID",
			"name" =>  "example Product Name",       // Name or ID is required.

			"list" => "example List name",
			"brand" => "Example Nike Brand",
			"category" =>  "Example/Shoes/Sport",
			"variant" => "example Black",
			"position" => 1,
			"price" => "123.5 CZK",
		];
	}
}
