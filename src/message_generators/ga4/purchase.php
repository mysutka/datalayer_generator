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
		$out["items"] = $this->_applyDiscountToItems($out["items"]);
		foreach($out["items"] as &$i) {
			if (!isset($i["discount"])) {
				continue;
			}
			$i["discount"] = round($i["discount"], ($currency_decimals_summary+2));
			// price is discounted
			// @see https://developers.google.com/analytics/devguides/collection/ga4/apply-discount?client_type=gtag
			$i["price"] = $i["price"] - $i["discount"];
		}
		return $out;
	}

	/**
	 * The discount is calculated and distributed among the items based on their respective prices
	 * discount is per unit
	 */
	function _applyDiscountToItems($items) {
		$order = $this->getObject();
		$price_vat = $order->getItemsPrice(true);
		# getItemsPrice() includes rounding price
		# we need to get price without the rounding price
		# also we do not apply discount to products in sale
		foreach($this->items as $i) {
			if($i->getProduct()->getCode()=="price_rounding") {
				$price_vat -= $i->getUnitPriceInclVat() * $i->getAmount();
			} elseif (!$i->getCampaignDiscountApplied()) {
				$price_vat -= $i->getUnitPriceInclVat() * $i->getAmount();
			}
		}
		# Distribute the discount between items (excluding rounding)
		$discount = $this->getDiscount();
		foreach($items as $idx => &$i) {
			if ($this->items[$idx]->getProduct()->getCode()=="price_rounding") {
				continue;
			}
			# Campaign was not available, we do not apply discount to the item
			if (!$this->items[$idx]->getCampaignDiscountApplied()) {
				continue;
			}
			$_prc = $i["price"];
			$_d = $_prc / $price_vat * $discount;
			$i["discount"] = $_d;
		}
		return $items;

	}

	function _getUnitPrice($order_item) {
		return $order_item->getUnitPriceInclVat();
	}

	function getAmount($order_item) {
		return $order_item->getAmount();
	}

	/**
	 * Price for shipping.
	 *
	 * When order contains a campaign or voucher with free shipping flag, returns 0.
	 * Does not distinguish / detect partial discount. Only full price or zero.
	 */
	protected function _getShipping() {
		$shipping = $this->getObject()->getDeliveryFeeInclVat();
		$campaigns = $this->getObject()->getCampaigns();
		$vouchers = $this->getObject()->getVouchers();

		$campaigns = array_filter($campaigns, function($c) {
			return $c->freeShipping();
		});
		$vouchers = array_filter($vouchers, function($c) {
			return $c->freeShipping();
		});
		if ((sizeof($campaigns)>0) || (sizeof($vouchers)>0)) {
			$shipping = 0.0;
		}
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
		$_price -= $order->getVouchersDiscountAmount($incl_vat, ["free_shipping" => false]);
		$_price -= $this->_getCampaignsDiscountAmount($incl_vat);
		return $_price;
	}

	/**
	 * Sleva za kampane, bez dopravy zdarma.
	 *
	 */
	private function _getCampaignsDiscountAmount($incl_vat) {
		$campaigns = $this->getObject()->getCampaigns();
		$campaigns = array_filter($campaigns, function($c) {
			return !$c->freeShipping();
		});
		$out = 0.0;
		foreach($campaigns as $c) {
			$out += $c->getDiscountAmount($incl_vat);
		}
		return $out;
	}

	private function _getVouchersDiscountAmount($incl_vat) {
		$order = $this->getObject();
		$vouchers = $order->getVouchers();
		$vouchers = array_filter($vouchers, function($v) {
			return !$v->freeShipping();
		});
		
		$out = 0.0;
		foreach($vouchers as $v) {
#			$out += $v->getDiscountAmount($incl_vat);
			$out += $v->getDiscountAmount();
		}
		return $out;
	}

	private function getDiscount() {
		$_discount = $this->_getCampaignsDiscountAmount(true);
		$_discount += $this->_getVouchersDiscountAmount(true);

		return $_discount;
	}
}
