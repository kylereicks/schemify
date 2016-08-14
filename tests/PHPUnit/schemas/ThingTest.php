<?php
/**
 * Tests for the Thing schema.
 *
 * @package Schemify
 */

namespace Schemify\Schemas;

use WP_Mock as M;

use Mockery;
use ReflectionMethod;
use ReflectionProperty;
use Schemify;

class ThingTest extends Schemify\TestCase {

	protected $testFiles = array(
		'schemas/Thing.php',
	);

	public function test__construct() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$post_id  = new ReflectionProperty( $instance, 'postId' );
		$post_id->setAccessible( true );
		$is_main  = new ReflectionProperty( $instance, 'isMain' );
		$is_main->setAccessible( true );

		// Before we've done anything.
		$this->assertEmpty( $post_id->getValue( $instance ) );
		$this->assertEmpty( $is_main->getValue( $instance ) );

		// Construct + test.
		$instance = new Thing( 123, true );

		$this->assertEquals( 123, $post_id->getValue( $instance ) );
		$this->assertTrue( $is_main->getValue( $instance ) );
	}
	public function testGetProperties() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'build' )
			->once()
			->andReturn( $data );

		$this->assertEquals( $data, $instance->getProperties() );
	}

	public function testGetPropertiesCachesResults() {
		$data     = array( 'foo' => 'bar' );
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )
			->shouldAllowMockingProtectedMethods()
			->makePartial();
		$instance->shouldReceive( 'build' )
			->once()
			->andReturn( $data );
		$property = new ReflectionProperty( $instance, 'data' );
		$property->setAccessible( true );

		// Ensure we're starting empty
		$this->assertEmpty( $property->getValue( $instance ) );

		// Execute, verify our $data is now in $this->data.
		$instance->getProperties();

		$this->assertEquals( $data, $property->getValue( $instance ) );
	}

	public function testGetPropertiesCastsOutputAsArray() {
		$data = new \stdClass;
		$data->foo = 'bar';

		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$property = new ReflectionProperty( $instance, 'data' );
		$property->setAccessible( true );
		$property->setValue( $instance, $data );

		$this->assertEquals( array( 'foo' => 'bar' ), $instance->getProperties() );
	}

	public function testGetProp() {
		$this->markTestIncomplete();
	}

	public function testJsonSerialize() {
		$uniqid = uniqid();

		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$instance->shouldReceive( 'getProperties' )
			->once()
			->andReturn( $uniqid );

		$this->assertEquals( $uniqid, $instance->jsonSerialize() );
	}

	public function testGetSchema() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();

		M::wpFunction( 'Schemify\Core\strip_namespace', array(
			'times'  => 1,
			'return' => 'Thing',
		) );

		$this->assertEquals( 'Thing', $instance->getSchema() );
	}

	public function testGetSchemaCachesResult() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );

		M::wpFunction( 'Schemify\Core\strip_namespace', array(
			'return' => 'Thing',
		) );

		$instance->getSchema();

		$this->assertEquals( 'Thing', $property->getValue( $instance ) );
	}

	public function testGetSchemaPullsFromCache() {
		$instance = Mockery::mock( __NAMESPACE__ . '\Thing' )->makePartial();
		$random   = uniqid();
		$property = new ReflectionProperty( $instance, 'schema' );
		$property->setAccessible( true );
		$property->setValue( $instance, $random );


		$this->assertEquals( $random, $instance->getSchema() );
	}

	public function testBuild() {
		$this->markTestIncomplete();
	}

	/**
	 * Verifies that ID's aren't being forced into integers, as that could cause issues with caching.
	 */
	public function testBuildWithHomepage() {
		$this->markTestIncomplete();

		$instance->build( 'home', true );
	}

	public function testGetPropertyList() {
		$this->markTestIncomplete();
	}

	public function testMergeSchemaProperties() {
		$this->markTestIncomplete();
	}

	public function testGetDescription() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_the_excerpt', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'Excerpt',
		) );

		M::wpPassthruFunction( 'esc_html' );

		$this->assertEquals( 'Excerpt', $instance->getDescription( 123 ) );
	}

	public function testGetName() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_the_title', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'Name',
		) );

		$this->assertEquals( 'Name', $instance->getName( 123 ) );
	}

	public function testGetImage() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_post_thumbnail_id', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 124,
		) );

		$this->assertInstanceOf( __NAMESPACE__ . '\ImageObject', $instance->getImage( 123 ) );
	}

	public function testGetImageIsNullWhenNotMainSchema() {
		$instance = new Thing( 123, false );

		M::wpFunction( 'get_post_thumbnail_id', array(
			'times'  => 0,
		) );

		$this->assertNull( $instance->getImage( 123 ) );
	}

	public function testGetUrl() {
		$instance = new Thing( 123, true );

		M::wpFunction( 'get_permalink', array(
			'times'  => 1,
			'args'   => array( 123 ),
			'return' => 'http://example.com',
		) );

		$this->assertEquals( 'http://example.com', $instance->getUrl( 123 ) );
	}
}
