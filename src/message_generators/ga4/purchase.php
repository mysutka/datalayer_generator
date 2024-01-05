<?php
namespace DatalayerGenerator\MessageGenerators\GA4;

class Purchase extends EventBase {

	public function __construct($object, $options=[]) {
		$options += [
			"event_name" => "purchase",
		];
		parent::__construct($object, $options);
	}

	public function getEcommerceData() {
		$out = [
			"currency" => null,
			"transaction_id" => null,
			"value" => null,
			"coupon" => null,
			"shipping" => null,
			"tax" => null,
			"items" => [],
		];
		$_items = [];

		$currency = $this->getCurrentCurrency();
		$price = $this->_getPriceToPay(false);
		$price_vat = $this->_getPriceToPay(true);
		$tax = $price_vat - $price;

		$out["currency"] = (string)$this->getCurrentCurrency();
		$out["transaction_id"] = $this->getObject()->getOrderNo();
		$out["value"] = round($price_vat, $currency->getDecimalsSummary());
		$out["tax"] = round($tax, $currency->getDecimalsSummary());
		$out["shipping"] = $this->_getShipping();
		foreach($this->items as $idx => $i) {
			$_item = $this->getCommonProductAttributes($i->getProduct());
			$_item["index"] = $idx;
			$_item["price"] = $i->getUnitPriceInclVat();
			$_item["quantity"] = $i->getAmount();
			$out["items"][] = array_filter($_item, ["DatalayerGenerator\MessageGenerators\GA4\EventBase", "_arrayFilter"]);
		}

		return array_filter($out);
	}

	protected function _getUnitPrice($order_item) {
		return null;
	}

	protected function _getShipping() {
		$shipping = $this->getObject()->getDeliveryMethod()->getPriceInclVat();
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
