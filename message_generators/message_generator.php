<?php
namespace GoogleTagManager\MessageGenerators;

class MessageGenerator {
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

	function toObject() {
		trigger_error(sprintf("%s: do not use this class directly", get_called_class()));
		return [];
	}
}

