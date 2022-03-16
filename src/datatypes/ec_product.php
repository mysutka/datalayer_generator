<?php
namespace DatalayerGenerator\Datatypes;

abstract class EcProduct extends EcDatatype {

	/**
		* @return array this is the dummy class and so return dummy data in array
	 */
	function getData() {
		if (is_null($this->getObject())) {
			return null;
		};
		$out = [
			"name" =>  $this->getProductName(),       // Name or ID is required.
			"id" =>  $this->getProductId(),
			"price" => $this->getProductPrice(),
			"brand" => $this->getProductBrand(),
			"category" =>  $this->getProductCategory(),
			"variant" => $this->getProductVariant(),
#			"list" => "example List name",
		];
		return array_filter($out);
	}

	abstract function getProductId();
	abstract function getProductName();
	abstract function getProductBrand();
	abstract function getProductCategory();
	abstract function getProductVariant();
	abstract function getProductPrice();
}
