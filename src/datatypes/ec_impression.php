<?php
namespace DatalayerGenerator\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
abstract class EcImpression extends EcDatatype {

	/**
		* @return array this is the dummy class and so return dummy data in array
	 */
	function getData() {
		if (is_null($this->getObject())) {
			return null;
		};
		$out = [
			"id" => $this->getImpressionId(),
			"name" => $this->getImpressionName(),       // Name or ID is required.

			"list" => $this->getImpressionList(),
			"brand" => $this->getImpressionBrand(),
			"category" => $this->getImpressionCategory(),
			"variant" => $this->getImpressionVariant(),
			"position" => (int)$this->getImpressionPosition(),
			"price" => $this->getImpressionPrice(),
		];
		return array_filter($out);
	}

	abstract function getImpressionId();
	abstract function getImpressionName();
	abstract function getImpressionList();
	abstract function getImpressionBrand();
	abstract function getImpressionCategory();
	abstract function getImpressionVariant();
	abstract function getImpressionPosition();
	abstract function getImpressionPrice();
}
