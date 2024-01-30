<?php

class DummyBasket {

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
}

