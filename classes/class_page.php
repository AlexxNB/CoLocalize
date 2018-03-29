<?php
require_once("class_utils.php");
require_once("class_language.php");

class Page {
    private $_ver;
    private $_tplvars;
    private $_vars;
    private $_rawvars;
    private $_javascript;
    private $_jslink;
    private $_csslink;
    private $_pageview;
    public $L;

    public function __construct(){
        $this->_ver = 1;

        $utils = new Utils();
        $lang = new Language();

        if($utils->isGlobal('lang'))
            $l = $utils->getGlobal('lang');
        else    
            $l = 'en';

        $this->L = $lang->GetLangVars($l);

        
        $this->_pageview = $this->View('page');
        foreach($_GET as $key=>$value)
        {
             $this->_vars[$key]=$value;
        }
        
        foreach($_POST as $key=>$value)
        {
             $this->_vars[$key]=$value;
             $this->_clearVar($key);
        }

        $this->_preProc();

        
    }

    public function View($tpl){
        $v = new View($tpl);
        $v->L = $this->L;
        return $v;
    }

    public function __set($key, $value)
    {
        $this->_tplvars[$key]=$value;
    }

    public function MakePage(){
        $auth = new Auth();

        $this->_pageview->Title = $this->Title;
        $this->_pageview->Content = $this->Content;

        if(count($this->_javascript)>0) 
            $this->_pageview->JSCode = $this->_makeJavascript();
        if(count($this->_csslink)>0) 
            $this->_pageview->CSSLinks = $this->_makeCSSLink();
        if(count($this->_jslink)>0) 
            $this->_pageview->JSLinks = $this->_makeJSLink();
        
        if(count($this->_tplvars)>0) 
            foreach($this->_tplvars as $key=>$value){
                $this->_pageview->{$key} = $value;
            }

        $this->_pageview->Render();
        exit();
    }

    private function _preProc(){
        require_once(__DIR__.'/../_preproc.php');
        $list = preProc();
        foreach($list['vars'] as $name=>$content){
            $this->_tplvars[$name] = $content;
        }

        foreach($list['js'] as $content){
            $this->AddJSLink($content);
        }

        foreach($list['css'] as $content){
            $this->AddCSSLink($content);
        }
    }

    public function AddJavascript($code){
        $code = preg_replace('/<script type=.*>|<\/script>/i','',$code);
        $this->_javascript[]=$code;
    }

    private function _makeJavascript(){
        return '<script language="JavaScript" type="text/javascript">'."\r\n".implode("\r\n",$this->_javascript)."\r\n".'</script>';
    }

    public function AddCSSLink($path){
        $path .= '?v='.$this->_ver;
        $this->_csslink[]= '<link rel="stylesheet" href="'.$path.'" type="text/css" />';
    }

    private function _makeCSSLink(){
        return "\r\n\r\n".implode("\r\n",$this->_csslink)."\r\n";
    }

    public function AddJSLink($path){
        $path .= '?v='.$this->_ver;
        $this->_jslink[]= '<script language="JavaScript" src="'.$path.'" type="text/javascript"></script>';
    }

    public function _makeJSLink()
    {
        return "\r\n\r\n".implode("\r\n",$this->_jslink)."\r\n";
    }

    public function GetURL($part=false){
        $u =  preg_replace('/\?(.*)$/i', '', $_SERVER["REQUEST_URI"]);
        $ar = explode('/',$u);
        $url = array();
        foreach($ar as $line)
        {
            if(!empty($line)) $url[] = $line;
        }

        if($part)
        {
            if(!isset($url[$part-1]))
                return false;
            else
                return $url[$part-1];
        }
        else
            return $url;
    }

    public function Show404(){
        require_once(__DIR__.'/../404.php');
        exit;
    }

    private function _clearVar($var){
        $this->_rawvars[$key]=$value;

        if(!is_array($this->_vars[$var]))
        $this->_vars[$var] = htmlspecialchars($this->_vars[$var]);

        return $this->_vars[$var];
    }

    public function IsVar($name){
        return isset($this->_vars[$name]);
    }

    public function IsEmpty($var){
        return preg_match('|^\s*$|siu',$this->_vars[$var]);
    }

    public function Var($name){
        return $this->_vars[$name];
    }

    public function Location($url=false)
    {
        if(!$url) $url = $_SERVER["REQUEST_URI"];
        header("LOCATION:$url");
        exit;
    }
}




class View{
    private $_view;
    private $_vars;

    public function __construct($view){
        $this->_view = $view;
    }

    public function __set($key, $value)
    {
            $this->_vars[$key]=$value;
    }

    public function  HTML(){
        if(count($this->_vars) > 0)
            foreach($this->_vars as $key=>$value){
                $$key = $value;
            }
        ob_start();
        include __DIR__.'/../res/views/'.$this->_view.'.tpl.php';
        return ob_get_clean();
    }

    public function Render(){
        echo $this->HTML();
    }    
}
?>