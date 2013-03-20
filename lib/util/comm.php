<?php

class CommUtilLib extends Feng
{
    public static function trimArr($arr)
    {
        $newArr = array();
        foreach($arr as $key => $val)
        {
            if (is_string($val))
            {
                $val = trim($val);
            }
            $newArr[$key] = $val;
        }
        return $newArr;
    }
    
    public static function setCookie($name, $value, $expire = 0, $path = '/')
    {
        setcookie($name,$value,$expire,$path);
        $_COOKIE[$name] = $value;
    }
    
    public static function rMkdir($path)
    {
        $path = $path .'/1';
        $arr = self::getDirTree($path);
        $arr = array_reverse($arr);
        foreach ($arr as $dir)
        {
            if (!file_exists($dir))
            {
                mkdir($dir);
                //chmod($dir, 0777);
            }
        }
    }
    
    private static function getDirTree($dir, $dirArr = array())
    {
        $path = dirname($dir);
        array_push($dirArr, $path);
        if ($path == '.' || $path == '\\' || $path == '/')
        {
            return $dirArr;
        }
    
        return self::getDirTree($path, $dirArr);
    }
}
