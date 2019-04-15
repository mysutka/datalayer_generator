<?php
class BasicTest extends PHPUnit\Framework\TestCase {
	public function testBasic() {
		$instance = GoogleTagManager::GetInstance();
		$this->assertNotNull($instance);
		$this->assertTrue( false );
	}
}
