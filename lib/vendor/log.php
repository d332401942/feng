<?php

class LogVendorLib extends Feng
{

    const KEY_RUN_INFO = '运行信息';

	const KEY_DB = 'Db';

    const KEY_DB_MYSQL = 'MySQL';
	
	const KEY_DB_SPHINX = 'Sphinx';

    const KEY_DB_TYPE_KEY = 'DBTYPE';

    const KEY_QUERY = 'query';

    const KEY_START_TIME = '开始时间';

    const KEY_END_TIME = '结束时间';

    const KEY_USED_TIME = '耗时';

    const KEY_AUTO_LOAD_FILE = '加载文件';

    /**
     * PHP 系统错误
     */
    const KEY_SYS_ERROR = '系统错误';

    /**
     * 框架警告
     */
    const KEY_FENG_WARNING = '警告';

    const KEY_EXCEPTION = '异常';

    public static $fireDebugInfo = array();

    public static $errorMsg = array();

    private static $runTmp = array();

    private static $dbTmp = array();

    public static function deBug()
    {
        if (Config::FIRE_DEBUG)
        {
        	$logInfo = LogVendorLib::$fireDebugInfo;
        	unset($logInfo[self::KEY_AUTO_LOAD_FILE]);
        	if (isset($logInfo[self::KEY_EXCEPTION][0]))
        	{
        		$e = $logInfo[self::KEY_EXCEPTION][0];
        		$className = get_class($e);
        		$line = $e->getFile() .'---'. $e->getLine();
        		$code = $e->getCode();
        		$msg = $e->getMessage();
        		$logInfo[self::KEY_EXCEPTION] = array(
        						'ClassName' => $className,
        						'Line' => $line,
        						'Code' => $code,
        						'Message' => $msg,
        		);
        	}
            FB::log($logInfo);
        }
		if (Config::LOG_RUN_IS_OPEN)
		{
			self::write();
		}
    }

    public static function setException($e)
    {
        if (empty(self::$fireDebugInfo[self::KEY_EXCEPTION]))
        {
            self::$fireDebugInfo[self::KEY_EXCEPTION] = array();
        }
        array_push(self::$fireDebugInfo[self::KEY_EXCEPTION], $e);
    }

    public static function setSysError($msg)
    {
        if (empty(self::$fireDebugInfo[self::KEY_SYS_ERROR]))
        {
            self::$fireDebugInfo[self::KEY_SYS_ERROR] = array();
        }
        array_push(self::$fireDebugInfo[self::KEY_SYS_ERROR], $msg);
    }

    public static function setWarning($msg)
    {
        if (empty(self::$fireDebugInfo[self::KEY_FENG_WARNING]))
        {
            self::$fireDebugInfo[self::KEY_FENG_WARNING] = array();
        }
        array_push(self::$fireDebugInfo[self::KEY_FENG_WARNING], $msg);
    }

    public static function start($className, $functionName)
    {
        $key = self::getKey($className, $functionName);
        if (empty(self::$runTmp[$key]))
        {
            self::$runTmp[$key] = array();
        }
        array_push(self::$runTmp[$key], microtime(true));
    }

    public static function end($className, $functionName)
    {
        $key = self::getKey($className, $functionName);
        $endTime = microtime(true);
        $startTime = array_pop(self::$runTmp[$key]);
        $runInfo = array(
                $key => array(
                        self::KEY_START_TIME => $startTime,
                        self::KEY_END_TIME => $endTime,
                        self::KEY_USED_TIME => $endTime - $startTime
                )
        );
        if (! isset(self::$fireDebugInfo[self::KEY_RUN_INFO]))
        {
            self::$fireDebugInfo[self::KEY_RUN_INFO] = array();
        }
        array_push(self::$fireDebugInfo[self::KEY_RUN_INFO], $runInfo);
    }

    public static function dbStart($className, $functionName, $query, 
            $dbType = self::KEY_DB_MYSQL)
    {
        $key = self::getKey($className, $functionName);
        if (empty(self::$dbTmp[$key]))
        {
            self::$dbTmp[$key] = array();
        }
        array_push(self::$dbTmp[$key], 
                array(
                        microtime(true),
                        $query,
                        $dbType
                ));
    }

    public static function dbEnd($className, $functionName)
    {
        $key = self::getKey($className, $functionName);
        $endTime = microtime(true);
        $startInfo = array_pop(self::$dbTmp[$key]);
        $startTime = $startInfo[0];
        $query = $startInfo[1];
        $dbType = $startInfo[2];
        $userTime = $endTime - $startTime;
        $info = array(
                $key => array(
                        self::KEY_START_TIME => $startTime,
                        self::KEY_END_TIME => $endTime,
                        self::KEY_USED_TIME => $userTime,
                        self::KEY_QUERY => $query
                )
        );
        if (! isset(self::$fireDebugInfo[self::KEY_DB]))
        {
            self::$fireDebugInfo[self::KEY_DB] = array();
        }
        array_push(self::$fireDebugInfo[self::KEY_DB], $info);
    }

