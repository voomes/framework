<?php 

include("globals/configuration.php");

class engine extends configuration
{
	public $page;
		
	public function construct($page)
	{		
		$this->load();

		$constants = array('online' => 1, 
						   'root' => 1,
						   'publicroot' => 1, 
						   'imgroot' => 1, 
						   'host' => 1,
						   'username' => 1, 
						   'password' => 1, 
						   'database' => 1
						   );

		foreach($this as $k => $v)
		{
			if(!empty($constants[$k]))
			{
				define($k, $v);
			}
		}
		
		define('page',$page);
		$this->page = $page;
	}

	public function get()
	{
		if(file_exists(root.'pages/'.$this->page.'.php'))
		{
			return true;
		}
		else
		{
			$location = publicroot.'error';
			header("Location: $location");
		}
	}

	public function build($page)
	{
		$this->page = $page;
		
		if($this->online == 0)
		{
			$location = publicroot.'offline';
			header("Location: $location");
			exit();
		}
	}
}

class autoload 
{
	public function process($class)
	{	
		if($class == 'applications\stdClass')
		{
			return new stdClass();
		}
		else
		{
			list($type, $class) = explode('\\', $class);
								
			if(file_exists(root.'engine/'.$type.'/'.$class.'.php'))
			{
				include(root.'engine/'.$type.'/'.$class.'.php');
			}
			else
			{
				echo "Class not found: " . $class . " Type: ". $type;
				exit();
			}
		}
	}
}

function __autoload($class) 
{	
	$autoload = new autoload;
	$autoload->process($class);
}

?>