<?php
/**
 * Base class for generators
 * For enhanced ecommerce.
 */
namespace DatalayerGenerator\MessageGenerators;

class ActionBase {
	/**
	 * @param array $options
	 * - xhr - render in xhr requests [default: false]
	 * - event - custom event name; defaults to value recognized by Universal Analytics tag in Google Tag Manager to support automatic Enhance Ecommerce events processing
	 * - activity - enhanced ecommerce activity: impressions, click, detail, add, remove, promoView, promoClick, checkout, purchase, refund etc..
	 */
	function __construct($object, $options=[]) {

		$options += [
			"xhr" => false,
			"event" => null,
			"activity" => null,
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

	function getActivity() {
		if (!isset($this->options["activity"])) {
			return null;
		}
		return $this->options["activity"];
	}

	function getActionField() {
		return null;
	}

	function getDataLayerMessage() {
		/* Hlasku vypsat jen kdyz je volana trida DatalayerGenerator nebo kdyz volana trida nema metodu getDataLayerMessage()
		if (get_called_class() == get_class() || !in_array("getDataLayerMessage" , get_class_methods(get_called_class()))) {
		}
		 */
		if (get_called_class() == get_class()) {
			trigger_error(sprintf("%s: do not use this class directly", get_called_class()));
		}
		$_activity = $this->getActivity();
		$out = [
			"ecommerce" => [
				"${_activity}" => $this->getActivityData(),
			],
		];

		if ($_event = $this->getEvent()) {
			$out["event"] = $_event;
		}
		if ($_actionField = $this->getActionField()) {
			$out["ecommerce"][$this->getActivity()]["actionField"] = $_actionField;
		}

		return $out;
	}
}

