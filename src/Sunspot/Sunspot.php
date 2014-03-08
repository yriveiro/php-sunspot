<?php
namespace Sunspot;


use Sunspot\Options;
use Sunspot\Response;
use Guzzle\Http\Client;
use Guzzle\Http\QueryAggregator\DuplicateAggregator;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\CurlException;


class Sunspot
{
	const VERSION = '0.0.1';

	private $http;

	public function __construct(array $cluster = array('localhost:8983'), Options $options = null)
	{
		$this->cluster = $cluster;
		$this->options = (is_null($options)) ? new Options() : $options;

		$this->http = new Client($this->baseUrl());
	}

	/**
	 * Returns the base url to construct the requests.
	 */
	public function baseUrl()
	{
		return sprintf('http://%s',	$this->cluster[array_rand($this->cluster)]);
	}

    /**
     * Pings Solr to know if is alive.
     */
	public function ping()
	{
		$endpoint = sprintf('/%s/admin/ping', $this->options->getContext());

		$query = array(
			'query' => array(
				'wt' => 'json'
			)
		);

		$options = array_merge($this->options->getHttpOptions(), $query);

		$request = $this->http->get($endpoint, array(), $options);

		try {
			$response = $this->http->send($request);
		} catch (ClientErrorResponseException $e) {
			return false;
		} catch (CurlException $e) {
			return false;
		}

		return $response->isSuccessful();
	}

	public function search(array $q, $collection, $resource = 'select')
	{
		$endpoint = sprintf(
			'%s/%s/%s',
			$this->options->getContext(),
			$collection,
			$resource
		);

		$options = array(
			'query' => $q
		);

		$request = $this->http->get($endpoint, array(), $options);
		$request->getQuery()->setAggregator(new DuplicateAggregator());

		try {
			$response = $this->http->send($request);
		} catch (ClientErrorResponseException $e) {
			$httpStatusCode = $e->getResponse()->getStatusCode();
			$error = $e->getMessage();

			return Response::parseFailedRequest($httpStatusCode, $error);
		} catch (CurlException $e) {
			return false;
		}

		return Response::parse($response->getBody());
	}

	public function getSchema($collection, $file = 'schema.xml')
	{
		$endpoint = sprintf(
			'%s/%s/admin/file',
			$this->options->getContext(),
			$collection
		);

		$options = array(
			'query' => array(
				'file' => $file
			)
		);

		$request = $this->http->get($endpoint, array(), $options);

		try {
			$response = $this->http->send($request);
		} catch (ClientErrorResponseException $e) {
			return false;
		} catch (CurlException $e) {
			return false;
		}

		return $response->getBody();
	}
}
