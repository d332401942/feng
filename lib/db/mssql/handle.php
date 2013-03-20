<?php
class HandleMssqlDbLib extends Feng
{
	
	private $dataModel;
	
	private $modelName;
	
	private $tableName;
	
	public function __construct($modelName = null)
	{
		//LogVendorLib::start(__CLASS__, __FUNCTION__);
		if (! $modelName)
		{
			$dataClassName = get_called_class();
			$modelName = preg_replace('/Data$/', 'DataModel', $dataClassName);
		}
		$model = new $modelName();
		$this->dataModel = $model;
		$this->modelName = $modelName;
		$this->tableName = $model->getTableName();
		$this->connect();
		//LogVendorLib::end(__CLASS__, __FUNCTION__);
	}
	
	private function connect()
	{
		$pdo = 
		$conn = mssql_connect(Config::DB_MSSQL_HOST, Config::DB_MSSQL_USERNAME, Config::DB_MSSQL_PASSWORD);
	}
}