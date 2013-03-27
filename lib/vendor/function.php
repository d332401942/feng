<?php

class FunctionVendorLib extends Feng
{

    public function cut($string, $parame)
    {
        return '：-->' . $string . '-----我的位置在' . __FILE__ . '--' . __CLASS__ . '--' . __FUNCTION__ . '--参数' . $parame;
    }

    public function truncate($string, $sublen = 80, $etc = '...', $break_words = false, $middle = false)
    {
        $start = 0;
        $code = "UTF-8";
        if ($code == 'UTF-8')
        {
            //如果有中文则减去中文的个数
            $cncount = $this->cncount($string);

            if ($cncount > ($sublen / 2))
            {
                $sublen = ceil($sublen / 2);
            }
            else
            {
                $sublen = $sublen - $cncount;
            }

            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            $t_string = array();
            preg_match_all($pa, $string, $t_string);
			
			$str = join('', array_slice($t_string[0], $start, $sublen));
			$str = rtrim($str, '.');
            if (count($t_string[0]) - $start > $sublen)
			{
                return $str . $etc;
			}
			else
			{
				return $str;
			}
        } 
        else
        {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';

            for ($i = 0; $i < $strlen; $i++)
            {
                if ($i >= $start && $i < ($start + $sublen))
                {
                    if (ord(substr($string, $i, 1)) > 129)
                    {
                        $tmpstr.= substr($string, $i, 2);
                    }
                    else
                    {
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if (ord(substr($string, $i, 1)) > 129)
                {
                    $i++;
                }
            }
            if (strlen($tmpstr) < $strlen)
            {
                $tmpstr.= $etc;
            }
            return $tmpstr;
        }
    }

    public function date_format($stamp, $format)
    {
        return date($format,$stamp);
    }
    
    public function parsePicpath($path)
    {
        if (!preg_match('/^http:\/\//',$path))
        {
            $path = '/'.$path;
        }
        return $path;
    }
    
    public function numerRound($numer,$n)
    {
    	return round($numer,$n);
    }
    
    public function screenUrl($url)
    {
    	if (strlen($url) < 4)
    	{
    		return $url;
    	}
    	$num = strrchr($url,'http');
    	if (!$num)
    	{
    		return $url;
    	}
    	$arr = explode('http', $url);
    	$url = 'http' . array_pop($arr);
    	$url = urldecode($url);
    	
    	return $url;
    }

	public function diffNow($time)
	{
		return CommUtilLib::diffNow($time);
	}
    
    private function cncount($str)
    {
        $len = strlen($str);
        $cncount = 0;

        for ($i = 0; $i < $len; $i++)
        {
            $temp_str = substr($str, $i, 1);

            if (ord($temp_str) > 127)
            {
                $cncount++;
            }
        }
        return ceil($cncount / 3);
    }

}
