<?php
namespace DatalayerGenerator\Datatypes;

/**
 * This is a dummy class and should be inherited to return real data.
 *
 * Impression should return array that corresponds to impressionFieldObject specified by EC reference documentation.
 *
 * https://developers.google.com/analytics/devguides/collection/analyticsjs/enhanced-ecommerce#ecommerce-data
 */
class Promotion extends EcPromotion {

	public function getPromotionId() {
		return "example-banner-id-1";
	}

	public function getPromotionName() {
		return "Example Summer Sale";
	}

	public function getPromotionCreative() {
		return "Example Just Banner";
	}

	public function getPromotionPosition() {
		return "example: slot 1";
	}
}
