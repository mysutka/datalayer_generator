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
			"campaigns" => [
				["free_shipping" => true,],
			],
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

	public function test_datalayer_for_view_promotion_one_banner() {
		$instance = \DatalayerGenerator\Collector::GetInstance();

		$expected_banner = [
			"promotion_name" => "Summer Sale",
			"promotion_id" => "summer_sale",
		];
		$expected_banner_2 = [
			"promotion_name" => "30 percent discount on nuts",
			"promotion_id" => "30_percent_on_nuts",
		];

#		$banners = [new Banner($expected_banner), new Banner($expected_banner_2)];
		$banner = new Banner($expected_banner);

		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\ViewPromotion($banner, [], ["item_converter" => new DummyBannerConverter]));
		$this->_test_basic($instance, ["event" => "view_promotion", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$event = $obj["ecommerce"];
		$items = $obj["ecommerce"]["items"];
		$this->assertEmpty($items);

		$this->assertArrayHasKey("promotion_id", $event);
		$this->assertArrayHasKey("promotion_name", $event);
		$this->assertArrayNotHasKey("creative_name", $event);
		$this->assertArrayNotHasKey("creative_slot", $event);

		$this->assertEquals("summer_sale", $event["promotion_id"]);
		$this->assertEquals("Summer Sale", $event["promotion_name"]);
	}

	public function test_datalayer_for_view_promotion_multiple_banners() {
		$instance = \DatalayerGenerator\Collector::GetInstance();

		$expected_banner = [
			"promotion_name" => "Summer Sale",
			"promotion_id" => "summer_sale",
		];
		$expected_banner_2 = [
			"promotion_name" => "30 percent discount on nuts",
			"promotion_id" => "30_percent_on_nuts",
		];

		$banners = [new Banner($expected_banner), new Banner($expected_banner_2)];
		$banner = new Banner($expected_banner);

		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\ViewPromotion(null, ["items" => $banners]));
		$this->_test_basic($instance, ["event" => "view_promotion", "check_items" => false, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$obj = array_shift($dl);

		$items = $obj["ecommerce"]["items"];
		$this->assertNotEmpty($items);

		$this->assertArrayHasKey("promotion_id", $items[0]);
		$this->assertArrayHasKey("promotion_name", $items[0]);
		$this->assertArrayNotHasKey("creative_name", $items[0]);
		$this->assertArrayNotHasKey("creative_slot", $items[0]);

		$this->assertArrayHasKey("promotion_id", $items[1]);
		$this->assertArrayHasKey("promotion_name", $items[1]);
		$this->assertArrayNotHasKey("creative_name", $items[1]);
		$this->assertArrayNotHasKey("creative_slot", $items[1]);

		$this->assertEquals("summer_sale", $items[0]["promotion_id"]);
		$this->assertEquals("Summer Sale", $items[0]["promotion_name"]);

		$this->assertEquals("30_percent_on_nuts", $items[1]["promotion_id"]);
		$this->assertEquals("30 percent discount on nuts", $items[1]["promotion_name"]);
	}
}


