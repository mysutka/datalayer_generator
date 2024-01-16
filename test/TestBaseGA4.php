<?php
class TestBaseGA4 extends PHPUnit\Framework\TestCase {

	public function test_dummy() {
		$this->assertTrue(true);
	}

	public function _test_basic($instance, $options=[]) {
		$options += [
			"debug" => false,
			"event" => null,
			"items_count" => 1,
		];

		$tested_keys = [
			$options["event"] ? "event" : null,
			"ecommerce",
		];
		$tested_keys = array_filter($tested_keys);
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
#		error_log(print_r($dl, true));
		$this->assertCount($options["items_count"], $dl);
		($options["debug"]===true) && print(print_r($dl,true));
		$this->assertIsArray($obj = array_shift($dl));
		($options["debug"]===true) && print(print_r($obj,true));
		# array contains two keys "ecommerce" and "event"
		$this->assertCount(sizeof($tested_keys), array_keys($obj));
		$this->assertEqualsCanonicalizing($tested_keys, array_keys($obj));

		if (is_null($options["event"])) {
			$this->assertArrayNotHasKey("event", $obj);
		} else {
			$this->assertEquals($options["event"], $obj["event"]);
		}
		$this->assertArrayHasKey("items", $obj["ecommerce"]);

		# test prvku pole
		foreach($obj["ecommerce"]["items"] as $idx => $item) {
			$this->assertEquals($idx, $item["index"]);
			$this->assertIsArray($item);
			$this->assertArrayHasKey("item_id", $item);
			$this->assertArrayHasKey("item_name", $item);
		}
	}

	function _test_basic_json($instance, $options=[]) {
		$tested_keys = [
			$options["event"] ? "event" : null,
			"ecommerce",
		];
		$tested_keys = array_filter($tested_keys);
		# messages returned as json
		$this->assertNotEmpty($dl_json = $instance->getDataLayerMessagesJson());
		$this->assertCount(1, $dl_json);
		$this->assertIsString($element = array_shift($dl_json));
		$this->assertNotNull($obj_json = json_decode($element,true));
		$this->assertIsArray($obj_json);
		$this->assertCount(count($tested_keys), array_keys($obj_json));
		$this->assertSame($tested_keys, array_keys($obj_json));
		if (is_null($options["event"])) {
			$this->assertArrayNotHasKey("event", $obj_json);
		} else {
			$this->assertEquals($options["event"], $obj_json["event"]);
		}
		$this->assertArrayHasKey("items", $obj_json["ecommerce"]);
		foreach($obj_json["ecommerce"]["items"] as $idx => $item) {
			$this->assertEquals($idx, $item["index"]);
			$this->assertIsArray($item);
			$this->assertArrayHasKey("item_id", $item);
			$this->assertArrayHasKey("item_name", $item);
		}
	}
}
