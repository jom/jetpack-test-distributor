<?php
namespace Automattic_Unit\Human_Testable_Helpers\Fakes;

require_once( TESTED_LIBRARY_PATH . DIRECTORY_SEPARATOR . 'data-sources' . DIRECTORY_SEPARATOR . 'class.data-source.php' );
require_once( TESTED_LIBRARY_PATH . DIRECTORY_SEPARATOR . 'test-items' . DIRECTORY_SEPARATOR . 'class.jetpack-test-item.php' );

use Automattic\Human_Testable\Test_Items\Jetpack_Test_Item;
use Automattic\Human_Testable\Data_Sources\Data_Source;

define( 'FAKE_DATA_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'data' );

/**
 * Test data source
 */
class Test_Data_Source extends Data_Source {
	private $memory_tables = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		$tables = array( 'jetpack_test_items_completed', 'jetpack_test_items', 'jetpack_versions' );
		foreach ( $tables as $table ) {
			$this->memory_tables[ $table ] = array();
			$data_path = FAKE_DATA_DIR . DIRECTORY_SEPARATOR . $table . '.json';
			if ( file_exists( $data_path ) ) {
				$this->memory_tables[ $table ] = json_decode( file_get_contents( $data_path ), true );
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_environment_attributes() {
		return array(
			'browser',
			'host',
			'jp_version',
			'wp_version',
			'php_version',
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_tests() {
		$tests = [];
		foreach ( $this->memory_tables['jetpack_test_items'] as $row ) {
			$test_item = $this->prepare( $row );
			$tests[ $test_item->get_id() ] = $test_item;
		}
		return $tests;
	}

	public function get_version_modules() {
		$versions = array();
		foreach ( $this->memory_tables['jetpack_versions'] as $row ) {
			$versions[ $row['version'] ] = json_decode( $row['touched_modules'], true );
		}
		return $versions;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_completed_tests( $site_id, $environment_hash ) {
		$tests = [];
		$site_id = (int) $site_id;
		foreach ( $this->memory_tables['jetpack_test_items_completed'] as $item ) {
			if ( $site_id === $item['site_id'] && $environment_hash === $item['environment'] ) {
				$tests[] = $item['jetpack_test_item_id'];
			}
		}
		return $tests;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_completed_test( $site_id, $test_id, $environment_hash ) {
		$this->memory_tables['jetpack_test_items_completed'][] = array(
			'site_id' => (int)$site_id,
			'jetpack_test_item_id' => (int)$test_id,
			'environment' => $environment_hash
		);
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_test_item_class() {
		return Jetpack_Test_Item::class;
	}
}
