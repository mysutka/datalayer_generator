<?php
/**
 * @runTestsInSeparateProcesses
 */
class CustomBuilderTest extends TestBase {

	public function test_datalayer_for_product_detail_custom_datatype() {
		$instance = DatalayerGenerator\Datalayer::GetInstance();
		DatalayerGenerator\Datatypes\EcDatatype::SetProductClassName(new DatatypeProductBuilder);

		# @todo use own Generator, ProductDetailGenerator returns builtin product array
		$product = [
			"id" => "shorts-01-wi",
			"name" => "Shorts with stripe",
			"brand" => "Brownies",
			"variant" => "Wide",
			"category" => null,
		];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\ProductDetail($product));
		$this->_test_basic($instance, ["activity" => "detail", "event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "detail", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("products", $obj["ecommerce"]["detail"]);
		$this->assertArrayHasKey("actionField", $obj["ecommerce"]["detail"]);
		$this->assertInternalType("array", $obj["ecommerce"]["detail"]["products"]);
		$this->assertInternalType("array", $obj["ecommerce"]["detail"]["actionField"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("brand", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("variant", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayHasKey("price", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayNotHasKey("category", $obj["ecommerce"]["detail"]["products"][0]);
		$this->assertArrayNotHasKey("list", $obj["ecommerce"]["detail"]["products"][0]);

		$product_data = $obj["ecommerce"]["detail"]["products"][0];
		$this->assertEquals("shorts-01-wi", $product_data["id"]);
		$this->assertEquals("Shorts with stripe", $product_data["name"]);
		$this->assertEquals("Brownies", $product_data["brand"]);
		$this->assertEquals("Wide", $product_data["variant"]);
		$this->assertArrayNotHasKey("category", $product_data);
	}

	public function test_datalayer_for_promotion_custom_datatype() {
		$instance = DatalayerGenerator\Datalayer::GetInstance();
		DatalayerGenerator\Datatypes\EcDatatype::SetPromotionClassName("DatatypePromotionBuilder");

		$promotions = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Promotion($promotions));
		$this->_test_basic($instance, ["activity" => "promoView", "event" => "promoView", "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "promoView", "event" => "promoView", "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayHasKey("promotions", $obj["ecommerce"]["promoView"]);
		$this->assertArrayNotHasKey("actionField", $obj["ecommerce"]["promoView"]);
		$this->assertInternalType("array", $obj["ecommerce"]["promoView"]["promotions"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["promoView"]["promotions"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["promoView"]["promotions"][0]);
		$this->assertArrayHasKey("position", $obj["ecommerce"]["promoView"]["promotions"][0]);
		$this->assertArrayHasKey("creative", $obj["ecommerce"]["promoView"]["promotions"][0]);

		$product_data = $obj["ecommerce"]["promoView"]["promotions"][0];
		$this->assertEquals("shorts-2020-brown", $product_data["id"]);
		$this->assertEquals("New Brownies models for 2020", $product_data["name"]);
		$this->assertEquals("Brownies 2020", $product_data["creative"]);
		$this->assertEquals("top-slot", $product_data["position"]);
	}

	public function test_datalayer_for_impression_custom_datatype() {
		$instance = DatalayerGenerator\Datalayer::GetInstance();
		DatalayerGenerator\Datatypes\EcDatatype::SetImpressionClassName("DatatypeImpressionBuilder");

		$promotions = ["a", "b"];
		$instance->measureEcommerceObject(new \DatalayerGenerator\MessageGenerators\Impressions($promotions));
		$this->_test_basic($instance, ["activity" => "impressions", "event" => null, "debug" => !true]);
		$this->_test_basic_json($instance, ["activity" => "impressions", "event" => null, "debug" => !true]);

		$dl = $instance->getDataLayerMessages();
		$dl_json = $instance->getDataLayerMessagesJson();

		$obj = array_shift($dl);
		$obj_json = array_shift($dl_json);

		$this->assertArrayNotHasKey("promotions", $obj["ecommerce"]["impressions"]);
		$this->assertArrayNotHasKey("actionField", $obj["ecommerce"]["impressions"]);
		$this->assertInternalType("array", $obj["ecommerce"]["impressions"]);

		# message returned either as array or as json should contain same data
		$this->assertEquals(sizeof($dl), sizeof($dl_json));
		$this->assertSame($obj, json_decode($obj_json,true));

		# test prvku pole
		$this->assertArrayHasKey("id", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("name", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("brand", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("variant", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("category", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("list", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("position", $obj["ecommerce"]["impressions"][0]);
		$this->assertArrayHasKey("price", $obj["ecommerce"]["impressions"][0]);

		$product_data = $obj["ecommerce"]["impressions"][0];
		$this->assertEquals("shorts-01-wi", $product_data["id"]);
		$this->assertEquals("Shorts with stripe", $product_data["name"]);
		$this->assertEquals("Brownies", $product_data["brand"]);
		$this->assertEquals("Underwear/Striped shorts", $product_data["category"]);
	}
}

class DatatypePromotionBuilder extends \DatalayerGenerator\Datatypes\EcPromotion {

	public function getPromotionId() { return "shorts-2020-brown"; }
	public function getPromotionName() { return "New Brownies models for 2020"; }
	public function getPromotionCreative() { return "Brownies 2020"; }
	public function getPromotionPosition() { return "top-slot"; }
}

class DatatypeProductBuilder extends \DatalayerGenerator\Datatypes\EcProduct {

	public function getProductId(){ return $this->getObject()["id"]; /*"shorts-01-wi";*/ }
	public function getProductName(){ return $this->getObject()["name"]; }
	public function getProductBrand(){ return $this->getObject()["brand"]; }
	public function getProductVariant(){ return $this->getObject()["variant"]; }
	public function getProductCategory(){ return $this->getObject()["category"]; }
}

class DatatypeImpressionBuilder extends \DatalayerGenerator\Datatypes\EcImpression {

	public function getImpressionId(){ return "shorts-01-wi"; }
	public function getImpressionName(){ return "Shorts with stripe"; }
	public function getImpressionBrand(){ return "Brownies"; }
	public function getImpressionVariant(){ return "Wide"; }
	public function getImpressionCategory(){ return "Underwear/Striped shorts"; }
	public function getImpressionList() { return "category"; }
	public function getImpressionPosition() { return 1; }
	public function getImpressionPrice() { return 5.5; }
}
