<?php
namespace Automattic_Unit\Human_Testable\Test_Items;

require_once( dirname( __DIR__ ) . DIRECTORY_SEPARATOR . 'test_class.base-test.php' );
require_once( TESTED_LIBRARY_PATH . DIRECTORY_SEPARATOR . 'test-items' . DIRECTORY_SEPARATOR . 'class.jetpack-test-item.php' );

use Automattic\Human_Testable\Test_Items\Jetpack_Test_Item;
use Automattic_Unit\Human_Testable\Base_Test;

class Test_Jetpack_Test_Item extends Base_Test {
	public function test_get_id() {
		$item = $this->get_jetpack_test_item( array( 'jetpack_test_item_id' => '123' ) );
		$this->assertEquals( $item->get_id(), 123 );
	}

	public function test_get_title() {
		$item = $this->get_jetpack_test_item( array( 'title' => 'Look for Sasquatch' ) );
		$this->assertEquals( $item->get_title(), 'Look for Sasquatch' );
	}

	public function test_get_instructions() {
		$item = $this->get_jetpack_test_item( array( 'instructions' => '1) Check the woods' ) );
		$this->assertEquals( $item->get_instructions(), '1) Check the woods' );
	}

	public function test_get_module() {
		$item = $this->get_jetpack_test_item( array( 'module' => 'woods' ) );
		$this->assertEquals( $item->get_module(), 'woods' );
	}

	public function test_get_importance() {
		$item = $this->get_jetpack_test_item( array( 'importance' => 10 ) );
		$this->assertEquals( $item->get_importance(), 10 );
	}

	public function test_get_host() {
		$item = $this->get_jetpack_test_item( array( 'host' => 'dreamhost' ) );
		$this->assertEquals( $item->get_host(), 'dreamhost' );
	}

	public function test_get_browser() {
		$item = $this->get_jetpack_test_item( array( 'browser' => 'firefox' ) );
		$this->assertEquals( $item->get_browser(), 'firefox' );
	}

	public function test_get_initial_path() {
		$item = $this->get_jetpack_test_item( array( 'initial_path' => '/sasquatch' ) );
		$this->assertEquals( $item->get_initial_path(), '/sasquatch' );
	}

	public function test_did_module_change() {
		$item = $this->get_jetpack_test_item( array( 'importance' => 1, 'module' => 'comments' ) );
		$this->assertTrue( $item->check_environment( $this->fill_environment( array( 'jp_version' => '4.6.0' ) ) ) );
		$this->assertFalse( $item->check_environment( $this->fill_environment( array( 'jp_version' => '4.5.9' ) ) ) );
		$this->assertFalse( $item->check_environment( $this->fill_environment( array( 'jp_version' => '4.5.8' ) ) ) );
	}

	public function test_major_version_medium_importance() {
		$item = $this->get_jetpack_test_item( array( 'importance' => 5, 'module' => 'comments' ) );
		$base_env = array( 'browser' => 'ie', 'host' => 'bluehost', 'wp_version' => '4.7.0', 'php_version' => '7.0.1' );
		$env_set_a = $this->fill_environment( array_merge( $base_env, array( 'jp_version' => '4.7.2' ) ) );
		$env_set_b = $this->fill_environment( array_merge( $base_env, array( 'jp_version' => '4.7.4' ) ) );
		$env_set_c = $this->fill_environment( array_merge( $base_env, array( 'jp_version' => '3.8.5' ) ) );
		$env_a = $env_set_a->get_current_environment();
		$env_b = $env_set_b->get_current_environment();
		$env_c = $env_set_c->get_current_environment();
		$this->assertTrue( $item->check_environment( $env_set_b ) );
		$env_set_b->load_completed_test( 1, $env_c );
		$this->assertTrue( $item->check_environment( $env_set_b ) );
		$env_set_b->load_completed_test( 1, $env_a );
		$this->assertFalse( $item->check_environment( $env_set_b ) );
	}

	protected function fill_environment( $environment, $site_id = 1 ) {
		$data_source = $this->get_test_data_source();
		return $data_source->get_environment_set( $site_id, array_merge(
			array(
				'browser' => null,
				'host' => null,
				'jp_version' => null,
				'wp_version' => null,
				'php_version' => null,
			), $environment
		) );
	}

	protected function get_fake_attributes( $attr = array() ) {
		return array_merge( array(
			'jetpack_test_item_id' => '1',
			'active' => '1',
			'date_added' => '2016-07-19 03:33:10',
			'importance' => '10',
			'title' => 'Do an IE Thing',
			'instructions' => '1) Do something cool.\r\n2) High five your friend.',
			'min_jp_ver' => '',
			'max_jp_ver' => '',
			'min_wp_ver' => '',
			'max_wp_ver' => '',
			'min_php_ver' => '',
			'max_php_ver' => '',
			'module' => 'publicize',
			'host' => '',
			'browser' => '',
			'initial_path' => '/wp-admin/options-general.php?page=sharing',
			'added_by' => 'samhotchkiss',
		), $attr );
	}
	protected function get_jetpack_test_item( $attr = null ) {
		$attr = $this->get_fake_attributes( $attr );
		return new Jetpack_Test_Item( $this->get_test_data_source(), $attr );
	}
}
