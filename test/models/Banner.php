<?php

class Banner extends ElementBase {

	function getName() {
		return $this->values["promotion_name"];
	}

	function getHtmlElementId() {
		return $this->values["promotion_id"];
	}

	function __toString() {
		return (string)$this->getName();
	}
}
