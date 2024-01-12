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

		# @todo use own Generator, ProductDetailGenerator returns builtin product array
		$products = [new Product("a"), new Product("b")];
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

		$this->assertIsArray($obj["ecommerce"]["items"]);

		# test prvku pole
		$this->assertArrayHasKey("item_id", $obj["ecommerce"]["items"][0]);
		$this->assertArrayHasKey("item_name", $obj["ecommerce"]["items"][0]);

		$product_data = $obj["ecommerce"]["items"][0];
		$this->assertEquals("catalog_id", $product_data["item_id"]);
		$this->assertEquals("dummy name", $product_data["item_name"]);
		$this->assertEquals("Dummy brand", $product_data["item_brand"]);
		$this->assertEquals("Catalog", $product_data["item_category"]);
		$this->assertEquals("Books", $product_data["item_category2"]);
		$this->assertEquals("Human sciences", $product_data["item_category3"]);
		$this->assertEquals("History", $product_data["item_category4"]);
		$this->assertArrayNotHasKey("item_category5", $product_data);
		$this->assertEquals(1, $product_data["quantity"]);
	}

	public function test_datalayer_for_purchase() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		$order = new DummyOrder;
		$products = [new OrderItem("a"), new OrderItem("b")];
		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\Purchase($order, ["items" => $products], ["item_converter" => new DummyOrderItemConverter]));
		$this->_test_basic($instance, ["event" => "purchase", "debug" => !true]);
		$this->_test_basic_json($instance, ["event" => "purchase", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();
		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertEqualsCanonicalizing($obj, json_decode($obj_json,true));

		$this->assertIsArray($obj["ecommerce"]["items"]);

		# test prvku pole
		$this->assertArrayHasKey("item_id", $obj["ecommerce"]["items"][0]);
		$this->assertArrayHasKey("item_name", $obj["ecommerce"]["items"][0]);

		$product_data = $obj["ecommerce"]["items"][0];
		$this->assertEquals("catalog_id", $product_data["item_id"]);
		$this->assertEquals("dummy name", $product_data["item_name"]);
		$this->assertEquals("Dummy brand", $product_data["item_brand"]);
		$this->assertEquals("Catalog", $product_data["item_category"]);
		$this->assertEquals("Books", $product_data["item_category2"]);
		$this->assertEquals("Human sciences", $product_data["item_category3"]);
		$this->assertEquals("History", $product_data["item_category4"]);
		$this->assertArrayNotHasKey("item_category5", $product_data);

		# purchase specific attributes
		$this->assertEquals(11376.54, $obj["ecommerce"]["value"]);
		$this->assertEquals(1714.11, $obj["ecommerce"]["tax"]);
		$this->assertEquals(79.0, $obj["ecommerce"]["shipping"]);
		$this->assertEquals("dummy-T_12345", $obj["ecommerce"]["transaction_id"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["currency"]);
	}

	public function test_datalayer_for_add() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$products = [new Product("a"), new Product("b")];
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

		$this->assertIsArray($obj["ecommerce"]["items"]);

		# test prvku pole
		$this->assertArrayHasKey("item_id", $obj["ecommerce"]["items"][0]);
		$this->assertArrayHasKey("item_name", $obj["ecommerce"]["items"][0]);

#		$this->assertArrayNotHasKey("value", $obj["ecommerce"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["currency"]);
	}

	public function test_datalayer_for_begin_checkout() {
		$instance = DatalayerGenerator\Collector::GetInstance();

		# @todo use own Generator, ImpressionGenerator returns builtin product array
		$products = [new BasketItem("a"), new BasketItem("b")];
		$basket = new DummyBasket;
		$instance->push(new \DatalayerGenerator\MessageGenerators\GA4\BeginCheckout($basket, ["items" => $products], ["item_converter" => new DummyBasketItemConverter]));
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

		$this->assertIsArray($obj["ecommerce"]["items"]);

		# test prvku pole
		$this->assertArrayHasKey("item_id", $obj["ecommerce"]["items"][0]);
		$this->assertArrayHasKey("item_name", $obj["ecommerce"]["items"][0]);

		$product_data = $obj["ecommerce"]["items"][0];
		$this->assertEquals(3, $product_data["quantity"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["value"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["currency"]);
#		$this->assertEquals("CZK", $obj["ecommerce"]["coupon"]);
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

class DummyConverter {
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

	function getCategoryNames($product) {
	}

	function getUnitPrice($product, DatalayerGenerator\MessageGenerators\GA4\EventBase $event_base) {
		return 123.45;
	}

	function getAmount($item) {
		return 2;
	}

	function toArray($item) {
		return $this->getCommonProductAttributes($item);
	}
}

class DummyBasketItemConverter extends DummyConverter {


	function getAmount($item) {
		return 3;
	}

	function toArray($item) {
		$out = parent::toArray($item);
		$out["quantity"] = $this->getAmount($item);
		return $out;
	}
}

class DummyOrderItemConverter extends DummyConverter {
}

class DummyOrder {

	function getOrderNo() {
		return "dummy-T_12345";
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

	function getOrderNo() {
		return "dummy-T_12345";
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

class Brand {
	function getName() {
		return "Dummy brand";
	}

	function __toString() {
		return $this->getName();
	}
}
class Card {

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
		return new Brand;
	}
}

class Product {
	function getCatalogId() {
		return "catalog_id";
	}
	function getName() {
		return "dummy name";
	}
	function getCard() {
		return new Card;
	}
}

class OrderItem {

	function getProduct() {
		return new Product;
	}

	function getUnitPriceInclVat() {}
	function getAmount() {
	}
}

class BasketItem {

	function getProduct() {
		return new Product;
	}
}

