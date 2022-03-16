<?php
namespace DatalayerGenerator\Datatypes;

abstract class EcPromotion extends EcDatatype {

	/**
		* @return array this is the dummy class and so return dummy data in array
	 */
	function getData() {
		if (is_null($this->getObject())) {
			return null;
		};
		$out = [
			"id" => $this->getPromotionId(),
			"name" => $this->getPromotionName(),
			"creative" => $this->getPromotionCreative(),
			"position" => $this->getPromotionPosition(),
		];
		return array_filter($out);
	}

	abstract function getPromotionId();
	abstract function getPromotionName();
	abstract function getPromotionCreative();
	abstract function getPromotionPosition();
}

