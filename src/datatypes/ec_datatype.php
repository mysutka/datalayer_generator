<?php
namespace DatalayerGenerator\Datatypes;

class EcDatatype {

	static $ProductClassName = "\DatalayerGenerator\Datatypes\Product";
	static $ImpressionClassName = "\DatalayerGenerator\Datatypes\Impression";
	static $PromotionClassName = "\DatalayerGenerator\Datatypes\Promotion";

	public function __construct($object = null, $options=[]) {
		$this->object = $object;
		$this->options = $options;
	}

	protected function getObject() {
		return $this->object;
	}

	static function CreateImpression($object, $options=[]) {
		$class_name = static::$ImpressionClassName;
		return new $class_name($object, $options);
	}

	static function CreatePromotion($object) {
		$class_name = static::$PromotionClassName;
		return new $class_name($object);
	}

	static function CreateProduct($object) {
		$class_name = static::$ProductClassName;
		return new $class_name($object);
	}

	static function SetProductClassName($class_name) {
		if (is_object($class_name)) {
			$class_name = get_class($class_name);
		}
		static::$ProductClassName = $class_name;
	}

	static function SetImpressionClassName($class_name) {
		if (is_object($class_name)) {
			$class_name = get_class($class_name);
		}
		static::$ImpressionClassName = $class_name;
	}

	static function SetPromotionClassName($class_name) {
		if (is_object($class_name)) {
			$class_name = get_class($class_name);
		}
		static::$PromotionClassName = $class_name;
	}
}
