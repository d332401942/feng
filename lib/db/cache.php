<?php

class CacheDbLib extends Feng
{

	private $redis = null;
	
	protected function getRedis($host = null, $port = null)
	{
		if (!$this->redis)
		{
			$this->redis = new RedisDbLib($host, $port);
		}
		return $this->redis;
	}
}
