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
}
