<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_error_handler('customError');
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
$filePath = dirname(__FILE__);
session_start();
include $filePath . '/feng.php';
include $filePath . '/conf.php';
include $filePath . '/lib/FirePHPCore/fb.php';
$appDir = APP_DIR;
if (!$appDir) 
{
	$appDir = '.';
}
include rtrim($appDir, '/') . '/config.php';

function __autoload($className)
{
    if (class_exists($className) || !class_exists('AutoLoad'))
    {
        return;
    }
    AutoLoad::includeFile($className);
}

function customError($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno))
    {
        // This error code is not included in error_reporting
        return;
    }
    switch ($errno)
    {
        case E_ERROR:
            $errortype = '【MERROR:】';
            break;

        case E_WARNING:
            $errortype = '【WARNING:】';
            break;

        case E_NOTICE:
            $errortype = '【NOTICE:】';
            break;
        default:
            $errortype = '【Unknown error type:】';
            break;
    }
    $errorMsg = $errortype . $errstr . ' in ' . $errfile.' on line ' . $errline;
    echo $errorMsg . '<br>';
    LogVendorLib::setSysError($errorMsg);
}

class AutoLoad extends Feng
{

    public static function strToPath($str)
    {
        $upper = range('A', 'Z');
        $arr = str_split($str);
        $str = null;
        $autoArr = array();
        foreach ($arr as $val)
        {
            if (in_array($val, $upper))
            {
                $autoArr [] = strtolower($str);
                $str = $val;
            }
            else
            {
                $str .= $val;
            }
        }
        $autoArr [] = strtolower($str);
        array_shift($autoArr);
        $autoArr = array_reverse($autoArr);
        return $autoArr;
    }
    
    public static function includeFile($className)
    {
        $autoArr = self::strToPath($className);
        self::includeByArr($autoArr);
    }
    
    private static function includeByArr($autoArr)
    {
        $preName = rtrim(APP_DIR, '/');
        if (current($autoArr) == 'lib')
        {
            $preName = dirname(__FILE__);
        }
        $filePath = '';
        foreach ($autoArr as $val)
        {
            $filePath .= '/' . $val;
        }
        $filePath .= '.php';
        if (file_exists($preName.$filePath))
        {
            $filePath = $preName.$filePath;
        }
        else
        {
            $filePath = './'.$filePath;
        }
        if (file_exists($filePath))
        {
            include $filePath;
            if (!isset(LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE]))
            {
                LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE] = array();
            }
            if (!in_array($filePath, LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE]))
            {
                array_push(LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE], $filePath);
            }
        }
        else
        {
            $lastName = array_pop($autoArr);
            $i = count($autoArr);
            if ($i <= 0)
            {
                return;
            }
            $autoArr[$i - 1] = $lastName . $autoArr[$i - 1];
            self::includeByArr($autoArr);
        }
    }
}

class Main extends Feng
{

    public function run($regulation)
    {
        $urlClass = new UrlCoreLib();
        try
        {
            $urlClass->parseUrl($regulation);
            LogVendorLib::deBug();
        }
        catch (Exception $e)
        {
            LogVendorLib::setException($e);
            LogVendorLib::deBug();
            //header("HTTP/1.0 404 Not Found");
            throw $e;
        }
    }
    
    public function notFound($fileName)
    {
        include $fileName;
    }
}

function M($className)
{
    static $fengMClassNameToModel = array();
    
    if (isset($fengMClassNameToModel[$className]))
    {
        $model = $fengMClassNameToModel[$className];
    }
    else
    {
        $model = new $className();
    }
    return $model;
}

function P()
{
    $args = func_get_args(); // 获取多个参数
    if (count($args) < 1)
    {
        return;
    }

    echo '<div style = "width:100%;text-align:left"><pre>';
    // 多个参数循环输出
    foreach ($args as $arg)
    {
        if (is_array($arg))
        {
            print_r($arg);
            echo '<br>';
        }
        else if (is_string($arg))
        {
            echo $arg . '<br>';
        }
        else
        {
            var_dump($arg);
            echo '<br>';
        }
    }
    echo '</pre></div>';
}
