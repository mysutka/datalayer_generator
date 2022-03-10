<?php
/**
 * @runTestsInSeparateProcesses
 */
class BasicTest extends PHPUnit\Framework\TestCase {

	/*
	public function test_idea() {
		$instance = new Datalayer::GoogleTagManager();
		$instance->push(new Datalayer\Analytics\EnhancedEcommerce\Purchase());
	}
	 */

	public function test_empty_datalayer() {
		$instance = GoogleTagManager::GetInstance();
		$this->assertNotNull($instance);

		$this->assertEmpty($messages = $instance->getDataLayerMessages());
		$this->assertEmpty($messages_json = $instance->getDataLayerMessagesJson());
	}

	public function _test_basic($instance, $options=[]) {
		$options += [
			"debug" => false,
			"activity" => null,
			"event" => null,
		];

		$tested_keys = [
			"ecommerce",
			$options["event"] ? "event" : null,
		];
		$tested_keys = array_filter($tested_keys);
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);
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
	}

	public function test_datalayer_for_product_impressions() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionsGenerator returns builtin product array
		$product = null;
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\Impressions($product));
		$this->_test_basic($instance, ["activity" => "impressions", "event" => null]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayNotHasKey("products", $obj["ecommerce"]["impressions"]);
		$this->assertInternalType("array", $obj["ecommerce"]["impressions"]);
		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["impressions"][0]);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_product_detail() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ProductDetailGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\ProductDetail($product));
		$this->_test_basic($instance, ["activity" => "detail", "event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["detail"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["detail"]);
		$this->assertInternalType("array", $obj["ecommerce"]["detail"]["products"]);
		$this->assertInternalType("array", $obj["ecommerce"]["detail"]["actionField"]);

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["detail"]["products"][0]);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_purchase() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\Purchase($product));
		$this->_test_basic($instance, ["activity" => "purchase", "event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["purchase"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["purchase"]);
		$this->assertInternalType("array", $obj["ecommerce"]["purchase"]["products"]);
		$this->assertInternalType("array", $obj["ecommerce"]["purchase"]["actionField"]);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_add() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\Add($product));
		$this->_test_basic($instance, ["activity" => "add", "event" => "addToCart", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "add", "event" => "addToCart", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["add"]);
		$this->assertArrayNotHasKey("actionField", $obj["ecommerce"]["add"]);
		$this->assertInternalType("array", $obj["ecommerce"]["add"]["products"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_checkout() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\Checkout($product));
		$this->_test_basic($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["checkout"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["checkout"]);
		$this->assertInternalType("array", $obj["ecommerce"]["checkout"]["products"]);
		$this->assertInternalType("array", $obj["ecommerce"]["checkout"]["actionField"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_banner_promotions() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\Promotion($product));
		$this->_test_basic($instance, ["activity" => "promoView", "event" => "promoView", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$this->assertArrayNotHasKey("products", $obj["ecommerce"]["promoView"]);
		$this->assertInternalType("array", $obj["ecommerce"]["promoView"]);

		# messages returned as json
		$this->assertNotEmpty($dl_json = $instance->getDataLayerMessagesJson());
		$this->assertCount(1, $dl_json);
		$this->assertInternalType("string", $element = array_shift($dl_json));
		$this->assertNotNull($obj_json = json_decode($element,true));
		$this->assertInternalType("array", $obj_json);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, $obj_json);
	}
}