    public static function write()
    {
		$appDir = APP_DIR;
		if (!$appDir)
		{
			$appDir = '.';	
		}
        $dirName = $appDir . '/' . Config::LOG_DOLDER;
        $timeDir = date('Ymd');
        $path = $dirName . '/' . $timeDir;
        CommUtilLib::rMkdir($path);
        self::writeRun($path);
        self::writeSysError($path);
        self::writeWarning($path);
        self::writeException($path);
        self::writeDb($path);
    }

    private static function writeDb($path)
    {
        $file = $path . '/db.log';
        if (! empty(self::$fireDebugInfo[self::KEY_DB]))
        {
            $handle = fopen($file, 'a');
            foreach (self::$fireDebugInfo[self::KEY_DB] as $array)
            {
                $str = '';
                foreach ($array as $key => $arr)
                {
                    $str .= '['.$key . ']';
                    //$str .= '[query::'. (is_array($arr[self::KEY_QUERY]) ? implode(',', $arr[self::KEY_QUERY]) : $arr[self::KEY_QUERY]).']';
					$str .= '[query::' .json_encode($arr[self::KEY_QUERY]). ']';
                    $str .= '[StartTime::' .
                            date('H:m:s', $arr[self::KEY_START_TIME]) .
                            strstr($arr[self::KEY_START_TIME], '.').']';
                    $str .= '[EndTime::' .
                            date('H:m:s', $arr[self::KEY_END_TIME]) .
                            strstr($arr[self::KEY_END_TIME], '.').']';
                    $str .= '[UseTime::' . $arr[self::KEY_USED_TIME].']';
                }
                $str .= "\r\n";
                fwrite($handle, $str);
            }
            fclose($handle);
        }
    }
    
    private static function writeException($path)
    {
        $file = $path . '/exception.log';
        if (! empty(self::$fireDebugInfo[self::KEY_EXCEPTION]))
        {
            foreach (self::$fireDebugInfo[self::KEY_EXCEPTION] as $e)
            {
                $className = get_class($e);
                $str = '【' . $className . '】';
                $str .= 'Message:' . $e->getMessage() . '; ';
                $str .= 'Code:' . $e->getCode() . '; ';
                $str .= 'in ' .$e->getFile() .' as line '.$e->getLine().'; ';
                $str .= "\r\n";
                
                foreach ($e->getTrace() as $trace)
                {
                    $str .= "\t\t";
                    $str .= $trace['class'] .'::'. $trace['function'];
                    $str .= ' in '.$trace['file'] .' as line ' . $trace['line'];
                    $str .= "\r\n";
                }
                $handle = fopen($file, 'a');
                fwrite($handle, $str);
                fclose($handle);
            }
        }
    }

    private static function writeWarning($path)
    {
        $file = $path . '/warning.log';
        if (! empty(self::$fireDebugInfo[self::KEY_FENG_WARNING]))
        {
            $handle = fopen($file, 'a');
            foreach (self::$fireDebugInfo[self::KEY_FENG_WARNING] as $val)
            {
                fwrite($handle, $val . "\r\n");
            }
            fclose($handle);
        }
    }

    private static function writeSysError($path)
    {
        $file = $path . '/syserror.log';
        if (! empty(self::$fireDebugInfo[self::KEY_SYS_ERROR]))
        {
            $handle = fopen($file, 'a');
            foreach (self::$fireDebugInfo[self::KEY_SYS_ERROR] as $val)
            {
                fwrite($handle, $val . "\r\n");
            }
            fclose($handle);
        }
    }

    private static function writeRun($path)
    {
        $runfile = $path . '/run.log';
        $runHandle = fopen($runfile, 'a');
        $runContent = self::getRunContent();
        //$runContent = json_encode(self::$fireDebugInfo) . "\r\n";
        fwrite($runHandle, $runContent);
        fclose($runHandle);
    }

    private static function getRunContent()
    {
        $content = '';
        $runArr = ! empty(self::$fireDebugInfo[self::KEY_RUN_INFO]) ? self::$fireDebugInfo[self::KEY_RUN_INFO] : array();
        foreach ($runArr as $array)
        {
            foreach ($array as $key => $arr)
            {
                $content .= '['.$key . ']';
                $content .= '[StartTime::' .
                         date('H:m:s', $arr[self::KEY_START_TIME]) .
                         strstr($arr[self::KEY_START_TIME], '.').']';
                $content .= '[EndTime::' .
                         date('H:m:s', $arr[self::KEY_END_TIME]) .
                         strstr($arr[self::KEY_END_TIME], '.').']';
                $content .= '[UseTime::' . $arr[self::KEY_USED_TIME].']';
            }
            $content .= "\r\n";
        }
        return $content;
    }

    private static function getKey($className, $functionName)
    {
        return $className . '::' . $functionName;
    }
}
