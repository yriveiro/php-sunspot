<?php

namespace Sunspot;


use \SplFixedArray;
use \UnexpectedValueException;
use \Sunspot\JSONResponse;


class Response
{
	public $httpStatusCode = 200;
	public $errorMessage = null;
	public $status = 1;
	public $QTime = 0;
	public $numFound = 0;
	public $start = 0;
	public $docs = null;
	private $facetQueries = null;
	private $facetFields = null;
	private $facetDates = null;
	private $facetRanges = null;

	public function __construct()
	{
		// Nothing to do here.
	}

	public static function parseFailedRequest($httpStatusCode, $error)
	{
		$response = new Response();

		$response->httpStatusCode = $httpStatusCode;
		$response->errorMessage = $error;

		return $response;
	}

	public static function parse($response, JSONResponse $parser = null)
	{
		$res = new Response();

		if (empty($response) || $response === false) {
			return $res;
		}

		$response = json_decode($response);

		if (is_null($response)) {
			throw new UnexpectedValueException(
				sprintf('Json decode error: %s', json_last_error_msg())
			);
		}

		if (isset($response->responseHeader)) {
			$res->parseResponseHeader($response->responseHeader);
		}

		if (isset($response->response)) {
			$res->parseResponse($response->response);
		}

		if (isset($response->facet_counts)) {
			$res->parseFacetCounts($response->facet_counts);
		}

		return $res;
	}

	private function parseResponseHeader($headerResponse)
	{
		$this->status = $headerResponse->status;
		$this->QTime = $headerResponse->QTime;
	}

	private function parseResponse($response)
	{
		$this->numFound = $response->numFound;
		$this->start = $response->start;
		$this->docs = SplFixedArray::fromArray($response->docs);
	}

	private function parseFacetCounts($facetCounts)
	{
		$this->parseFacetQueries($facetCounts->facet_queries);
		$this->parseFacetFields($facetCounts->facet_fields);
		$this->parseFacetDates($facetCounts->facet_dates);
		$this->parseFacetRanges($facetCounts->facet_ranges);
	}

	private function parseFacetQueries($facetQueriesData)
	{
		$this->parseFacetElement('facetQueries', $facetQueriesData);
	}

	private function parseFacetFields($facetFieldsData)
	{
		$this->parseFacetElement('facetFields', $facetFieldsData);
	}

	private function parseFacetDates($facetDatesData)
	{
		$this->parseFacetElement('facetDates', $facetDatesData);
	}

	private function parseFacetRanges($facetRangesData)
	{
		$this->parseFacetElement('facetRanges', $facetRangesData);
	}

	private function parseFacetElement($element, $data)
	{
		$collector = array();

		foreach ($data as $key => $values) {
			$collector[$key] = SplFixedArray::fromArray($values);
		}

		$this->{$element} = $collector;
	}

	public function getFacetField($field)
	{
		if (array_key_exists($field, $this->facetFields)) {
			return $this->facetFields[$field];
		}

		return false;
	}
}
