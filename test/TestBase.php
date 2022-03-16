<?php
class TestBase extends PHPUnit\Framework\TestCase {

	public function test_dummy() {
		$this->assertTrue(true);
	}

	public function _test_basic($instance, $options=[]) {
		$options += [
			"debug" => false,
			"activity" => null,
			"event" => null,
			"items_count" => 1,
		];

		$tested_keys = [
			"ecommerce",
			$options["event"] ? "event" : null,
		];
		$tested_keys = array_filter($tested_keys);
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount($options["items_count"], $dl);
		($options["debug"]===true) && print(print_r($dl,true));
		$this->assertInternalType("array", $obj = array_shift($dl));
		($options["debug"]===true) && print(print_r($obj,true));
		# array contains two keys "ecommerce" and "event"
		$this->assertCount(sizeof($tested_keys), array_keys($obj));
		$this->assertSame($tested_keys, array_keys($obj));

		if (is_null($options["event"])) {
			$this->assertArrayNotHasKey("event", $obj);
		} else {
			$this->assertEquals($options["event"], $obj["event"]);
		}
		$this->assertArrayHasKey($options["activity"], $obj["ecommerce"]);
	}

	function _test_basic_json($instance, $options=[]) {
		$tested_keys = [
			"ecommerce",
			$options["event"] ? "event" : null,
		];
		$tested_keys = array_filter($tested_keys);
		# messages returned as json
		$this->assertNotEmpty($dl_json = $instance->getDataLayerMessagesJson());
		$this->assertCount(1, $dl_json);
		$this->assertInternalType("string", $element = array_shift($dl_json));
		$this->assertNotNull($obj_json = json_decode($element,true));
		$this->assertInternalType("array", $obj_json);
		$this->assertCount(count($tested_keys), array_keys($obj_json));
		$this->assertSame($tested_keys, array_keys($obj_json));
		if (is_null($options["event"])) {
			$this->assertArrayNotHasKey("event", $obj_json);
		} else {
			$this->assertEquals($options["event"], $obj_json["event"]);
		}
		$this->assertArrayHasKey($options["activity"], $obj_json["ecommerce"]);
	}
}
