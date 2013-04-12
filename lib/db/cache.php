<?php

class CacheDbLib extends Feng
{

	private $redis = null;
	private $memcache = null;
	
	protected function getRedis($host = null, $port = null)
	{
		if (!$this->redis)
		{
			$this->redis = new RedisDbLib($host, $port);
		}
		return $this->redis;
	}

	protected function getMemcache()
	{
		if (!$this->memcache)
		{
			$this->memcache = new Memcache();
			$memServerArr = explode(Config::MEMCACHE_SERVER);
			foreach ($memServerArr as $str)
			{
				$arr = explode(':', $str);
				$this->memcache->addServer($arr[0], $arr[1]);
			}
		}
		return $this->memcache;
	}
}
