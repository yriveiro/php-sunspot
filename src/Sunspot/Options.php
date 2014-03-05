<?php
namespace Sunspot;


class Options
{
	private $_properties = array();

	public function __construct(array $options = array())
	{
		$this->parse((empty($options)) ? self::getDefaults() : $options);
	}

	public static function getDefaults()
	{
		$defaults = array(
			'context' => 'solr',
			'timeout' => 60,
			'connectionTimeout' => 2,
			'retries' => 10
		);

		return $defaults;
	}

	public function parse(array $options)
	{
		foreach ($options as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function getHttpOptions()
	{
		return array(
			'timeout' => $this->getTimeout(),
			'connect_timeout' => $this->getConnectionTimeout()
		);
	}

	public function getContext()
	{
		return $this->context;
	}

	public function getTimeout()
	{
		return $this->timeout;
	}

	public function getConnectionTimeout()
	{
		return $this->connectionTimeout;
	}

	public function getRetries()
	{
		return $this->retries;
	}
}