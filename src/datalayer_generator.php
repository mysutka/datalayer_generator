<?php
/**
 * Base class for generators
 */
namespace GoogleTagManager;

class DatalayerGenerator {
	/**
	 * @param array $options
	 * - xhr - render in xhr requests [default: false]
	 */
	function __construct($object, $options=[]) {

		$options += [
			"xhr" => false,
		];
		$this->object = $object;
		$this->options = $options;
	}

	function getObject() {
		return $this->object;
	}

	function getDatalayerMessage() {
		/* Hlasku vypsat jen kdyz je volana trida DatalayerGenerator nebo kdyz volana trida nema metodu getDatalayerMessage()
		if (get_called_class() == get_class() || !in_array("getDatalayerMessage" , get_class_methods(get_called_class()))) {
		}
		 */
		trigger_error(sprintf("%s: do not use this class directly", get_called_class()));
		return [];
	}
}

