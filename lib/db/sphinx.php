<?php

class SphinxDbLib extends SphinxClient
{

	public function __construct()
	{
		parent::__construct();
		$this->connect();
	}

	public function getResultIds($result, $pageCore = null)
	{
		$resultIds = array();
		if (!empty($result['matches']))
		{
			$resultIds = array_keys($result['matches']);
			if ($pageCore)
			{
				$pageCore->count = $result['total'];
				$pageCore->pageCount = ceil($pageCore->count / $pageCore->pageSize);
			}
		}
		return $resultIds;
	}

	public function clear()
	{
		$this->resetFilters();
		$this->resetGroupBy();
	}

	private function connect()
	{
		$this->setServer(Config::SPHINX_SERVER, Config::SPHINX_PORT);
	}
}
