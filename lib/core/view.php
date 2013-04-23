<?php

class ViewCoreLib extends Feng
{

    private $templateFile = null;
    private $compile = null;

    public function __construct()
    {
        $this->compile = new CompileVendorLib();
    }

    public function render($templateFile)
    {
        if ($templateFile)
        {
            $this->templateFile = $templateFile;
        }
    }

    public function assign($varName, $var)
    {
        $this->compile->assign($varName, $var);
    }

    public function cache($functionName, $rely = null, $time = 3600)
    {
        $this->compile->cache($functionName, $rely, $time);
    }

    public function display($templateFile = null)
    {
        UrlCoreLib::$displayEd = true;
        if ($templateFile)
        {
            $this->templateFile = $templateFile;
        }
        $this->compile();
        $this->compile->display();
    }

    public function getHtml($templateFile = null)
    {
        if ($templateFile)
        {
            $this->templateFile = $templateFile;
        }
        $this->compile();
        return $this->compile->getHtml();
    }

    public function responseError($msg = '', $code = 0)
    {
    	if (is_array($msg) || is_object($msg))
    	{
    		$msg = json_encode($msg);
    	}
        throw new ViewExceptionLib($msg, $code);
    }

    public function redirect($url)
    {
        header('Location: ' . $url);exit;
    }
    
    public function closeWindow()
    {
    	throw new OtherExceptionLib(OtherExceptionLib::CLOSE_WINDOW);
    }

    private function compile()
    {
        $this->compile->compile($this->templateFile);
    }

}