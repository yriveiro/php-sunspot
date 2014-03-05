<?php
namespace Sunspot;


class Facet
{
	private $facetQueries = null;
	private $facetFields = null;
	private $facetDates = null;
	private $facetRanges = null;

	public function __construct()
	{
		// Nothing to do here.
	}

	public static function parse($facet)
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