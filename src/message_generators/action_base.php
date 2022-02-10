<?php
/**
 * Base class for generators
 */
namespace GoogleTagManager\MessageGenerators;;

class ActionBase {
	/**
	 * @param array $options
	 * - xhr - render in xhr requests [default: false]
	 * - event - custom event name; defaults to value recognized by Universal Analytics tag in Google Tag Manager to support automatic Enhance Ecommerce events processing
	 */
	function __construct($object, $options=[]) {

		$options += [
			"xhr" => false,
			"event" => null,
		];
		$this->object = $object;
		$this->options = $options;
	}

	function getObject() {
		return $this->object;
	}

	function getEvent() {
		if (!isset($this->options["event"])) {
			return null;
		}
		return $this->options["event"];
	}

	function getDatalayerMessage() {
		/* Hlasku vypsat jen kdyz je volana trida DatalayerGenerator nebo kdyz volana trida nema metodu getDatalayerMessage()
		if (get_called_class() == get_class() || !in_array("getDatalayerMessage" , get_class_methods(get_called_class()))) {
		}
		 */
		if (get_called_class() == get_class()) {
			trigger_error(sprintf("%s: do not use this class directly", get_called_class()));
		}
		return [];
	}
}

