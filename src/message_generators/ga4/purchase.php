<?php
namespace DatalayerGenerator\MessageGenerators\GA4;
use DatalayerGenerator\MessageGenerators\GA4\ItemConverter\OrderItemConverter;

class Purchase extends EventBase {

	public function __construct($object, $event_params=[], $options=[]) {
		$event_params += [
			"event_name" => "purchase",
		];
		$options += [
			"item_converter" => new OrderItemConverter($options),
		];
		parent::__construct($object, $event_params, $options);
	}

	public function getEcommerceData() {
		$out = parent::getEcommerceData();
		$out += [
			"currency" => null,
			"transaction_id" => null,
			"value" => null,
			"coupon" => null,
			"shipping" => null,
			"tax" => null,
		];

		$currency = $this->getCurrentCurrency();
		$price = $this->_getPriceToPay(false);
		$price_vat = $this->_getPriceToPay(true);
		$tax = $price_vat - $price;

		$currency_decimals_summary = 2;
		if ($currency) {
			$currency_decimals_summary = $currency->getDecimalsSummary();
		}
		$out["currency"] = (string)$currency;
		$out["transaction_id"] = $this->getObject()->getOrderNo();
		$out["value"] = round($price_vat, $currency_decimals_summary);
		$out["tax"] = round($tax, $currency_decimals_summary);
		$out["shipping"] = round($this->_getShipping(), $currency_decimals_summary);
		return $out;
	}

	function _getUnitPrice($order_item) {
		return $order_item->getUnitPriceInclVat();
	}

	function getAmount($order_item) {
		return $order_item->getAmount();
	}

	protected function _getShipping() {
		$shipping = $this->getObject()->getDeliveryFeeInclVat();
		return $shipping;
	}

	/**
	 * Celkova cena za transakci:
	 * + cena za zbozi
	 * - sleva za vouchery
	 * - sleva za kampane (registrace, velka objednavka ...
	 */
	private function _getPriceToPay($incl_vat=true) {
		$order = $this->getObject();
		$_price = $order->getItemsPrice($incl_vat);
		$_price -= $order->getVouchersDiscountAmount($incl_vat);
		$_price -= $order->getCampaignsDiscountAmount($incl_vat);
		return $_price;
}

}
