<?php
class BasicTest extends PHPUnit\Framework\TestCase {

	public function test_empty_datalayer() {
		$instance = GoogleTagManager::GetInstance();
		$this->assertNotNull($instance);

		$this->assertEmpty($instance->getDataLayerMessages());
		$this->assertEmpty($instance->getDataLayerMessagesJson());
	}

	public function test_datalayer_for_banner_impression() {
		$instance = GoogleTagManager::GetInstance();

		$instance->measurePromotionImpression(new GoogleTagManager\MessageGenerators\ImpressionGenerator(null));
		$this->assertNotEmpty($dl = $instance->getDataLayerMessages());
		$this->assertCount(1, $dl);

		$this->assertInternalType("array", $obj = array_shift($dl));

		error_log(print_r($obj,true));

		# array contains two keys "ecommerce" and "event"
		$this->assertCount(2, array_keys($obj));
		$this->assertSame(["ecommerce","event"], array_keys($obj));

		# messages returned as json
		$this->assertNotEmpty($dl_json = $instance->getDataLayerMessagesJson());
		$this->assertCount(1, $dl_json);
		$this->assertInternalType("string", $element = array_shift($dl_json));
		$this->assertNotNull($obj = json_decode($element,true));
		$this->assertInternalType("array", $obj);
	}
}

