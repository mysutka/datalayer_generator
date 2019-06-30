<?php
/**
 * @runTestsInSeparateProcesses
 */
class BasicTest extends PHPUnit\Framework\TestCase {

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

		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);
		($options["debug"]===true) && print(print_r($dl,true));
		$this->assertInternalType("array", $obj = array_shift($dl));
		($options["debug"]===true) && print(print_r($obj,true));
		# array contains two keys "ecommerce" and "event"
		$this->assertCount(2, array_keys($obj));
		$this->assertSame(["ecommerce","event"], array_keys($obj));

		$this->assertEquals($options["event"], $obj["event"]);
		$this->assertArrayHasKey($options["activity"], $obj["ecommerce"]);
	}

	function _test_basic_json($instance, $options=[]) {
		# messages returned as json
		$this->assertNotEmpty($dl_json = $instance->getDataLayerMessagesJson());
		$this->assertCount(1, $dl_json);
		$this->assertInternalType("string", $element = array_shift($dl_json));
		$this->assertNotNull($obj_json = json_decode($element,true));
		$this->assertInternalType("array", $obj_json);
	}

	public function test_datalayer_for_product_impressions() {
		$instance = GoogleTagManager::GetInstance();

		$instance->setImpressionClass(new GoogleTagManager\Datatypes\Impression());

		# @todo use own Generator, ImpressionsGenerator returns builtin product array
		$product = null;
		$instance->measureProductImpressions(new GoogleTagManager\MessageGenerators\ImpressionsGenerator($product));
		$this->_test_basic($instance, ["activity" => "impressions", "event" => "impressions"]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);

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
		$instance->setProductClass(new GoogleTagManager\Datatypes\Product());

		# @todo use own Generator, ProductDetailGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureProductDetail(new GoogleTagManager\MessageGenerators\ProductDetailGenerator($product));
		$this->_test_basic($instance, ["activity" => "detail", "event" => "detail", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["detail"]);
		$this->assertInternalType("array", $obj["ecommerce"]["detail"]["products"]);

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["detail"]["products"][0]);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_purchase() {
		$instance = GoogleTagManager::GetInstance();
		$instance->setProductClass(new GoogleTagManager\Datatypes\Product());

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measurePurchase(new GoogleTagManager\MessageGenerators\PurchaseGenerator($product));
		$this->_test_basic($instance, ["activity" => "purchase", "event" => "purchase", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);

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

	public function test_datalayer_for_checkout() {
		$instance = GoogleTagManager::GetInstance();
		$instance->setProductClass(new GoogleTagManager\Datatypes\Product());

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\CheckoutGenerator($product));
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
		$instance->setPromotionClass(new GoogleTagManager\Datatypes\Promotion);

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureEcommerceObject(new GoogleTagManager\MessageGenerators\PromotionGenerator($product));
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

