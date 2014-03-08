<?php

namespace tests\Sunspot;


use Sunspot\Sunspot;
use PHPUnit_Framework_TestCase;


class SunspotTest extends PHPUnit_Framework_TestCase
{
	public function testPing()
	{
		$sunspot = new Sunspot();
		$this->assertTrue($sunspot->ping());
	}

	public function testPingFail()
	{
		$sunspot = new Sunspot(array('localhost:8984'));
		$this->assertFalse($sunspot->ping());
	}

	public function testSimpleSearch()
	{
		$sunspot = new Sunspot(array('localhost:8983'));
		$q = array(
			'q' => '*:*',
			'wt' => 'json',
			'rows' => '10',
		);

		$response = $sunspot->search($q, 'collection1');

		$this->assertInstanceOf('Sunspot\Response', $response);
		$this->assertEquals(0, $response->status);
	}

	public function testSimpleSearchNoValidCollection()
	{
		$sunspot = new Sunspot(array('localhost:8983'));
		$q = array(
			'q' => '*:*',
			'wt' => 'json',
			'rows' => '10',
		);

		$response = $sunspot->search($q, 'noValidCollection');

		$this->assertInstanceOf('Sunspot\Response', $response);
		$this->assertEquals(404, $response->httpStatusCode);
		$this->assertEquals(1, $response->status);
	}

	public function testSimpleSearchNoValidHost()
	{
		$sunspot = new Sunspot(array('localhost:8984'));
		$q = array(
			'q' => '*:*',
			'wt' => 'json',
			'rows' => '10',
		);

		$response = $sunspot->search($q, 'Collection1');

		$this->assertFalse($response);
	}

	public function testGetSchema()
	{
		$schema = dirname(__FILE__) . '/../fixtures/schemas/schema-default-4.7.0.xml';
		
		$sunspot = new Sunspot(array('localhost:8983'));
		$response = $sunspot->getSchema('collection1');

		$this->assertStringEqualsFile($schema, $response);
	}

	public function testGetSchemaNotValidCollection()
	{
		$sunspot = new Sunspot(array('localhost:8983'));
		$response = $sunspot->getSchema('Collection1');

		$this->assertFalse($response);
	}

	public function testGetSchemaNotValidHost()
	{
		$sunspot = new Sunspot(array('localhost:8984'));
		$response = $sunspot->getSchema('collection1');

		$this->assertFalse($response);
	}
}
