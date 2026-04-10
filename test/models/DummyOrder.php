<?php

class DummyOrder extends ElementBase {

	function getOrderNo() {
		return $this->values["order_no"];
	}

	function getDeliveryFeeInclVat() {
		return (float)79.0;
	}

	function getItemsPrice($with_vat = true) {
		$out = 9876.54;
		if ($with_vat===false) {
			$out = 9876.54/121*100;
		}
		return $out;
	}

	function getVouchersDiscountAmount() {
		return 500;
	}

	function getCampaignsDiscountAmount() {
		return 1000;
	}

	function getCampaigns() {
		return array_map(function($e) {
			return new Campaign($e);
		}, $this->values["campaigns"]
		);
	}

	function getVouchers() {
		if (!isset($this->values["vouchers"])) {
			return [];
		}
		return array_map(function($e) {
			return new Voucher($e);
		}, $this->values["vouchers"]
		);
	}
}
