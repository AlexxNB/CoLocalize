<?php
class Restapi
{
	var $com_level;
	
	public function __construct($com_level=2)
	{
		$this->com_level = $com_level;
	}
	
	public function getGroup()
	{
		return $this->getURL($this->com_level);
	}

	public function getCommand()
	{
		return $this->getURL($this->com_level+1);
	}
	
	public function getParam($param,$safe=true)
	{
		if(!isset($_GET[$param])) return false;
		$txt = trim($_GET[$param]);
		
		if($safe) $param = htmlspecialchars(strip_tags($txt));

		return $txt;
	}

	public function getJSONParam($param)
	{
		if(!isset($_GET[$param])) return false;
		$txt =  json_decode($_GET[$param],1);

		return $txt;
	}
	
	public function makeJSON($array)
	{
		$ar['result'] = $array;
		$ar['status'] = 200;
		
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
		exit();
	}
	
	public function clientError($msg,$data=false)
	{
		$ar['error'] = $msg;
		$ar['status'] = 400;
		if($data) $ar['data'] = $data;
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
		exit();
	}
	
	public function serverError($msg,$data=false)
	{
		$ar['error'] = $msg;
		$ar['status'] = 500;
		if($data) $ar['data'] = $data;
		echo json_encode($ar,JSON_UNESCAPED_UNICODE);
		exit();
	}
	
	private function getURL($part=false)
	{
		$ar = explode('/',$_SERVER["REQUEST_URI"]);
		$url = array();
		
		foreach($ar as $line)
		{
			if(!empty($line) OR $line=='0') $url[] = $line;
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
}


?>
