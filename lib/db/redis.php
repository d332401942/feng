<?php

if (!class_exists('Redis'))
{
	include __DIR__ . '/redisapi.php';
}

class RedisDbLib extends Redis
{
	
	public function __construct($host = null, $port = null)
	{
		if (!$host)
		{
			$host = Config::REDIS_HOST;
		}
		if (!$port)
		{
			$port = Config::REDIS_PORT;
		}
		if (method_exists($this, 'setHost'))
		{
			parent::__construct($host,$port);
		}
		else
		{
			parent::__construct();
			parent::connect($host, $port);
		}
		if (Config::REDIS_PASSWORD)
		{
			$this->auth(Config::REDIS_PASSWORD);
		}
	}

}
