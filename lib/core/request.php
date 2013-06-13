<?php

class RequestCoreLib extends Feng
{
    
    public function httpRequest($parameters)
    {
        $defFunc = Config::VIEW_FUNC;
        $viewStr = empty($parameters[0]) ? strtolower(Config::VIEW_DOLDER) : strtolower($parameters[0]);
        $paramStr = empty($parameters[1]) ? '' : $parameters[1];
        $params = $this->getParams($paramStr);
        $className = null;
        UrlCoreLib::$viewClass = $this->getViewClass($viewStr, $className);
        $templateFile = self::getTempateFile($className);
        if (!UrlCoreLib::$viewClass->isRender())
        {
        	UrlCoreLib::$viewClass->render ( $templateFile );
        }
        LogVendorLib::start($className, $defFunc);
        UrlCoreLib::$viewClass->$defFunc($params);
        LogVendorLib::end($className, $defFunc);
        if (!UrlCoreLib::$viewClass->isDisplay)
        {
            UrlCoreLib::$viewClass->display();
        }
    }

	public function ajaxRequest($parameters)
	{
		$viewStr = empty($parameters[0]) ? strtolower(Config::VIEW_DOLDER) : strtolower($parameters[0]);
		$arr = explode('/', $viewStr);
		$defFunc = array_pop($arr);
		$viewStr = 'ajax/' . implode('/', $arr);
		$paramStr = empty($parameters[1]) ? '' : $parameters[1];
		$params = $this->getParams($paramStr);
		$className = null;
		UrlCoreLib::$viewClass = $this->getViewClass($viewStr, $className);
		LogVendorLib::start($className, $defFunc);
		try
		{
			UrlCoreLib::$viewClass->$defFunc($params);
		}
		catch (BusinessExceptionLib $e)
		{
			$message = $e->getMessage();
			$code = $e->getCode();
			$this->responseError($message, $code);
		}
		catch(AjaxExceptionLib $e)
		{
			$message = $e->getMessage();
			$code = $e->getCode();
			$this->responseError($message, $code);
		}
		LogVendorLib::end($className, $defFunc);
	}
	
    protected static function getTempateFile($className)
    {
        return rtrim(APP_DIR, '/') . '/' . config::TEMPLATE_DOLDER . UrlCoreLib::getTplFileName($className);
    }


    protected function getViewClass($str, &$className)
    {
        $viewArr = explode('/', $str);
        if (count($viewArr) == 1)
        {
            array_push($viewArr, Config::VIEW_FILE);
        }
        $appDir = APP_DIR;
        if ($appDir == '')
        {
            $appDir = '.';
        }
        $path = rtrim($appDir, '/') . '/' . Config::VIEW_FOLDER;
        $preClassName = ucfirst(array_pop($viewArr));
        $lastClassName = null;
        foreach ($viewArr as $val)
        {
            $path .= '/' . $val;
            $lastClassName = ucfirst($val) . $lastClassName;
        }
        $className = $preClassName . $lastClassName . ucfirst(Config::VIEW_FOLDER);
        $path = $path . '/' . strtolower($preClassName) . '.php';
        if (!file_exists($path))
        {
            LogVendorLib::setWarning($path . '没有找到');
            throw new Exception('not found :: '.$path);
        }
        include_once $path;
        array_push(LogVendorLib::$fireDebugInfo['加载文件'], $path);
        UrlCoreLib::$viewClass = new $className($className);
        return UrlCoreLib::$viewClass;
    }

    protected function getParams($str)
    {
        $params = array();
        $str = trim($str, '/');
        if (!$str)
        {
            return $params;
        }
        $arr = explode('/', $str);
        if (count($arr) % 2 != 0)
        {
            array_push($arr, null);
        }
        $i = 0;
        while ($i < count($arr))
        {
            $params[$arr[$i]] = urldecode($arr[$i + 1]);
            $i += 2;
        }
        return $params;
    }
    
    private function responseError($msg, $code = 0)
    {
    	if (!$msg && !$code)
    	{
    		return;
    	}
    	$array = array(
    					'error' => array(
    									'message' => $msg,
    									'code' => $code
    					)
    	);
    	echo json_encode($array);
    }
}
