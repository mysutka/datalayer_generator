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

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

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

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertEqualsCanonicalizing($obj, json_decode($obj_json,true));

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

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		$this->assertEqualsCanonicalizing($obj, json_decode($obj_json,true));

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

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		$this->assertEqualsCanonicalizing($obj, json_decode($obj_json,true));

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

class DummyConverter extends DatalayerGenerator\MessageGenerators\GA4\ItemConverter\ItemConverter {
	function getCommonProductAttributes($product) {

		$categories = $this->getCategoryNames($product);
		$card = $product->getCard();
		$brand = $card->getBrand();
		return [
			"item_id" => $product->getCatalogId(),
			"item_name" => $product->getName(),
			"affiliation" => "Dummies e-shop",
			"coupon" => "",
			"discount" => "",
			"index" => 0,
			"item_brand" => (string)$brand,
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

	function getCategoryNames($product) {
		return [
			"Catalog",
			"Books",
			"Human sciences",
			"History",
		];
	}

	function getUnitPrice($product, DatalayerGenerator\MessageGenerators\GA4\EventBase $event_base) {
		return 123.45;
	}

	function getAmount($item, $event) {
		return $event->getAmount($item);
	}
}

class DummyBasketItemConverter extends DummyConverter { }

class DummyOrderItemConverter extends DummyConverter { }

class DummyOrder extends ElementBase {

	function getOrderNo() {
		return $this->values["order_no"];
	}

	function getDeliveryFeeInclVat() {
		return (float)79.0;
	}

	function getItemsPrice($with_vat = true) {
		$out = 9876.54;
		if ($with_vat===false) {
			$out = 9876.54/121*100;
		}
		return $out;
	}

	function getVouchersDiscountAmount() {
		return 500;
	}

	function getCampaignsDiscountAmount() {
		return 1000;
	}
}

class DummyBasket {

	function getDeliveryFeeInclVat() {
		return (float)79.0;
	}

	function getItemsPrice($with_vat = true) {
		$out = 9876.54;
		if ($with_vat===false) {
			$out = 9876.54/121*100;
		}
		return $out;
	}

	function getVouchersDiscountAmount() {
		return 500;
	}

	function getCampaignsDiscountAmount() {
		return 1000;
	}
}

class ElementBase {
	var $values = null;

	function __construct($values=[]) {
		$this->values = $values;
	}
}

class Product extends ElementBase {

	function __construct($values=[]) {
		$values += [
			"catalog_id" => "product-no-1",
			"name" => "dummy name",
			"card" => [
				"brand" => [
					"name" => "Brandy",
				],
			],
		];
		parent::__construct($values);
	}

	function getCatalogId() {
		return $this->values["catalog_id"];
	}
	function getName() {
		return $this->values["name"];
	}
	function getCard() {
		return new Card($this->values["card"]);;
	}
}

class Card extends ElementBase {
	function __construct($values=[]) {
		$values += [
			"name" => "dummy Card name",
		];
		parent::__construct($values);
	}

	function getCategories() {
		$categories = [
			"Catalog",
			"Books",
			"Human sciences",
			"History",
		];
		return $categories;
	}

	function getBrand() {
		return new Brand($this->values["brand"]);;
	}
}

class Brand extends ElementBase {

	function getName() {
		return $this->values["name"];
	}

	function __toString() {
		return (string)$this->getName();
	}
}

class OrderItem extends ElementBase {

	function __construct($values=[]) {
		$values += [
			"amount" => 1,
		];
		parent::__construct($values);
	}

	function getProduct() {
		return new Product($this->values["product"]);;
	}

	function getUnitPriceInclVat() {}
	function getAmount() {
		return $this->values["amount"];
	}
}

class BasketItem extends ElementBase {

	function __construct($values=[]) {
		$values += [
			"amount" => 1,
		];
		parent::__construct($values);
	}

	function getProduct() {
		return new Product($this->values["product"]);
	}
	function getAmount() {
		return $this->values["amount"];
	}
}

