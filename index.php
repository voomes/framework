<?php
	
	use drivers as driver;
	
	ob_start();
		
	include("engine/engine.php");
	
	$page = empty($_GET['page']) ? 'index' : $_GET['page'];
	
	$engine = new engine;	
	$engine->construct($page);
						
	if($engine->get() === true)
	{		
		include(root.'pages/'.$engine->page.'.php');
	}
	else
	{
		$location = publicroot.'error';	
		header("Location: $location");	
	}				
				
	$contents = ob_get_contents();
	ob_end_clean();

	echo $contents;
	
?>