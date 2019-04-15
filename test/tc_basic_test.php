<?php
class TcBasicTest extends \PHPUnit_Framework_TestCase {
	function test_basic() {
		$instance = GoogleTagManager::GetInstance();
		$this->assertNotNull($instance);
	}
}
