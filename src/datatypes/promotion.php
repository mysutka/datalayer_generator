<?php
namespace GoogleTagManager\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
class Promotion extends EcDatatype implements iDatatype {

	/**
		* @param $impressionObject objekt produktu
		* @return array this is the dummy class and so return dummy data in array
	 */
	function getData($impressionObject) {
		return [
			"id" => "example-banner-id-1",
			"name" => "Example Summer Sale",
			"creative" => "Example Just Banner",
			"position" => "example: slot 1",
		];
	}
}
