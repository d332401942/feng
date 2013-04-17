<?php
include __DIR__ . '/sphinxapi.php';
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
				$pageCore->count = $result['total_found'];
				$pageCore->pageCount = ceil($pageCore->count / $pageCore->pageSize);
			}
		}
		return $resultIds;
	}
	
	public function buildExcerpts($docs, $index, $words, $opts = array())
	{
		$array = array(
			'docs' => $docs,
			'index' => $index,
			'words' => $words,
			'opts' => $opts,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::buildExcerpts($docs, $index, $words, $opts);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function setSortMode($mode = SPH_SORT_EXTENDED, $sortby = null)
	{
		$array = array(
			'mode' => $mode,
			'sortby' => $sortby,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::setSortMode($mode, $sortby);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function setFieldWeights($weights)
	{
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $weights,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::setFieldWeights($weights);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function query($keyword, $index = '*', $comment = '')
	{
		$array = array(
			'keyword' => $keyword,
			'index' => $index,
			'comment' => $comment,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::query($keyword, $index, $comment);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function setFilter($attribute, $values, $exclude = false)
	{
		$array = array(
			'attribute' => $attribute,
			'values' => $values,
			'exclude' => $exclude,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::setFilter($attribute, $values, $exclude);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function setFilterRange($attribute, $min, $max, $exclude = false)
	{
		$array = array(
			'attribute' => $attribute,
			'min' => $min,
			'max' => $max,
			'exclude' => $exclude,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::setFilterRange($attribute, $min, $max, $exclude);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function setFilterFloatRange($attribute, $min, $max, $exclude = false)
	{
		$array = array(
			'attribute' => $attribute,
			'min' => $min,
			'max' => $max,
			'exclude' => $exclude,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::setFilterFloatRange($attribute, $min, $max, $exclude);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
	}
	
	public function setGroupBy($attribute, $func = SPH_GROUPBY_ATTR, $groupsort = '@group desc')
	{
		$array = array(
			'attribute' => $attribute,
			'func' => $func,
			'groupsort' => $groupsort,
		);
		LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array,LogVendorLib::KEY_DB_SPHINX);
		$result = parent::setGroupBy($attribute, $func, $groupsort);
		LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
		return $result;
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
