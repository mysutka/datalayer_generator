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
		$this->assertEquals(8376.54, $obj["ecommerce"]["value"]);
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

	public function test_datalayer_for_view_item_list() {
		$instance = \DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionsGenerator returns builtin product array
		$products = [
			new Card([
				"products" => [
					[
						"catalog_id" => "catalog_id_01",
						"name" => "Neverending Story",
					],
					[
						"catalog_id" => "catalog_id_02",
						"name" => "Neverending Story, pt.II"
					],
					[
						"catalog_id" => "catalog_id_03",
						"name" => "Unendliche Geschichte",
					],
				],
				"brand" => [
					"name" => "Odeon",
				],
			]),
			new Card([
				"products" => [
					[
						"catalog_id" => "alb_id_01",
						"name" => "Honzikova cesta",
					],
				],
				"brand" => [
					"name" => "Albatros",
				],
			]),
		];
		$instance->push(new DatalayerGenerator\MessageGenerators\GA4\ViewItemList(null, ["items" => $products], ["item_converter" => new DummyConverter]));
		$this->_test_basic($instance, ["event" => "view_item_list"]);
		$this->_test_basic_json($instance, ["event" => "view_item_list"]);
		$this->_assertJsonIsSameAsArray($instance);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$product_data = $obj["ecommerce"]["items"][0];

		$this->assertEquals("Neverending Story", $product_data["item_name"]);
		$expected = [
			"catalog_ids" => [ "catalog_id_01", "catalog_id_02", "catalog_id_03", "alb_id_01", ],
			"brands" => ["Odeon","Odeon",  "Odeon", "Albatros",],
		];

		foreach($obj["ecommerce"]["items"] as $idx => $item) {
			$this->assertEquals($expected["catalog_ids"][$idx], $item["item_id"]);
			$this->assertEquals($expected["brands"][$idx], $item["item_brand"]);
			$this->assertEquals(1, $item["quantity"]);
			$this->assertEquals("Dummies e-shop", $item["affiliation"]);
		}
	}

	public function test_datalayer_for_banner_promotions() {
		$instance = \DatalayerGenerator\Collector::GetInstance();

		$expected_product = [
			"catalog_id" => "product-id-001",
			"name" => "Neverending Story, pt.II"
		];
		$expected_banner = [
			"promotion_name" => "Summer Sale",
			"promotion_id" => "summer_sale",
		];
		$banner = new Banner($expected_banner);
		$products = [new Product($expected_product), new Product(["name" => "b"])];

		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\ViewPromotion($banner, ["items" => $products], ["item_converter" => new DummyConverter]));
		$this->_test_basic($instance, ["event" => "view_promotion", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$this->assertNotEmpty($obj["ecommerce"]["items"]);

		$this->assertArrayHasKey("promotion_id", $obj["ecommerce"]);
		$this->assertArrayHasKey("promotion_name", $obj["ecommerce"]);
		$this->assertArrayNotHasKey("creative_name", $obj["ecommerce"]);
		$this->assertArrayNotHasKey("creative_slot", $obj["ecommerce"]);

		$this->assertEquals("summer_sale", $obj["ecommerce"]["promotion_id"]);
		$this->assertEquals("Summer Sale", $obj["ecommerce"]["promotion_name"]);
	}
}


