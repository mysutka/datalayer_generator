<?php

class Brand extends ElementBase {

	function getName() {
		return $this->values["name"];
	}

	function __toString() {
		return (string)$this->getName();
	}
}
