<?php
/**
 * @runTestsInSeparateProcesses
 */
class BasicTest extends TestBase {

	/*
	public function test_idea() {
		$instance = new DatalayerGenerator::Datalayer();
		$instance->push(new Datalayer\Analytics\EnhancedEcommerce\Purchase());
	}
	 */

	public function test_empty_datalayer() {
		$instance = DatalayerGenerator\Collector::GetInstance();
		$this->assertNotNull($instance);

		$this->assertEmpty($messages = $instance->getDataLayerMessages());
		$this->assertEmpty($messages_json = $instance->getDataLayerMessagesJson());
	}

	public function test_datalayer_for_product_impressions() {
		$instance = \DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionsGenerator returns builtin product array
		$products = ["a", "b", "c"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Impressions($products));
		$this->_test_basic($instance, ["activity" => "impressions", "event" => null]);
		$this->_test_basic_json($instance, ["activity" => "impressions", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayNotHasKey("products", $obj["ecommerce"]["impressions"]);
		$this->assertIsArray($obj["ecommerce"]["impressions"]);
		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["impressions"][0]);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_product_detail() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ProductDetailGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\ProductDetail($product));
		$this->_test_basic($instance, ["activity" => "detail", "event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "detail", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["detail"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["detail"]);
		$this->assertIsArray($obj["ecommerce"]["detail"]["products"]);
		$this->assertIsArray($obj["ecommerce"]["detail"]["actionField"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["detail"]["products"][0]);

		$product_data = $obj["ecommerce"]["detail"]["products"][0];
		$this->assertEquals("example Product ID", $product_data["id"]);
		$this->assertEquals("example Product Name", $product_data["name"]);
		$this->assertEquals("example Brand Name", $product_data["brand"]);
		$this->assertEquals("Example/Shoes/Sport", $product_data["category"]);
	}


	public function test_datalayer_for_purchase() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Purchase($product));
		$this->_test_basic($instance, ["activity" => "purchase", "event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "purchase", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["purchase"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["purchase"]);
		$this->assertIsArray($obj["ecommerce"]["purchase"]["products"]);
		$this->assertIsArray($obj["ecommerce"]["purchase"]["actionField"]);


		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_add() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Add($product));
		$this->_test_basic($instance, ["activity" => "add", "event" => "addToCart", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "add", "event" => "addToCart", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["add"]);
		$this->assertArrayNotHasKey("actionField", $obj["ecommerce"]["add"]);
		$this->assertIsArray($obj["ecommerce"]["add"]["products"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_checkout() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Checkout($product));
		$this->_test_basic($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "checkout", "event" => "checkout", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["checkout"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["checkout"]);
		$this->assertIsArray($obj["ecommerce"]["checkout"]["products"]);
		$this->assertIsArray($obj["ecommerce"]["checkout"]["actionField"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));
	}

	public function test_datalayer_for_banner_promotions() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Promotion($product));
		$this->_test_basic($instance, ["activity" => "promoView", "event" => "promoView", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("promotions", $obj["ecommerce"]["promoView"]);
		$this->assertArrayNotHasKey("products", $obj["ecommerce"]["promoView"]);
		$this->assertIsArray($obj["ecommerce"]["promoView"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["promoView"]["promotions"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["promoView"]["promotions"][0]);

		$promotion_data = $obj["ecommerce"]["promoView"]["promotions"][0];
		$this->assertEquals("example-banner-id-1", $promotion_data["id"]);
		$this->assertEquals("Example Summer Sale", $promotion_data["name"]);
		$this->assertEquals("Example Just Banner", $promotion_data["creative"]);
		$this->assertEquals("example: slot 1", $promotion_data["position"]);
	}

	public function test_datatypes() {
		$a = DatalayerGenerator\Datatypes\EcDatatype::CreateProduct(["a" => 1, "b" => 2]);
		$this->assertInstanceOf("DatalayerGenerator\Datatypes\Product", $a);
		$a = DatalayerGenerator\Datatypes\EcDatatype::CreateImpression(["a" => 1, "b" => 2]);
		$this->assertInstanceOf("DatalayerGenerator\Datatypes\Impression", $a);
		$a = DatalayerGenerator\Datatypes\EcDatatype::CreatePromotion(["a" => 1, "b" => 2]);
		$this->assertInstanceOf("DatalayerGenerator\Datatypes\Promotion", $a);
#		error_log(print_r($a,true));
#		error_log(print_r(Collector::GetProductClass(),true));
	}
}

