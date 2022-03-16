<?php
namespace DatalayerGenerator\MessageGenerators;

interface iMessage {

	/**
	 * - product and promotion actions: click, detail, add, remove,  checkout, checkout_option, purchase, refund, promo_click, (promo_view, impressions)
	 * - impression data: impressions
	 * - product data: products
	 * - promotion data: promotions
	 */
#	public function getEcommerceMeasurement();
#	public function getDataLayerMessage();
	public function getActivityData();
}
