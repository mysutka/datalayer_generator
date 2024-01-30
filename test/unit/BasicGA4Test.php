<?php
/**
 * @runTestsInSeparateProcesses
 */
class BasicGA4Test extends TestBaseGA4 {

	public function test_empty_datalayer() {
		$instance = DatalayerGenerator\Collector::GetInstance();
		$this->assertNotNull($instance);

		$this->assertEmpty($messages = $instance->getDataLayerMessages());
		$this->assertEmpty($messages_json = $instance->getDataLayerMessagesJson());
	}

	public function test_datalayer_for_product_detail() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		$expected_product = [
			"catalog_id" => "product-id-001",
			"name" => "Neverending Story"
		];

		$products = [new Product($expected_product), new Product(["name" => "b"])];
		$instance->push(new DatalayerGenerator\MessageGenerators\GA4\ViewItem("a", ["items" => $products], ["item_converter" => new DummyConverter]));
		$this->_test_basic($instance, ["event" => "view_item", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "view_item", "debug" => !true]);
		$this->_assertJsonIsSameAsArray($instance);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$product_data = $obj["ecommerce"]["items"][0];

		$this->assertEquals("product-id-001", $product_data["item_id"]);
		$this->assertEquals("Neverending Story", $product_data["item_name"]);
		$this->assertEquals("Brandy", $product_data["item_brand"]);
		$this->assertEquals(1, $product_data["quantity"]);
		$this->assertEquals("Catalog", $product_data["item_category"]);
		$this->assertEquals("Books", $product_data["item_category2"]);
		$this->assertEquals("Human sciences", $product_data["item_category3"]);
		$this->assertEquals("History", $product_data["item_category4"]);
		$this->assertArrayNotHasKey("item_category5", $product_data);
		$this->assertEquals(1, $product_data["quantity"]);
	}

	public function test_datalayer_for_purchase() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		$order_values = [
			"order_no" => "ORDER-T_12345",
		];
		$order = new DummyOrder($order_values);
		$expected_item = [
			"amount" => 2,
			"transaction_id" => "dummy-T_12345",
			"product" => [
				"name" => "Purchased product 1",
				"catalog_id" => "ordered item 1",
				"card" => [
					"brand" => [
						"name" => "Super Dummies",
					],
				],
			],
		];
		$items = [new OrderItem($expected_item), new OrderItem(["product" => ["name" => "dummy name 2"]])];
		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\Purchase($order, ["items" => $items], ["item_converter" => new DummyOrderItemConverter]));
		$this->_test_basic($instance, ["event" => "purchase", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "purchase", "debug" => !true]);
		$this->_assertJsonIsSameAsArray($instance);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$product_data = $obj["ecommerce"]["items"][0];

		$this->assertEquals($expected_item["product"]["catalog_id"], $product_data["item_id"]);
		$this->assertEquals($expected_item["product"]["name"], $product_data["item_name"]);
		$this->assertEquals($expected_item["product"]["card"]["brand"]["name"], $product_data["item_brand"]);
		$this->assertEquals($expected_item["amount"], $product_data["quantity"]);
		$this->assertEquals("Catalog", $product_data["item_category"]);
		$this->assertEquals("Books", $product_data["item_category2"]);
		$this->assertEquals("Human sciences", $product_data["item_category3"]);
		$this->assertEquals("History", $product_data["item_category4"]);
		$this->assertArrayNotHasKey("item_category5", $product_data);

		# purchase specific attributes
		$this->assertEquals(11376.54, $obj["ecommerce"]["value"]);
		$this->assertEquals(1714.11, $obj["ecommerce"]["tax"]);
		$this->assertEquals(79.0, $obj["ecommerce"]["shipping"]);
		$this->assertEquals($order_values["order_no"], $obj["ecommerce"]["transaction_id"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["currency"]);
	}

	public function test_datalayer_for_add() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		$expected_product = [
			"catalog_id" => "product-id-001",
			"name" => "Neverending Story, pt.II"
		];
		$products = [new Product($expected_product), new Product(["name" => "b"])];
		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\AddToCart($products[0], ["items" => $products], ["item_converter" => new DummyConverter]));
		$this->_test_basic($instance, ["event" => "add_to_cart", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "add_to_cart", "debug" => !true]);
		$this->_assertJsonIsSameAsArray($instance);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$product_data = $obj["ecommerce"]["items"][0];

		$this->assertEquals("product-id-001", $product_data["item_id"]);
		$this->assertEquals("Neverending Story, pt.II", $product_data["item_name"]);
#		$this->assertArrayNotHasKey("value", $obj["ecommerce"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["currency"]);
	}

	public function test_datalayer_for_begin_checkout() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		$expected_item = [
			"product" => [
				"catalog_id" => "chck-id-002",
				"name" => "Checkout, the beginning",
			],
			"amount" => 3,
		];
		$items = [new BasketItem($expected_item), new BasketItem(["product" => []])];
		$basket = new DummyBasket;
		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\BeginCheckout($basket, ["items" => $items], ["item_converter" => new DummyBasketItemConverter]));
		$this->_test_basic($instance, ["event" => "begin_checkout", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "begin_checkout", "debug" => !true]);
		$this->_assertJsonIsSameAsArray($instance);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$product_data = $obj["ecommerce"]["items"][0];

		$this->assertEquals($expected_item["product"]["catalog_id"], $product_data["item_id"]);
		$this->assertEquals($expected_item["product"]["name"], $product_data["item_name"]);
		$this->assertEquals("Brandy", $product_data["item_brand"]);
		$this->assertEquals(3, $product_data["quantity"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["value"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["currency"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["coupon"]);
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
}


