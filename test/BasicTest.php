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

	public function test_datalayer_for_product_impressions() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionsGenerator returns builtin product array
		$product = null;
		$instance->measureProductImpressions(new GoogleTagManager\MessageGenerators\ImpressionsGenerator($product));
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);
#		print(print_r($dl,true));

		$this->assertInternalType("array", $obj = array_shift($dl));
#		print(print_r($obj,true));

		# array contains two keys "ecommerce" and "event"
		$this->assertCount(2, array_keys($obj));
		$this->assertSame(["ecommerce","event"], array_keys($obj));
		$this->assertEquals("impressions", $obj["event"]);

		$this->assertArrayHasKey("impressions", $obj["ecommerce"]);
		$this->assertArrayNotHasKey("products", $obj["ecommerce"]["impressions"]);
		$this->assertInternalType("array", $obj["ecommerce"]["impressions"]);

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

	public function test_datalayer_for_product_detail() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = null;
		$instance->measureProductDetail(new GoogleTagManager\MessageGenerators\ProductDetailGenerator($product));
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);


#		print(print_r($dl,true));
		$this->assertInternalType("array", $obj = array_shift($dl));

#		print(print_r($obj,true));

		# array contains two keys "ecommerce" and "event"
		$this->assertCount(2, array_keys($obj));
		$this->assertSame(["ecommerce","event"], array_keys($obj));
		$this->assertEquals("detail", $obj["event"]);

		$this->assertArrayHasKey("detail", $obj["ecommerce"]);
		$this->assertArrayHasKey("products", $obj["ecommerce"]["detail"]);
		$this->assertInternalType("array", $obj["ecommerce"]["detail"]["products"]);

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

	public function test_datalayer_for_purchase() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = null;
		$instance->measureProductDetail(new GoogleTagManager\MessageGenerators\PurchaseGenerator($product));
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);


#		print(print_r($dl,true));
		$this->assertInternalType("array", $obj = array_shift($dl));

		print(print_r($obj,true));

		# array contains two keys "ecommerce" and "event"
		$this->assertCount(2, array_keys($obj));
		$this->assertSame(["ecommerce","event"], array_keys($obj));
		$this->assertEquals("purchase", $obj["event"]);

		$this->assertArrayHasKey("purchase", $obj["ecommerce"]);
		$this->assertArrayHasKey("products", $obj["ecommerce"]["purchase"]);
		$this->assertInternalType("array", $obj["ecommerce"]["purchase"]["products"]);

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

	public function test_datalayer_for_banner_promotions() {
		$instance = GoogleTagManager::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = null;
		$instance->measureProductImpressions(new GoogleTagManager\MessageGenerators\PromotionGenerator($product));
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);
#		print(print_r($dl,true));

		$this->assertInternalType("array", $obj = array_shift($dl));
#		print(print_r($obj,true));

		# array contains two keys "ecommerce" and "event"
		$this->assertCount(2, array_keys($obj));
		$this->assertSame(["ecommerce","event"], array_keys($obj));
		$this->assertEquals("promoView", $obj["event"]);

		$this->assertArrayHasKey("promoView", $obj["ecommerce"]);
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

