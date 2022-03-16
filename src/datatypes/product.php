<?php
namespace DatalayerGenerator\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
class Product extends EcProduct {

	public function getProductId() {
		return "example Product ID";
	}

	public function getProductName() {
		return "example Product Name";
	}

	public function getProductBrand() {
		return "example Brand Name";
	}

	public function getProductVariant() {
		return "example Black";
	}

	public function getProductCategory() {
		return "Example/Shoes/Sport";
	}

	public function getProductPrice() {
		return 24.5;
	}
}
