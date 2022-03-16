<?php
namespace DatalayerGenerator\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
class Impression extends EcImpression {

	public function getImpressionId() {
		return "example Product Impression ID";
	}

	public function getImpressionName() {
		return "example Product Impression Name";
	}

	public function getImpressionBrand() {
		return "example Brand Name";
	}

	public function getImpressionCategory() {
		return "Impressions/Categories/Examples";
	}

	public function getImpressionVariant() {
		return "Impressive Variant";
	}

	public function getImpressionPosition() {
		return 1;
	}

	public function getImpressionPrice() {
		return 24.5;
	}

	public function getImpressionList() {
		return "category";
	}
}


