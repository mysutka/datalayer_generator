<?php
/**
 * @runTestsInSeparateProcesses
 */
class BasicGA4Test extends TestBaseGA4 {

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

	public function notest_datalayer_for_product_impressions() {
		$instance = \DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionsGenerator returns builtin product array
		$products = ["a", "b", "c"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Impressions($products));
		$this->_test_basic($instance, ["event" => null]);
		$this->_test_basic_json($instance, ["event" => null, "debug" => !true]);

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
		$instance->setItemizer( new DummyItemizer );

		# @todo use own Generator, ProductDetailGenerator returns builtin product array
		$products = ["a","b"];
		$instance->push(new DatalayerGenerator\MessageGenerators\GA4\ViewItem($products[0], ["items" => $products]));
		$this->_test_basic($instance, ["event" => "view_item", "ecommerce" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "view_item", "ecommerce" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertIsArray($obj["ecommerce"]["items"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		# test prvku pole
		$this->assertArrayHasKey("item_id", $obj["ecommerce"]["items"][0]);
		$this->assertArrayHasKey("item_name", $obj["ecommerce"]["items"][0]);

		$product_data = $obj["ecommerce"]["items"][0];
		$this->assertEquals("catalog_id", $product_data["item_id"]);
		$this->assertEquals("dummy name", $product_data["item_name"]);
		$this->assertEquals("Dummy brand", $product_data["item_brand"]);
#		$this->assertEquals("Example/Shoes/Sport", $product_data["category"]);
	}


	public function notest_datalayer_for_purchase() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Purchase($product));
		$this->_test_basic($instance, ["event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => null, "debug" => !true]);

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

	public function notest_datalayer_for_add() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Add($product));
		$this->_test_basic($instance, ["event" => "addToCart", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "addToCart", "debug" => !true]);

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

	public function notest_datalayer_for_checkout() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Checkout($product));
		$this->_test_basic($instance, ["event" => "checkout", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "checkout", "debug" => !true]);

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

	public function notest_datalayer_for_banner_promotions() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$product = ["a","b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Promotion($product));
		$this->_test_basic($instance, ["event" => "promoView", "debug" => !true]);

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

	public function notest_datatypes() {
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

class DummyItemizer {
	function getCommonProductAttributes($product) {
		$categories = [
			"Catalog",
			"Books",
			"Human sciences",
			"History",
		];
		return [
			"item_id" => "catalog_id",
			"item_name" => "dummy name",
			"affiliation" => "Dummy affiliation name",
			"coupon" => "",
			"discount" => "",
			"index" => 0,
			"item_brand" => "Dummy brand",
			"item_category" => $categories[0],
			"item_category2" => $categories[1],
			"item_category3" => $categories[2],
			"item_category4" => $categories[3],
			"item_category5" => null,
			"item_list_id" => "",
			"item_list_name" => "",
			"item_variant" => "",
			"location_id" => "",
			"quantity" => 1,
		];
	}

	function getUnitPrice($product, DatalayerGenerator\MessageGenerators\GA4\EventBase $event_base) {
		return 123.45;
	}
}

